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
# Si l'id passé en url est bon, on récupère les rôles de l'étudiant possédant cet id
if (isset($_GET['id'])){
	$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE id = ? AND etat != 0');
	$req->execute(array(intval($_GET['id'])));
	$etudiant = $req->fetch();
	if ($etudiant) {
		$id = intval($_GET['id']);
		$req = $bdd->prepare('SELECT * FROM attributionRolesAuxEtudiants NATURAL JOIN ROLES NATURAL LEFT JOIN attributionDroitsAuxRoles WHERE id = ? GROUP BY idRoles ORDER BY COUNT(*) DESC, idRoles ASC');
		$req->execute(array($id));
		$rolesPossedes = $req->fetchAll();
	}
}
?>

<?php
# Requête pour savoir si l'étudiant possède le droit i
$reqDroit = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants NATURAL JOIN attributionDroitsAuxRoles WHERE id = ? AND idDroits = ?');
# Requête pour savoir si l'étudiant b possède tous les droits possédés par l'étudiant a
$reqPossedeTout = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants A1 NATURAL JOIN attributionDroitsAuxRoles B1 WHERE A1.id = ? 
	AND NOT EXISTS (SELECT * FROM attributionRolesAuxEtudiants A2 NATURAL JOIN attributionDroitsAuxRoles B2 WHERE A2.id = ? AND B1.idDroits = B2.idDroits)');
# Requête pour savoir quels rôles l'étudiant a peut attribuer à l'étudiant b
$reqQuelsRoles = $bdd->prepare('SELECT * FROM ROLES R WHERE R.idRoles NOT IN (2,3,4,5,6)
	AND NOT EXISTS (SELECT * FROM attributionDroitsAuxRoles A1 WHERE R.idRoles = A1.idRoles
	AND NOT EXISTS (SELECT * FROM attributionRolesAuxEtudiants B1 NATURAL JOIN attributionDroitsAuxRoles A2 WHERE B1.id = ? AND A1.idDroits = A2.idDroits))
	AND NOT EXISTS (SELECT * FROM attributionRolesAuxEtudiants B2 WHERE B2.id = ? AND B2.idRoles = R.idRoles)');
?>

<?php
if (isset($_POST['rechercheEtudiant']) && $_POST['rechercheEtudiant'] == 'Valider') {
	if (isset($_POST['prenom'], $_POST['nom'], $_POST['formation'])){
		if (empty($_POST['formation'])) {
			$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE prenom LIKE ? AND nom LIKE ? AND etat != 0 ORDER BY nom ASC');
			$req->execute(array('%'.$_POST['prenom'].'%', '%'.$_POST['nom'].'%'));
		} else {
			$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE prenom LIKE ? AND nom LIKE ? AND etat != 0 AND formation LIKE ? ORDER BY nom ASC');
			$req->execute(array('%'.$_POST['prenom'].'%', '%'.$_POST['nom'].'%', '%'.$_POST['formation'].'%'));
		}
		$resultats = $req->fetchAll();
		echo strval($_POST['prenom']);
	}
}
?>

<?php
$errors = array();
$success = array();
?>

<?php
# Attribuer un rôle
if (isset($_POST['attribuerRole']) && $_POST['attribuerRole'] == 'Valider') {
	# S'il n'y a pas d'étudiant correct dans l'url
	# Sinon s'il n'y a pas de rôle sélectionné
	# Sinon si l'étudiant destination possède déjà ce rôle
	# Sinon si l'étudiant ne possède pas tous les droits de ce rôle
	if (!isset($id)) {
		$errors['étudiantManquant'] = "Etudiant inexistant";
	} else {
		if (empty($_POST['nomRole'])) {
			$errors['roleManquant'] = "Aucun rôle n'a été spécifié";
		} else {
			$req = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants NATURAL JOIN ROLES WHERE id = ? AND role = ?');
			$req->execute(array($id, $_POST['nomRole']));
			$data = $req->fetch();
			if ($data[0] > 0) {
				$errors['roleDejaPossede'] = "L'étudiant possède déjà ce rôle";
			}
			$req = $bdd->prepare('SELECT COUNT(*) FROM ETUDIANTS E WHERE E.id = ? 
				AND NOT EXISTS (SELECT * FROM attributionRolesAuxEtudiants A2 NATURAL JOIN attributionDroitsAuxRoles B2 NATURAL JOIN ROLES C WHERE C.role = ? 
				AND NOT EXISTS (SELECT * FROM attributionRolesAuxEtudiants A3 NATURAL JOIN attributionDroitsAuxRoles B3 WHERE A3.id = E.id))');
			$req->execute(array($_SESSION['id'], $_POST['nomRole']));
			$data = $req->fetch();
			if ($data[0] == 0) {
				$errors['droitManquant'] = "Vous devez posséder tous les droits conférés par ce rôle";
			}
		}
	}
	
	# Si l'étudiant n'est pas autorisé à attribuer un rôle
	$reqDroit->execute(array($_SESSION['id'], 3));
	$data = $reqDroit->fetch();
	if ($data[0] == 0) {
		$errors['droitAttributionManquant'] = "Vous n'êtes pas autorisé à attribuer un rôle";
	}
	
	# S'il n'y a pas d'erreur, on attribue le rôle à l'étudiant
	if (empty($errors)) {
		$req = $bdd->prepare('SELECT * FROM ROLES WHERE role = ?');
		$req->execute(array($_POST['nomRole']));
		$data = $req->fetch();
		$req = $bdd->prepare('INSERT INTO attributionRolesAuxEtudiants(id, idRoles) VALUES(:id, :idRoles)');
		$req->execute(array(
			'id' => $id,
			'idRoles' => $data['idRoles']));
		$success['roleAttribué'] = "Le rôle a bien été attribué";
		# On met à jour les rôles possédés
		$req = $bdd->prepare('SELECT * FROM attributionRolesAuxEtudiants NATURAL JOIN ROLES NATURAL LEFT JOIN attributionDroitsAuxRoles WHERE id = ? GROUP BY idRoles ORDER BY COUNT(*) DESC, idRoles ASC');
		$req->execute(array($id));
		$rolesPossedes = $req->fetchAll();
	}
}
?>

<?php
# Bannir un étudiant (supprime tous les rôles possédés)
if (isset($_POST['bannir']) && $_POST['bannir'] == 'Valider') {
	# S'il n'y a pas d'étudiant correct dans l'url
	if (!isset($id)) {
		$errors['étudiantManquant'] = "Etudiant inexistant";
	} else {
		# Si l'étudiant n'a pas le droit de bannir un autre étudiant
		$reqDroit->execute(array($_SESSION['id'], 8));
		$data = $reqDroit->fetch();
		if ($data[0] == 0) {
			$errors['droitBannissementManquant'] = "Vous n'êtes pas autorisé à bannir un étudiant";
		}
		
		# Si l'étudiant essaye de se bannir lui-même
		if ($id == $_SESSION['id']) {
			$errors['bannissementAbsurde'] = "Vous ne pouvez pas vous bannir vous-même";
		}
		
		# Si l'étudiant ne possède pas tous les droits possédés par l'étudiant qu'il souhaite bannir
		$reqPossedeTout->execute(array($id, $_SESSION['id']));
		$data = $reqPossedeTout->fetch();
		if ($data[0] > 0) {
			$errors['droitManquant'] = "Vous devez posséder tous les droits possédés par cet étudiant pour pouvoir le bannir";
		}
		
		# Si l'étudiant tente de bannir un administrateur sans être administrateur lui-même
		$req = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants A1 WHERE A1.id = ? AND A1.idRoles = 1 AND NOT EXISTS(SELECT * FROM attributionRolesAuxEtudiants A2 WHERE A2.id = ? AND A2.idRoles = 1)');
		$req->execute(array($id, $_SESSION['id']));
		$data = $req->fetch();
		if ($data[0] > 0) {
			$errors['bannissementInterdit'] = "Vous n'êtes pas autorisé à bannir un administrateur";
		}
		
		# S'il n'y a pas d'erreur, on banni l'étudiant et on supprime ses rôles
		if (empty($errors)) {
			$req = $bdd->prepare('UPDATE ETUDIANTS SET etat = -1 WHERE id = ?');
			$req->execute(array($id));
			$etudiant['etat'] = -1;
			$req = $bdd->prepare('DELETE FROM attributionRolesAuxEtudiants WHERE id = ?');
			$req->execute(array($id));
			$success['étudiantBanni'] = "Cet étudiant a bien été banni";
		}
	}
}
?>

<?php
# Réhabiliter un étudiant
if (isset($_POST['rehabiliter']) && $_POST['rehabiliter'] == 'Valider') {
	# S'il n'y a pas d'étudiant correct dans l'url
	if (!isset($id)) {
		$errors['étudiantManquant'] = "Etudiant inexistant";
	} else {
		# Si l'étudiant passé en url n'est pas dans l'état -1
		if ($etudiant['etat'] != -1) {
			$errors['mauvaisEtat'] = "Erreur détectée : l'étudiant a peut-être déjà été réhabilité ou supprimé de la base de données";
		}
		
		# Si l'étudiant n'a pas le droit de réhabiliter un autre étudiant
		$reqDroit->execute(array($_SESSION['id'], 8));
		$data = $reqDroit->fetch();
		if ($data[0] == 0) {
			$errors['droitRehabilitationManquant'] = "Vous n'êtes pas autorisé à réhabiliter un étudiant";
		}
		
		# S'il n'y a pas d'erreur, on réhabilite l'étudiant
		if (empty($errors)) {
			$req = $bdd->prepare('UPDATE ETUDIANTS SET etat = 2 WHERE id = ?');
			$req->execute(array($id));
			$etudiant['etat'] = 2;
			$success['étudiantRéhabilité'] = "Cet étudiant a bien été réhabilité";
		}
	}
}
?>

<?php
# Supprimer le compte d'un étudiant banni
if (isset($_POST['supprimerCompte']) && $_POST['supprimerCompte'] == 'Valider') {
	# S'il n'y a pas d'étudiant correct dans l'url
	if (!isset($id)) {
		$errors['étudiantManquant'] = "Etudiant inexistant";
	} else {
		# Si l'étudiant passé en url n'est pas dans l'état -1
		if ($etudiant['etat'] != -1) {
			$errors['mauvaisEtat'] = "Erreur détectée : l'étudiant a peut-être déjà été réhabilité ou supprimé de la base de données";
		}
		
		# Si l'étudiant n'a pas le droit de supprimer le compte d'un étudiant banni
		$reqDroit->execute(array($_SESSION['id'], 9));
		$data = $reqDroit->fetch();
		if ($data[0] == 0) {
			$errors['droitSuppressionCompteManquant'] = "Vous n'êtes pas autorisé supprimé le compte d'un étudiant banni";
		}
		
		# S'il n'y a pas d'erreur, on supprime l'étudiant
		if (empty($errors)) {
			$req = $bdd->prepare('DELETE FROM ETUDIANTS WHERE id = ?');
			$req->execute(array($id));
			unset($id);
			$success['étudiantSupprimé'] = "Le compte de cet étudiant a bien été supprimé";
		}
	}
}
?>

<?php
# Valider l'inscription
if (isset($_POST['validerInscription']) && $_POST['validerInscription'] == 'Valider') {
	# S'il n'y a pas d'étudiant correct dans l'url
	if (!isset($id)) {
		$errors['étudiantManquant'] = "Etudiant inexistant";
	} else {
		# Si l'étudiant passé dans l'url n'est pas dans l'état 1
		if ($etudiant['etat'] != 1) {
			$errors['mauvaisEtat'] = "Erreur détectée : l'inscription de l'étudiant a peut-être déjà été acceptée ou refusée";
		}
		
		# Si l'étudiant n'a pas le droit de valider l'inscription d'un autre étudiant
		$reqDroit->execute(array($_SESSION['id'], 5));
		$data = $reqDroit->fetch();
		if ($data[0] == 0) {
			$errors['droitValidationManquant'] = "Vous n'êtes pas autorisé à valider l'inscription d'un étudiant";
		}
		
		# S'il n'y a pas d'erreur, on valide l'inscription de l'étudiant
		if (empty($errors)) {
			$req = $bdd->prepare('UPDATE ETUDIANTS SET etat = 2 WHERE id = ?');
			$req->execute(array($id));
			$etudiant['etat'] = 2;
			$success['étudiantValidé'] = "L'inscription de cet étudiant a bien été validée";
		}
	}
}
?>

<?php
# Refuser l'inscription
if (isset($_POST['refuserInscription']) && $_POST['refuserInscription'] == 'Valider') {
	# S'il n'y a pas d'étudiant correct dans l'url
	if (!isset($id)) {
		$errors['étudiantManquant'] = "Etudiant inexistant";
	} else {
		# Si l'étudiant passé dans l'url n'est pas dans l'état 1
		if ($etudiant['etat'] != 1) {
			$errors['mauvaisEtat'] = "Erreur détectée : l'inscription de l'étudiant a peut-être déjà été acceptée ou refusée";
		}
		
		# Si l'étudiant n'a pas le droit de refuser l'inscription d'un autre étudiant
		$reqDroit->execute(array($_SESSION['id'], 6));
		$data = $reqDroit->fetch();
		if ($data[0] == 0) {
			$errors['droitRefusManquant'] = "Vous n'êtes pas autorisé à refuser l'inscription d'un étudiant";
		}
		
		# S'il n'y a pas d'erreur, on refuse l'inscription de l'étudiant
		if (empty($errors)) {
			$req = $bdd->prepare('DELETE FROM ETUDIANTS WHERE id = ?');
			$req->execute(array($id));
			unset($id);
			$success['étudiantRefusé'] = "L'inscription de cet étudiant a bien été refusée";
		}
	}
}
?>

<?php
# Rendre muet un étudiant sur le forum
if (isset($_POST['muter']) && $_POST['muter'] == 'Valider') {
	# S'il n'y a pas d'étudiant correct dans l'url
	if (!isset($id)) {
		$errors['étudiantManquant'] = "Etudiant inexistant";
	} else {
		# Si l'étudiant passé dans l'url n'est pas dans l'état 2
		if ($etudiant['etat'] != 2) {
			$errors['mauvaisEtat'] = "Erreur détectée : l'étudiant a peut-être déjà été rendu muet";
		}
		
		# Si l'étudiant n'a pas le droit de rendre muet un autre étudiant
		$reqDroit->execute(array($_SESSION['id'], 11));
		$data = $reqDroit->fetch();
		if ($data[0] == 0) {
			$errors['droitMuterManquant'] = "Vous n'êtes pas autorisé à rendre muet un étudiant";
		}
		
		# Si l'étudiant essaye de se retirer le droit de parole lui-même
		if ($id == $_SESSION['id']) {
			$errors['muteAbsurde'] = "Vous ne pouvez pas vous rendre muet vous-même";
		}
		
		# Si l'étudiant ne possède pas tous les droits possédés par l'étudiant qu'il souhaite rendre muet
		$reqPossedeTout->execute(array($id, $_SESSION['id']));
		$data = $reqPossedeTout->fetch();
		if ($data[0] > 0) {
			$errors['droitManquant'] = "Vous devez posséder tous les droits possédés par cet étudiant pour pouvoir le rendre muet";
		}
		
		# S'il n'y a pas d'erreur, on retire la parole de l'étudiant
		if (empty($errors)) {
			$req = $bdd->prepare('UPDATE ETUDIANTS SET etat = 3 WHERE id = ?');
			$req->execute(array($id));
			$etudiant['etat'] = 3;
			$success['étudiantMuté'] = "L'étudiant n'a plus le droit de poster des messages sur le forum";
		}
	}
}
?>

<?php
# Redonner le droit de parole à un étudiant
if (isset($_POST['demuter']) && $_POST['demuter'] == 'Valider') {
	# S'il n'y a pas d'étudiant correct dans l'url
	if (!isset($id)) {
		$errors['étudiantManquant'] = "Etudiant inexistant";
	} else {
		# Si l'étudiant passé dans l'url n'est pas dans l'état 3
		if ($etudiant['etat'] != 3) {
			$errors['mauvaisEtat'] = "Erreur détectée : l'étudiant a peut-être déjà retrouvé le droit de parole";
		}
		
		# Si l'étudiant n'a pas le droit de démuter un autre étudiant
		$reqDroit->execute(array($_SESSION['id'], 11));
		$data = $reqDroit->fetch();
		if ($data[0] == 0) {
			$errors['droitDemuterManquant'] = "Vous n'êtes pas autorisé à rendre la parole à un étudiant";
		}
		
		# S'il n'y a pas d'erreur, on rend la parole à l'étudiant
		if (empty($errors)) {
			$req = $bdd->prepare('UPDATE ETUDIANTS SET etat = 2 WHERE id = ?');
			$req->execute(array($id));
			$etudiant['etat'] = 2;
			$success['étudiantDémuté'] = "L'étudiant a à nouveau le droit de poster des messages sur le forum";
		}
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
				Etudiant
			</div>

			
			
			<?php if(isset($id)): ?>
				<div id="contenu">
					<div id ="contenuTitle">
						<?php
						$reqPossedeTout->execute(array($id, $_SESSION['id'])); # Pour savoir si on est autorisé à agir sur l'étudiant sélectionné
						$droitTout = $reqPossedeTout->fetch();
						$reqDroit->execute(array($_SESSION['id'], 3)); # Attribuer un rôle
						$droit3 = $reqDroit->fetch();
						$reqDroit->execute(array($_SESSION['id'], 5)); # Valider l'inscription
						$droit5 = $reqDroit->fetch();
						$reqDroit->execute(array($_SESSION['id'], 6)); # Refuser l'inscription
						$droit6 = $reqDroit->fetch();
						$reqDroit->execute(array($_SESSION['id'], 7)); # Modifier le profil
						$droit7 = $reqDroit->fetch();
						$reqDroit->execute(array($_SESSION['id'], 8)); # Bannir
						$droit8 = $reqDroit->fetch();
						$reqDroit->execute(array($_SESSION['id'], 9)); # Supprimer un compte
						$droit9 = $reqDroit->fetch();
						$reqDroit->execute(array($_SESSION['id'], 11)); # Rendre muet
						$droit11 = $reqDroit->fetch();
						?>
						<?php 
						echo text($etudiant['prenom']).' '.strtoupper(text($etudiant['nom']));
						if ($droit7[0] > 0) {
							echo ' <button><img src="../../img/modifier.png"></button>';
						}
						?> 
						<?php 
						if ($etudiant['etat'] == 0 || $etudiant['etat'] == 1) {
							echo '<img src="../../img/etat-1.png">';
						} elseif ($etudiant['etat'] == 2) {
							echo '<img src="../../img/etat-2.png">';
						} elseif ($etudiant['etat'] == 3) {
							echo '<img src="../../img/etat-3.png">';
						} else {
							echo '<img src="../../img/etat--1.png">';
						}
						echo '<br><form action="" method="post">';
						if ($droit8[0] > 0 && $etudiant['etat'] >= 2 && $droitTout[0] == 0) {
							echo '<button type="submit" name="bannir" value="Valider">Bannir</button> ';
						}
						if ($droit8[0] > 0 && $etudiant['etat'] == -1) {
							echo '<button type="submit" name="rehabiliter" value="Valider">Réhabiliter</button> ';
						}
						if ($droit9[0] > 0 && $etudiant['etat'] == -1) {
							echo '<button type="submit" name="supprimerCompte" value="Valider">Supprimer ce compte</button> ';
						}
						if ($droit5[0] > 0 && $etudiant['etat'] == 1) {
							echo '<button type="submit" name="validerInscription" value="Valider">Valider l\'inscription</button> ';
						}
						if ($droit6[0] > 0 && $etudiant['etat'] == 1) {
							echo '<button type="submit" name="refuserInscription" value="Valider">Refuser l\'inscription</button> ';
						}
						if ($droit11[0] > 0 && $etudiant['etat'] == 2 && $droitTout[0] == 0) {
							echo '<button type="submit" name="muter" value="Valider">Rendre muet</button> ';
						}
						if ($droit11[0] > 0 && $etudiant['etat'] == 3) {
							echo '<button type="submit" name="demuter" value="Valider">Redonner le droit de parole</button> ';
						}
						echo '</form>';
						?>
						
					</div>
					<h1>Informations personnelles :</h1>
					<table style="width: 500px; margin-left: auto; margin-right: auto;">
						<tr>
							<td>Prénom</td>
							<td><?php echo text($etudiant['prenom']) ?>
						</tr>
						<tr>
							<td>Nom</td>
							<td><?php echo text($etudiant['nom']) ?>
						</tr>
						<tr>
							<td>Numéro étudiant</td>
							<td><?php echo text($etudiant['numero']) ?>
						</tr>
						<tr>
							<td>Mail universitaire</td>
							<td><?php echo text($etudiant['mailUniv']) ?>
						</tr>
						<tr>
							<td>Mail personnel</td>
							<td><?php echo text($etudiant['mailPerso']) ?>
						</tr>
						<tr>
							<td>Formation</td>
							<td><?php echo text($etudiant['formation']) ?>
						</tr>
						<tr>
							<td>Promotion</td>
							<td><?php echo text($etudiant['promotion']) ?>
						</tr>
					</table>
					<?php if ($etudiant['etat'] >= 2): ?>
						<hr>
						<h1>Rôles possédés :</h1>
						<?php 
						if (count($rolesPossedes) > 0) { 
							foreach ($rolesPossedes as $rolePossede){
								echo '- '.text($rolePossede['role']).'<br>';
							}
						} else { 
							echo 'Aucun rôle n\'est possédé par cet étudiant';
						}
						?>
						<?php 
						if ($droit3[0] > 0): 
							$reqQuelsRoles->execute(array($_SESSION['id'], $id));
							$rolesAjoutables = $reqQuelsRoles->fetchAll();
						?>
						<form method="post">
						<label>Attribuer un rôle à cet étudiant :</label>
							<select name="nomRole">
								<?php 
								foreach ($rolesAjoutables as $roleAjoutable){
									echo '<option>'.text($roleAjoutable['role']);
								}
								?>
							</select>
							<button type="submit" name="attribuerRole" value="Valider">Valider</button>
						</form>
						<?php endif; ?>
					<?php endif; ?>
					 
				</div>
			<?php endif; ?>
			<div id="contenu">
				<div id ="contenuTitle">
					Rechercher un étudiant
				</div>
				<form action="etudiant.php" method="post">
					<label><u>Prénom</u> :</label><br>
					<input type="text" name="prenom"/><br>
					<label><u>Nom</u> :</label><br>
					<input type="text" name="nom" style="margin-bottom: 5px"/><br>
					<label><u>Formation</u> :</label>
					<select name="formation" size="1">
						<option>
						<option>CP2I
						<option>ENER
						<option>INFO
						<option>MACS
						<option>TELE
						<option>INST
					</select><br>
					<button type="submit" name="rechercheEtudiant" value="Valider">Rechercher</button>
				</form>
			</div>
			<?php if(isset($resultats) && !isset($id)): ?>
				<div id="contenu">
					<div id ="contenuTitle">
						<?php echo count($resultats) ?> résultat(s)
					</div>
					<table>
						<?php foreach ($resultats as $resultat): ?>
							<tr>
								<td><?php echo text($resultat['prenom']) ?></td>
								<td><?php echo strtoupper(text($resultat['nom'])) ?></td>
								<td><a href="etudiant.php?id=<?php echo $resultat['id']; ?>"><img src="../../img/id-card.png"></a></td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>
			<?php endif; ?>
		</div>

		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/alerte.js"></script>
	</body>
</html>