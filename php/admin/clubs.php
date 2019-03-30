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
# Requête pour savoir si l'étudiant possède le droit i
$reqDroit = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants NATURAL JOIN attributionDroitsAuxRoles WHERE id = ? AND idDroits = ?');
$reqDroit->execute(array($_SESSION['id'], 20));
$data = $reqDroit->fetch();
if ($data[0] > 0) {
	$droit20 = true;
} else {
	$droit20 = false;
}
?>

<?php
# Créer un nouveau club
if (isset($_POST['creationClub']) && $_POST['creationClub'] == 'Valider') {
	
	$errors = array();
	
	# Si on ne possède pas le droit de gestion des clubs (droit n°20)
	if ($droit20 == false) {
		$errors['droitClubManquant'] = "Vous n'êtes pas autorisé à créer ou supprimer des clubs";
	}
	
	# Si le nom du club n'est pas bon (vide, trop grand, déjà utilisé)
	if (empty($_POST['club'])) {
		$errors['club'] = "Nom de club manquant";
	} else if (strlen($_POST['club']) > 100) {
		$errors['club'] = "Le nom du club doit faire moins de 100 caractères";
	} else {
		$req = $bdd->prepare('SELECT COUNT(*) FROM CLUBS WHERE club = ?');
		$req->execute(array($_POST['club']));
		$data = $req->fetch();
		if ($data[0] > 0) {
			$errors['club'] = "Un club porte déjà ce nom";
		}
	}
	
	# Si la description est trop longue
	if (!empty($_POST['descriptionClub']) && strlen($_POST['descriptionClub']) > 1000) {
		$errors['descriptionClub'] = "La description du club doit faire au plus 1000 caractères";
	}
	
	# On vérifie que l'image envoyée est bien dans la banque d'images
	if ($_POST['image'] == 'Aucune') {
		$idImages = NULL;
	} else {
		$req = $bdd->prepare('SELECT * FROM IMAGES WHERE image = ?');
		$req->execute(array($_POST['image']));
		$data = $req->fetch();
		if ($data) {
			$idImages = $data['idImages'];
		} else {
			$errors['image'] = "L'image sélectionnée n'existe pas";
		}
	}
	
	# S'il n'y a pas d'erreur, on crée le club
	if (empty($errors)) {
		$req = $bdd->prepare('INSERT INTO CLUBS(idImages, club, descriptionClub) VALUES(:idImages, :club, :descriptionClub)');
		$req->execute(array(
			'idImages' => $idImages,
			'club' => $_POST['club'],
			'descriptionClub' => $_POST['descriptionClub']));
		$success['clubCréé'] = "Le club a bien été créé";
	}
}
?>

<?php
# Retirer les clubs sélectionnés
if (isset($_POST['supprimerClubs']) && $_POST['supprimerClubs'] == 'Valider') {
	
	$errors = array();
	
	# Si on ne possède pas le droit de gestion des clubs (droit n°20)
	if ($droit20 == false) {
		$errors['droitClubManquant'] = "Vous n'êtes pas autorisé à créer ou supprimer des clubs";
	}
	
	# S'il n'y a pas de clubs à retirer
	if (!isset($_POST['clubs'])) {
		$errors['aucunClubSelectionne'] = "Aucun club n'a été sélectionné";
	}
	
	# S'il n'y a pas d'erreur, on retire les clubs sélectionnés
	if (empty($errors)) {
		foreach($_POST['clubs'] as $valeur) {
			$req = $bdd->prepare('DELETE FROM CLUBS WHERE idClubs = ?');
			$req->execute(array($valeur));
		}
		$success['clubsRetires'] = "Les clubs sélectionnés ont bien été retirés";
	}
}
?>

<?php
# On récupère les différents clubs
$req = $bdd->prepare('SELECT * FROM CLUBS ORDER BY idClubs ASC');
$req->execute();
$clubs = $req->fetchAll();
?>

<?php
# On récupère les différentes images
$req = $bdd->prepare('SELECT * FROM IMAGES LIMIT 100');
$req->execute();
$images = $req->fetchAll();
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
				Clubs
			</div>
			
			<?php if($droit20 == true): ?>
			<div id="contenu">
				<div class="plus" id ="contenuTitle">
					Créer un nouveau club
				</div>
				<div id="creerRoleMiddle">
					<br>
					<form action="clubs.php" method="post">
						<label><u>Nom du club</u> :</label><br>
						<input type="text" name="club" /><br>
						<label><u>Description</u> :</label><br>
						<textarea name="descriptionClub"></textarea><br>
						<label><u>Image du club</u> :</label><br>
						<select name="image" id="selectImage">
							<option>Aucune
							<?php foreach($images as $image): ?>
							<option><?php echo $image['image']; ?>
							<?php endforeach; ?>
						</select><br>
						<div id="apercuImage"></div>
						
						<button type="submit" name="creationClub" value="Valider">Créer ce club</button>
					</form>
				</div>
			</div>
			<?php endif; ?>
			
			<div id="contenu">
				<form action="clubs.php" method="post">
					<table>
						<tr>
							<th>Club</td>
							<th>Image</td>
							<?php if ($droit20 == true): ?>
							<th><img src="../../img/supprimer.png"></td>
							<?php endif; ?>
						</tr>
						<?php foreach ($clubs as $club): ?>
						<tr>
							<td style="font-size: 20px;"><?php echo text($club['club']); ?></td>
							<td><img src="<?php 
												$req = $bdd->prepare('SELECT * FROM IMAGES WHERE idImages = ?');
												$req->execute(array($club['idImages']));
												$img = $req->fetch();
												if ($img['image'] != NULL) {
													echo '../../img/imports/'.$img['image'];
												}
											?>" style="max-height: 150px; max-width: 300px" ></td>
							<?php if ($droit20 == true): ?>
							<td><input type="checkbox" name="clubs[]" value=<?php echo $club['idClubs']; ?> ></td>
							<?php endif; ?>
						</tr>
						<?php endforeach; ?>
					</table>
					<?php if ($droit20 == true): ?>
					<button type="submit" name="supprimerClubs" value="Valider">Supprimer les clubs sélectionnés</button>
					<?php endif; ?>
				</form>
			</div>
		</div>

		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/admin.js"></script>
		<script type="text/javascript" src="../../js/alerte.js"></script>
	</body>
</html>