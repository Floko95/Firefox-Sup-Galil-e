<?php
# On redirige le visiteur s'il n'a rien à faire sur cette page
session_start();
if (!isset($_SESSION['id'])) {
	header ('Location: ../index.php');
	exit();
}
else {
	require_once '../inc/serveur.php';
	$req = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants NATURAL JOIN attributionDroitsAuxRoles WHERE id = ?');
	$req->execute(array($_SESSION['id']));
	$data = $req->fetch();
	if ($data[0] == 0) {
		header ('Location: ../index.php');
		exit();
	}
}
require_once '../inc/fonctions.php';
?>

<?php
# On récupère les différents rôles pour les afficher
$req = $bdd->prepare('SELECT * FROM ROLES'); // tous ceux qui ont un droit peuvent tout voir ? ORDER BY nb droits ?
$req->execute();
$roles = $req->fetchAll();
$nbRoles = count($roles);
?>

<?php
# On récupère les différents droits
$req = $bdd->prepare('SELECT * FROM DROITS');
$req->execute();
$droits = $req->fetchAll();
$nbDroits = count($droits);
# Requête pour savoir si l'étudiant possède le droit i
$reqDroit = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants NATURAL JOIN attributionDroitsAuxRoles WHERE id = ? AND idDroits = ?');
?>

<?php
$errors = array();
$success = array();
?>

<?php
# Création d'un rôle
if (isset($_POST['creationRole']) && $_POST['creationRole'] == 'Valider') {
	# Si l'étudiant n'est pas autorisé à créer un rôle
	$reqDroit->execute(array($_SESSION['id'], 1));
	$data = $reqDroit->fetch();
	if ($data[0] == 0) {
		$errors['droitCréationManquant'] = "Vous n'êtes pas autorisé à créer un rôle";
	}
	
	# Si le nom du rôle est absent, déjà utilisé ou trop long
	if (empty($_POST['nomRole'])) {
		$errors['nomRole'] = "Nom de rôle manquant";
	} elseif (strlen($_POST['nomRole']) > 30) {
		$errors['nomRole'] = "Le nom du rôle doit faire au plus 30 caractères";
	} else {
		$req = $bdd->prepare('SELECT COUNT(*) FROM ROLES WHERE role = ?');
		$req->execute(array($_POST['nomRole']));
		$existeDeja = $req->fetch();
		if ($existeDeja[0] != 0) {
			$errors['nomRole'] = "Nom de rôle déjà utilisé";
		}
	}
	
	# Si la description est trop longue
	if (!empty($_POST['descriptionRole']) && strlen($_POST['descriptionRole']) > 255) {
		$errors['descriptionRole'] = "La description du rôle doit faire au plus 255 caractères";
	}
	
	# Si l'étudiant crée un rôle et lui attribue un droit qu'il ne possède pas
	for ($i=1; $i <= $nbDroits; $i++) {
		if (isset($_POST['droit'.$i])){
			$reqDroit->execute(array($_SESSION['id'], $i));
			$data = $reqDroit->fetch();
			if ($data[0] == 0) {
				$errors['droitManquant'] = "Vous avez coch� un droit que vous ne poss�dez pas";
				break;
			}
		}
	}
	
	# S'il n'y a pas d'erreur, on crée le rôle et on lui associe les droits
	if (empty($errors)) {
		$req = $bdd->prepare('INSERT INTO ROLES(role, descriptionRole, supprimable) VALUES(:role, :descriptionRole, 1)');
		$req->execute(array(
			'role' => $_POST['nomRole'],
			'descriptionRole' => $_POST['descriptionRole']));
			
		$req = $bdd->prepare('SELECT * FROM ROLES WHERE role = ?');
		$req->execute(array($_POST['nomRole']));
		$idRoles = $req->fetch();
		for ($i=1; $i <= $nbDroits; $i++){
			if (isset($_POST['droit'.$i])){
				$req = $bdd->prepare('INSERT INTO attributionDroitsAuxRoles(idRoles, idDroits) VALUES(:idRoles, :idDroits)');
				$req->execute(array(
					'idRoles' => $idRoles['idRoles'],
					'idDroits' => $i));
			}
		}
		$success['creationRole'] = "Le rôle a bien été créé";
		# On met à jour les rôles
		$req = $bdd->prepare('SELECT * FROM ROLES');
		$req->execute();
		$roles = $req->fetchAll();
		$nbRoles = count($roles);
	}
	
}
?>

<?php
# Suppression d'un rôle
for ($i=0; $i < $nbRoles; $i++) {
	if (isset($_POST['suppressionRole'.$roles[$i]['idRoles']]) && $_POST['suppressionRole'.$roles[$i]['idRoles']] == 'Valider') {
		# Si l'étudiant n'est pas autorisé à supprimer un rôle
		$reqDroit->execute(array($_SESSION['id'], 2));
		$data = $reqDroit->fetch();
		if ($data[0] == 0) {
			$errors['droitSuppressionManquant'] = "Vous n'êtes pas autorisé à supprimer un rôle";
		}

		# Si l'étudiant ne possède pas tous les droits conférés par le rôle qu'il souhaite supprimer
		$req = $bdd->prepare('SELECT COUNT(*) FROM ETUDIANTS E WHERE E.id = ? 
			AND NOT EXISTS (SELECT * FROM attributionRolesAuxEtudiants A2 NATURAL JOIN attributionDroitsAuxRoles B2 WHERE A2.idRoles = ? 
			AND NOT EXISTS (SELECT * FROM attributionRolesAuxEtudiants A3 NATURAL JOIN attributionDroitsAuxRoles B3 WHERE A3.id = E.id))');
		$req->execute(array($_SESSION['id'], $roles[$i]['idRoles']));
		$data = $req->fetch();
		if ($data[0] == 0) {
			$errors['droitManquant'] = "Vous ne possédez pas tous les droits de ce rôle";
		}
		
		# Si le rôle ne peut pas être supprimé
		$req = $bdd->prepare('SELECT supprimable FROM ROLES WHERE idRoles = ?');
		$req->execute(array($roles[$i]['idRoles']));
		$data = $req->fetch();
		if ($data['supprimable'] == 0) {
			$errors['roleNonSupprimable'] = "Ce rôle est un rôle par défaut et ne peut pas être supprimé";
		}
		
		# Si des étudiants possèdent encore ce rôle
		$req = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants WHERE idRoles = ?');
		$req->execute(array($roles[$i]['idRoles']));
		$data = $req->fetch();
		if ($data[0] > 0) {
			$errors['etudiantPossedantRole'] = "Ce rôle ne peut pas être supprimé car des étudiants le possèdent encore";
		}
		
		# S'il n'y a pas d'erreur, on supprime le rôle (ses droits sont supprimés en cascade)
		if (empty($errors)) {
			$req = $bdd->prepare('DELETE FROM ROLES WHERE idRoles = ?');
			$req->execute(array($roles[$i]['idRoles']));
			$success['roleSupprimé'] = "Ce rôle a bien été supprimé";
			# On met à jour les rôles
			$req = $bdd->prepare('SELECT * FROM ROLES');
			$req->execute();
			$roles = $req->fetchAll();
			$nbRoles = count($roles);
		}
	}
}
?>

<?php
# Retrait d'un rôle
for ($i=0; $i < $nbRoles; $i++) {
	if (isset($_POST['retirerRole'.$roles[$i]['idRoles']]) && $_POST['retirerRole'.$roles[$i]['idRoles']] == 'Valider') {
		# Si l'étudiant n'est pas autorisé à retirer un rôle
		$reqDroit->execute(array($_SESSION['id'], 4));
		$data = $reqDroit->fetch();
		if ($data[0] == 0) {
			$errors['droitRetraitManquant'] = "Vous n'êtes pas autorisé à retirer un rôle à un étudiant";
		}
		
		# Si l'étudiant ne possède pas tous les droits conférés par le rôle qu'il souhaite retirer à un étudiant
		$req = $bdd->prepare('SELECT COUNT(*) FROM ETUDIANTS E WHERE E.id = ? 
			AND NOT EXISTS (SELECT * FROM attributionRolesAuxEtudiants A2 NATURAL JOIN attributionDroitsAuxRoles B2 WHERE A2.idRoles = ? 
			AND NOT EXISTS (SELECT * FROM attributionRolesAuxEtudiants A3 NATURAL JOIN attributionDroitsAuxRoles B3 WHERE A3.id = E.id))');
		$req->execute(array($_SESSION['id'], $roles[$i]['idRoles']));
		$data = $req->fetch();
		if ($data[0] == 0) {
			$errors['droitManquant'] = "Vous ne possédez pas tous les droits de ce rôle";
		}
		
		# On met dans un tableau la liste des id d'étudiants dont on souhaite retirer le rôle
		$req = $bdd->prepare('SELECT * FROM ETUDIANTS E WHERE EXISTS (SELECT * FROM attributionRolesAuxEtudiants aRAE WHERE aRAE.id = E.id)');
		$req->execute();
		$etudiantsPossedantUnRole = $req->fetchAll();
		$retirerA = array();
		foreach ($etudiantsPossedantUnRole as $etudiantPossedantUnRole){
			if (isset($_POST['retirer'.$etudiantPossedantUnRole['id']])){
				$retirerA[] = $etudiantPossedantUnRole['id'];
			}
		}
		
		# Si aucun étudiant n'a été sélectionnés
		if (count($retirerA) == 0) {
			$errors['aucuneSélection'] = "Vous devez sélectionner au moins un étudiant";
		}
		
		# Si c'est le rôle administrateur, il faut toujours qu'il reste au moins un étudiant possèdant ce rôle
		if ($roles[$i]['idRoles'] == 1) {
			$req = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants WHERE idRoles = 1');
			$req->execute();
			$data = $req->fetch();
			if ($data[0] == count($retirerA)) {
				$errors['administrateurNecessaire'] = "Il doit toujours y avoir au moins un administrateur";
			}
		}
		
		# S'il n'y a pas d'erreur, on retire le rôle aux étudiants sélectionnés
		if (empty($errors)) {
			foreach ($retirerA as $id){
				$req = $bdd->prepare('DELETE FROM attributionRolesAuxEtudiants WHERE idRoles = ? AND id = ?');
				$req->execute(array($roles[$i]['idRoles'], $id));
			}
			$success['roleSupprimé'] = 'Le rôle "'.$roles[$i]['role'].'" a bien été retiré aux étudiants selectionnés';
		}
	}
}
?>

<?php
# Supprimer le compte d'étudiants bannis
if (isset($_POST['supprimerCompte']) && $_POST['supprimerCompte'] == 'Valider') {
	# Si on n'a pas le droit de supprimer un compte
	$reqDroit->execute(array($_SESSION['id'], 9));
	$data = $reqDroit->fetch();
	if ($data[0] == 0) {
		$errors['droitManquant'] = "Vous n'avez pas le droit de supprimer des comptes";
	}
	
	# S'il n'y a pas d'erreur, on supprime le compte des étudiants bannis sélectionnés
	if (empty($errors)) {
		$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE etat = -1');
		$req->execute();
		$etudiantsBannis = $req->fetchAll();
		foreach ($etudiantsBannis as $etudiantBanni){
			if (isset($_POST['banni'.$etudiantBanni['id']])){
				$req = $bdd->prepare('DELETE FROM ETUDIANTS WHERE id = ?');
				$req->execute(array($etudiantBanni['id']));
			}
		}
		$success['comptesSupprimés'] = 'Les comptes des étudiants selectionnés ont bien été supprimés';
	}
}
?>

<?php
# Réhabiliter un étudiant banni
if (isset($_POST['réhabiliterCompte']) && $_POST['réhabiliterCompte'] == 'Valider') {
	# Si on n'a pas le droit de bannir / débannir un étudiant
	$reqDroit->execute(array($_SESSION['id'], 8));
	$data = $reqDroit->fetch();
	if ($data[0] == 0) {
		$errors['droitManquant'] = "Vous n'avez pas le droit de débannir des étudiants";
	}
	
	# S'il n'y a pas d'erreur, on réhabilite les étudiants sélectionnés
	if (empty($errors)) {
		$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE etat = -1');
		$req->execute();
		$etudiantsBannis = $req->fetchAll();
		foreach ($etudiantsBannis as $etudiantBanni){
			if (isset($_POST['banni'.$etudiantBanni['id']])){
				$req = $bdd->prepare('UPDATE ETUDIANTS SET etat = 2 WHERE id = ?');
				$req->execute(array($etudiantBanni['id']));
			}
		}
		$success['étudiantsRéhabilités'] = 'Les étudiants sélectionnés ont bien été réhabilités';
	}
}
?>

<?php
# Valider l'inscription d'étudiants
if (isset($_POST['validerInscription']) && $_POST['validerInscription'] == 'Valider') {
	# Si on n'a pas le droit de valider l'inscription d'étudiants
	$reqDroit->execute(array($_SESSION['id'], 5));
	$data = $reqDroit->fetch();
	if ($data[0] == 0) {
		$errors['droitManquant'] = "Vous n'avez pas le droit de valider l'inscription d'étudiants";
	}
	
	# S'il n'y a pas d'erreur, on valide l'inscription des étudiants sélectionnés
	if (empty($errors)) {
		$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE etat = 1');
		$req->execute();
		$etudiantsEnAttente = $req->fetchAll();
		foreach ($etudiantsEnAttente as $etudiantEnAttente){
			if (isset($_POST['enAttente'.$etudiantEnAttente['id']])){
				echo '12';
				$req = $bdd->prepare('UPDATE ETUDIANTS SET etat = 2 WHERE id = ?');
				$req->execute(array($etudiantEnAttente['id']));
			}
		}
		$success['étudiantsValidés'] = 'Les inscriptions des étudiants sélectionnés ont bien été validées';
	}
}
?>

<?php
# Refuser l'inscription d'étudiants
if (isset($_POST['refuserInscription']) && $_POST['refuserInscription'] == 'Valider') {
	# Si on n'a pas le droit de refuser l'inscription d'étudiants
	$reqDroit->execute(array($_SESSION['id'], 6));
	$data = $reqDroit->fetch();
	if ($data[0] == 0) {
		$errors['droitManquant'] = "Vous n'avez pas le droit de refuser l'inscription d'étudiants";
	}
	
	# S'il n'y a pas d'erreur, on valide l'inscription des étudiants sélectionnés
	if (empty($errors)) {
		$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE etat = 1');
		$req->execute();
		$etudiantsEnAttente = $req->fetchAll();
		foreach ($etudiantsEnAttente as $etudiantEnAttente){
			if (isset($_POST['enAttente'.$etudiantEnAttente['id']])){
				$req = $bdd->prepare('DELETE FROM ETUDIANTS WHERE id = ?');
				$req->execute(array($etudiantEnAttente['id']));
			}
		}
		$success['étudiantsRefusés'] = 'Les inscriptions des étudiants sélectionnés ont bien été refusées';
	}
}
?>


<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" href="../../css/admin.css" />
		<link rel="stylesheet" href="../../css/alerte.css" />
	</head>
	<body>

		<?php require_once '../navigation.php'; ?>
		<?php require_once '../inc/erreurs.php'; ?>
		<?php require_once 'inc/menu.php'; ?>
		

		<div id="page">
		
			<div id="title">
				Rôles
			</div>
			
			
			
			
			<?php 
			$reqDroit->execute(array($_SESSION['id'], 1));
			$data = $reqDroit->fetch();
			if($data[0] > 0): 
			?>
			<div id="contenu">
				<div id ="contenuTitle">
					Créer un nouveau rôle
				</div>
				<div id="creerRoleMiddle">
					<br>
					<form action="roles.php" method="post">
						<label><u>Nom du rôle</u> :</label><br>
						<input type="text" name="nomRole" /><br>
						<label><u>Description</u> :</label><br>
						<textarea name="descriptionRole"></textarea><br><br>
						<table>
							<?php
							$req = $bdd->prepare('SELECT * FROM DROITS');
							$req->execute();
							$tousDroits = $req->fetchAll();
							foreach ($tousDroits as $tousDroit):
								$reqDroit->execute(array($_SESSION['id'], $tousDroit['idDroits']));
								$data = $reqDroit->fetch();
								echo '<tr>';
								echo '<td>'.$tousDroit['droit'].'</td>';
								echo '<td><i>'.$tousDroit['descriptionDroit'].'</i></td>';
								if ($data[0] > 0) {
									echo '<td><input type="checkbox" name="droit'.$tousDroit['idDroits'].'"/></td>';
								}
								else {
									echo '<td><input type="checkbox" disabled/></td>';
								}
								echo '</tr>';
							endforeach;
							?>
						</table>
						<button type="submit" name="creationRole" value="Valider">Créer ce rôle</button>
					</form>
				</div>
			</div>
			<?php endif; ?>
			
			
			<?php foreach ($roles as $role): ?>
			<div class="role">
				<div class="roleTop">
					<div class="roleTitle" id=<?php echo "role".$role['idRoles'] ?>>
						<?php echo text($role['role']); ?>
					</div>
					<div class="roleDescription">
						<acronym title=<?php echo text($role['descriptionRole']); ?>><img src="../../img/info.png"></acronym>
					</div>
					<div class="roleNumber">
						<?php
						if ($role['idRoles'] == 2) {
							$req = $bdd->prepare('SELECT COUNT(*) FROM ETUDIANTS WHERE etat >= 2 AND formation IS NOT NULL');
							$req->execute();
							$nb = $req->fetch();
						} elseif ($role['idRoles'] == 3) {
							$annee = date("Y");
							$annee1 = $annee;
							$annee2 = $annee;
							if (date("m") >= 10) {
								$annee1++;
							}
							if (date("m") >= 8) {
								$annee2++;
							}
							$req = $bdd->prepare('SELECT COUNT(*) FROM ETUDIANTS WHERE etat >= 2 AND
								((formation = "CP2I" AND promotion < ?)
								OR
								(formation != "CP2I" AND promotion < ?))
							');
							$req->execute(array($annee1, $annee2));
							$nb = $req->fetch();
						} elseif ($role['idRoles'] == 5) {
							$req = $bdd->prepare('SELECT COUNT(*) FROM ETUDIANTS WHERE etat = 1');
							$req->execute();
							$nb = $req->fetch();
						} elseif ($role['idRoles'] == 6) {
							$req = $bdd->prepare('SELECT COUNT(*) FROM ETUDIANTS WHERE etat = -1');
							$req->execute();
							$nb = $req->fetch();
						} else {
							$req = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants WHERE idRoles = ?');
							$req->execute(array($role['idRoles']));
							$nb = $req->fetch();
						}
						echo $nb[0];
						?>
					</div>
				</div>
				<div class="roleMiddle" id=<?php echo "middlerole".$role['idRoles'] ?>>
				</div>
				<div class="roleBottom">
				</div>
			</div>
			<?php endforeach; ?>
			
			


		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/admin.js"></script>
		<script type="text/javascript" src="../../js/alerte.js"></script>
		
	</body>
</html>