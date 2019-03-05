<!-- LISTE DES ROLES -->

<?php
# On redirige le visiteur s'il n'a rien � faire sur cette page
session_start();
if (!isset($_SESSION['id'])) {
	header ('Location: ../accueil.php');
	exit();
}
else {
	require_once '../inc/serveur.php';
	$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE id = ? AND EXISTS (SELECT * FROM attributionRolesAuxEtudiants WHERE id = ?)'); // tous ceux qui ont un droit ?
	$req->execute(array($_SESSION['id'], $_SESSION['id']));
	$data = $req->fetch();
	if (!$data) {
		header ('Location: ../accueil.php');
		exit();
	}
}
require_once '../inc/fonctions.php';
?>

<?php
# On r�cup�re les diff�rents r�les pour les afficher
$req = $bdd->prepare('SELECT * FROM ROLES'); // tous ceux qui ont un droit peuvent tout voir ? ORDER BY nb droits ?
$req->execute();
$roles = $req->fetchAll();
$nbRoles = count($roles);
?>

<?php
# On r�cup�re les diff�rents droits
$req = $bdd->prepare('SELECT * FROM DROITS');
$req->execute();
$droits = $req->fetchAll();
$nbDroits = count($droits);
# Requ�te pour savoir si l'�tudiant poss�de le droit i
$reqDroit = $bdd->prepare('SELECT COUNT(*) FROM ETUDIANTS NATURAL JOIN attributionRolesAuxEtudiants NATURAL JOIN attributionDroitsAuxRoles WHERE id = ? AND idDroits = ?');
?>

<?php
$errors = array();
$success = array();
?>

<?php
# Cr�ation d'un r�le
if (isset($_POST['creationRole']) && $_POST['creationRole'] == 'Valider') {
	# Si l'�tudiant n'est pas autoris� � cr�er un r�le
	$reqDroit->execute(array($_SESSION['id'], 1));
	$data = $reqDroit->fetch();
	if ($data[0] == 0) {
		$errors['droitCr�ationManquant'] = "Vous n'�tes pas autoris� � cr�er un r�le";
	}
	
	# Si le nom du r�le est absent, d�j� utilis� ou trop long
	if (empty($_POST['nomRole'])) {
		$errors['nomRole'] = "Nom de r�le manquant";
	} elseif (strlen($_POST['nomRole']) > 30) {
		$errors['nomRole'] = "Le nom du r�le doit faire au plus 30 caract�res";
	} else {
		$req = $bdd->prepare('SELECT COUNT(*) FROM ROLES WHERE role = ?');
		$req->execute(array($_POST['nomRole']));
		$existeDeja = $req->fetch();
		if ($existeDeja[0] != 0) {
			$errors['nomRole'] = "Nom de r�le d�j� utilis�";
		}
	}
	
	# Si la description est trop longue
	if (!empty($_POST['descriptionRole']) && strlen($_POST['descriptionRole']) > 255) {
		$errors['descriptionRole'] = "La description du r�le doit faire au plus 255 caract�res";
	}
	
	# Si l'�tudiant cr�e un r�le et lui attribue un droit qu'il ne poss�de pas
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
	
	# S'il n'y a pas d'erreur, on cr�e le r�le et on lui associe les droits
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
		$success['creationRole'] = "Le r�le a bien �t� cr��";
		# On met � jour les r�les
		$req = $bdd->prepare('SELECT * FROM ROLES');
		$req->execute();
		$roles = $req->fetchAll();
		$nbRoles = count($roles);
	}
	
}
?>

<?php
# Suppression d'un r�le
for ($i=0; $i < $nbRoles; $i++) {
	if (isset($_POST['suppressionRole'.$roles[$i]['idRoles']]) && $_POST['suppressionRole'.$roles[$i]['idRoles']] == 'Valider') {
		# Si l'�tudiant n'est pas autoris� � supprimer un r�le
		$reqDroit->execute(array($_SESSION['id'], 2));
		$data = $reqDroit->fetch();
		if ($data[0] == 0) {
			$errors['droitSuppressionManquant'] = "Vous n'�tes pas autoris� � supprimer un r�le";
		}

		# Si l'�tudiant ne poss�de pas tous les droits conf�r�s par le r�le qu'il souhaite supprimer
		$req = $bdd->prepare('SELECT COUNT(*) FROM ETUDIANTS E WHERE E.id = ? 
		AND NOT EXISTS (SELECT * FROM attributionRolesAuxEtudiants A2 NATURAL JOIN attributionDroitsAuxRoles B2 WHERE A2.idRoles = ? 
		AND NOT EXISTS (SELECT * FROM attributionRolesAuxEtudiants A3 NATURAL JOIN attributionDroitsAuxRoles B3 WHERE A3.id = E.id))');
		$req->execute(array($_SESSION['id'], $roles[$i]['idRoles']));
		$data = $req->fetch();
		if ($data[0] == 0) {
			$errors['droitManquant'] = "Vous ne poss�dez pas tous les droits de ce r�le";
		}
		
		# Si le r�le ne peut pas �tre supprim�
		$req = $bdd->prepare('SELECT supprimable FROM ROLES WHERE idRoles = ?');
		$req->execute(array($roles[$i]['idRoles']));
		$data = $req->fetch();
		if ($data['supprimable'] == 0) {
			$errors['roleNonSupprimable'] = "Ce r�le est un r�le par d�faut et ne peut pas �tre supprim�";
		}
		
		# Si des �tudiants poss�dent encore ce r�le
		$req = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants WHERE idRoles = ?');
		$req->execute(array($roles[$i]['idRoles']));
		$data = $req->fetch();
		if ($data[0] > 0) {
			$errors['etudiantPossedantRole'] = "Ce r�le ne peut pas �tre supprim� car des �tudiants le poss�dent encore";
		}
		
		# S'il n'y a pas d'erreur, on supprime le r�le (ses droits sont supprim�s en cascade)
		if (empty($errors)) {
			$req = $bdd->prepare('DELETE FROM ROLES WHERE idRoles = ?');
			$req->execute(array($roles[$i]['idRoles']));
			$success['roleSupprim�'] = "Ce r�le a bien �t� supprim�";
			# On met � jour les r�les
			$req = $bdd->prepare('SELECT * FROM ROLES');
			$req->execute();
			$roles = $req->fetchAll();
			$nbRoles = count($roles);
		}
	}
}
?>

<?php
# Retrait de r�les
	// Si on a pas le droit de retirer des roles
	// Si on ne poss�de pas tous les droits que conf�rent ce role
	// Si c'est le role admin et qu'il n'en restera plus
?>


<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" lang="fr">
		<link rel="stylesheet" href="../../css/admin.css" />
	</head>
	<body>
		<div id="menu">
			<img src="menu.png" style="padding: 20px">
			<nav>
				<ul>
					<li id="menu-role"></li><hr>
					<li id="menu-etudiant"></li>
					<li id="menu-calendrier"></li>
					<li id="menu-boutique"></li>
					<li id="menu-minijeu"></li>
				</ul>
			</nav>
		</div>

		<div id="page">
			<div id="head">
				Accueil
			</div>
			<div id="title">
				R�les
			</div>
			
			<?php if(!empty($errors)): ?>
			<p class="red">
				<?php foreach ($errors as $error): ?>
					<?= $error; ?><br>
				<?php endforeach; ?>
			</p>
			<?php endif; ?>
			<?php if(!empty($success)): ?>
			<p class="green">
				<?php foreach ($success as $succes): ?>
					<?= $succes; ?><br>
				<?php endforeach; ?>
			</p>
			<?php endif; ?>
			
			
			<?php 
			$reqDroit->execute(array($_SESSION['id'], 1));
			$data = $reqDroit->fetch();
			if($data[0] > 0): 
			?>
			<div id="contenu">
				<div id ="creerRoleTitle">
					Cr�er un nouveau r�le
				</div>
				<div id="creerRoleMiddle">
					<br>
					<form action="roles.php" method="post">
						<label>Nom du r�le :</label><br>
						<input type="text" name="nomRole" /><br>
						<label>Description :</label><br>
						<textarea name="descriptionRole"></textarea><br><br>
						<table style="text-align: center;border: 1px solid #1E1E1E">
							<?php
							$req = $bdd->prepare('SELECT * FROM DROITS');
							$req->execute();
							$tousDroits = $req->fetchAll();
							foreach ($tousDroits as $tousDroit):
								$reqDroit->execute(array($_SESSION['id'], $tousDroit['idDroits']));
								$data = $reqDroit->fetch();
								echo '<tr>';
								echo '<td style="border-right: 1px solid #1E1E1E">'.$tousDroit['droit'].'</td>';
								echo '<td style="border-right: 1px solid #1E1E1E"><i>'.$tousDroit['descriptionDroit'].'</i></td>';
								if ($data[0] > 0) {
									echo '<td><input type="checkbox" name="droit'.$tousDroit['idDroits'].'"/></td>';
								}
								else {
									echo '<td><input type="checkbox" disabled/></td>';
								}
								echo '</tr>';
							endforeach;
							?>
						</table><br>
						<button type="submit" name="creationRole" value="Valider">Cr�er ce r�le</button>
					</form><br>
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
							$req = $bdd->prepare('SELECT COUNT(*) FROM ETUDIANTS WHERE etat >= 2 AND formation IS NULL');
							$req->execute();
							$nb = $req->fetch();
						} elseif ($role['idRoles'] == 4) {
							$req = $bdd->prepare('SELECT COUNT(*) FROM ETUDIANTS WHERE etat = 1');
							$req->execute();
							$nb = $req->fetch();
						} elseif ($role['idRoles'] == 5) {
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
	</body>
</html>