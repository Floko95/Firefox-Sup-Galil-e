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
$reqDroit->execute(array($_SESSION['id'], 14));
$data = $reqDroit->fetch();
if ($data[0] > 0) {
	$droit14 = true;
} else {
	$droit14 = false;
}
?>

<?php
# Créer une nouvelle actualité
if (isset($_POST['creationActualite']) && $_POST['creationActualite'] == 'Valider') {
	
	$errors = array();
	
	# Si on ne possède pas le droit de gestion des actualités (droit n°14)
	if ($droit14 == false) {
		$errors['droitActualitéManquant'] = "Vous n'êtes pas autorisé à ajouter ou retirer des actualités";
	}
	
	# Si le titre de l'actualite n'est pas bon (vide, trop grand)
	if (empty($_POST['actualite'])) {
		$errors['actualite'] = "Nom de l'actualité manquant";
	} else if (strlen($_POST['actualite']) > 100) {
		$errors['actualite'] = "Le nom de l'actualite doit faire moins de 100 caractères";
	}
	
	# Si le créateur (étudiant, club, association, ...) n'est pas bon (trop grand)
	if (!empty($_POST['createur']) && strlen($_POST['createur']) > 100) {
		$errors['createur'] = "Le nom du créateur doit faire moins de 100 caractères";
	}
	
	# Si la description est trop longue
	if (!empty($_POST['descriptionActualité']) && strlen($_POST['descriptionActualité']) > 1000) {
		$errors['descriptionActualité'] = "La description de l'actualité doit faire au plus 1000 caractères";
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
	
	# S'il n'y a pas d'erreur, on ajoute l'actualité
	if (empty($errors)) {
		$req = $bdd->prepare('INSERT INTO ACTUALITES(id, idImages, createur, actualite, descriptionActualite) VALUES(:id, :idImages, :createur, :actualite, :descriptionActualite)');
		$req->execute(array(
			'id' => $_SESSION['id'],
			'idImages' => $idImages,
			'createur' => $_POST['createur'],
			'actualite' => $_POST['actualite'],
			'descriptionActualite' => $_POST['descriptionActualite']));
		$success['actualitéAjoutée'] = "L'actualité a bien été ajoutée";
	}
}
?>

<?php
# Retirer les actualités sélectionnées
if (isset($_POST['supprimerActualites']) && $_POST['supprimerActualites'] == 'Valider') {
	
	$errors = array();
	
	# Si on ne possède pas le droit de gestion des actualités (droit n°14)
	if ($droit14 == false) {
		$errors['droitActualitéManquant'] = "Vous n'êtes pas autorisé à ajouter ou retirer des actualités";
	}
	
	# S'il n'y a pas d'actualité à retirer
	if (!isset($_POST['actualites'])) {
		$errors['aucuneActualiteSelectionnee'] = "Aucune actualité n'a été sélectionnée";
	}
	
	# S'il n'y a pas d'erreur, on retire les actualités sélectionnées
	if (empty($errors)) {
		foreach($_POST['actualites'] as $valeur) {
			$req = $bdd->prepare('DELETE FROM ACTUALITES WHERE idActualites = ?');
			$req->execute(array($valeur));
		}
		$success['actualitesRetirees'] = "Les actualités sélectionnées ont bien été retirées";
	}
}
?>

<?php
# On récupère les différentes actualités
$req = $bdd->prepare('SELECT * FROM ACTUALITES ORDER BY idActualites DESC');
$req->execute();
$actualites = $req->fetchAll();
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
				Actualités
			</div>
			
			<?php if($droit14 == true): ?>
			<div id="contenu">
				<div class="plus" id ="contenuTitle">
					Créer une nouvelle actualité
				</div>
				<div id="creerRoleMiddle">
					<br>
					<form action="actualites.php" method="post">
						<label><u>Nom de l'actualité</u> :</label><br>
						<input type="text" name="actualite" /><br>
						<label><u>A propos de</u> :</label><br>
						<input type="text" name="createur" /><br>
						<label><u>Description</u> :</label><br>
						<textarea name="descriptionActualite"></textarea><br>
						<label><u>Image de l'actualité</u> :</label><br>
						<select name="image" id="selectImage">
							<option>Aucune
							<?php foreach($images as $image): ?>
							<option><?php echo $image['image']; ?>
							<?php endforeach; ?>
						</select><br>
						<div id="apercuImage"></div>
						
						<button type="submit" name="creationActualite" value="Valider">Créer cette actualité</button>
					</form>
				</div>
			</div>
			<?php endif; ?>
			
			<div id="contenu">
				<form action="actualites.php" method="post">
					<table>
						<tr>
							<th>Actualité</td>
							<th>Image</td>
							<th>Créateur</td>
							<?php if ($droit14 == true): ?>
							<th><img src="../../img/supprimer.png"></td>
							<?php endif; ?>
						</tr>
						<?php foreach ($actualites as $actualite): ?>
						<tr>
							<td style="font-size: 20px;"><?php echo text($actualite['actualite']); ?></td>
							<td><img src="<?php 
												$req = $bdd->prepare('SELECT * FROM IMAGES WHERE idImages = ?');
												$req->execute(array($actualite['idImages']));
												$img = $req->fetch();
												if ($img['image'] != NULL) {
													echo '../../img/imports/'.$img['image'];
												}
											?>" style="max-height: 150px; max-width: 250px" ></td>
							<td style="font-size: 20px;"><?php echo text($actualite['createur']); ?></td>
							<?php if ($droit14 == true): ?>
							<td><input type="checkbox" name="actualites[]" value=<?php echo $actualite['idActualites']; ?> ></td>
							<?php endif; ?>
						</tr>
						<?php endforeach; ?>
					</table>
					<?php if ($droit14 == true): ?>
					<button type="submit" name="supprimerActualites" value="Valider">Supprimer les actualités sélectionnées</button>
					<?php endif; ?>
				</form>
			</div>
		</div>

		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/admin.js"></script>
		<script type="text/javascript" src="../../js/alerte.js"></script>
	</body>
</html>