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
$reqDroit->execute(array($_SESSION['id'], 17));
$data = $reqDroit->fetch();
if ($data[0] > 0) {
	$droit17 = true;
} else {
	$droit17 = false;
}
?>

<?php
# Importer une nouvelle image
$dossier = '../../img/imports';
if (isset($_POST['importImage']) && $_POST['importImage'] == 'Valider') {
	
	$errors = array();
	
	if (is_uploaded_file( $_FILES["image"]["tmp_name"]) && $_FILES["image"]["error"] === 0) {
		# Si on ne possède pas le droit d'ajout d'image (droit n°17)
		if ($droit17 == false) {
			$errors['droitImageManquant'] = "Vous n'êtes pas autorisé à ajouter ou retirer des images";
		}
		
		# Si le nom est trop long
		$image = basename($_FILES['image']['name']);
		if (strlen($image) > 35) {
			$errors['tailleNomImage'] = "Le nom de l'image de doit pas excéder 30 caractères";
		}
		
		# Si une image a déja ce nom
		if (file_exists(($dossier.'/'.$image))) {
			$errors['imageExistante'] = "Une image possède déjà ce nom";
		}

		# Si la taille de l'image est trop grande
		if ($_FILES["image"]["size"] > 1048576) {
			$errors['imageTropLourde'] = "L'image ne doit pas dépasser 1Mo";
		}
		
		# Si l'extension n'est pas bonne
		$extensions = array('.png', '.gif', '.jpg', '.jpeg');
		$extension = strrchr($_FILES['image']['name'], '.');
		if (!in_array($extension, $extensions)) {
			$errors['extensionInterdite'] = 'Vous devez uploader une image de type png, gif, jpg ou jpeg';
		}
		
		# S'il n'y a pas d'erreur, on ajouter l'image
		if (empty($errors)) {
			# On formate le nom de l'image
			$image = strtr($image,
				'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
				'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
			$image = preg_replace('/([^.a-z0-9]+)/i', '-', $image);
			# On crée le dossier contenant les images si il n'existe pas encore
			if (!file_exists($dossier)) {
				mkdir($dossier);
			}
			# On insère l'image dans le dossier
			if (move_uploaded_file($_FILES['image']['tmp_name'], $dossier.'/'.$image)) {
				$req = $bdd->prepare('INSERT INTO IMAGES(image) VALUES(:image)');
				$req->execute(array(
					'image' => $image));
				$req->closeCursor();
				$success['imageInseree'] = "L'image a bien été ajoutée à la banque d'images disponible";
			} else {
				$errors['erreurSurvenue'] = 'Une erreur est survenue, veuillez réessayer';
			}
		}
	} else {
		$errors['imageManquante'] = 'Vous devez sélectionner une image';
	}
}
?>

<?php
# Effacer les images sélectionnées
if (isset($_POST['supprimerImages']) && $_POST['supprimerImages'] == 'Valider') {
	
	$errors = array();
	
	# Si on ne possède pas le droit de gestion de la banque d'images (droit n°17)
	if ($droit17 == false) {
		$errors['droitImageManquant'] = "Vous n'êtes pas autorisé à ajouter ou retirer des images";
	}
	
	# S'il n'y a pas d'image à supprimer
	if (!isset($_POST['images'])) {
		$errors['aucuneImageSelectionnee'] = "Aucune image n'a été sélectionnée";
	}
	
	# S'il n'y a pas d'erreur, on retire les images sélectionnées
	if (empty($errors)) {
		foreach($_POST['images'] as $valeur) {
			# On retire l'image de la base de données
			$req = $bdd->prepare('SELECT * FROM IMAGES WHERE idImages = ?');
			$req->execute(array($valeur));
			$nom = $req->fetch();
			$req = $bdd->prepare('DELETE FROM IMAGES WHERE idImages = ?');
			$req->execute(array($valeur));
			# On retire l'image du dossier
			if (file_exists($dossier.'/'.$nom['image'])) {
				chmod($dossier.'/'.$nom['image'], 0777);
				unlink($dossier.'/'.$nom['image']);
			}
		}
		$success['imagesRetirees'] = "Les images sélectionnées ont bien été retirées de la banque d'images";
	}
}
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
				Banque d'images
			</div>
			
			<?php if($droit17 == true): ?>
			<div id="contenu">
				<div class="plus" id ="contenuTitle">
					Importer une nouvelle image
				</div>
				<div id="creerRoleMiddle">
					<br>
					<form action="images.php" method="post" enctype="multipart/form-data">
						<label><u>Image à importer</u> :</label><br>
						<input type="file" name="image"><br>
						
						<button type="submit" name="importImage" value="Valider">Importer cette image</button>
					</form>
				</div>
			</div>
			<?php endif; ?>
			
			<div id="contenu">
				<form action="images.php" method="post">
					<table>
						<tr>
							<th>Nom</td>
							<th>Image</td>
							<?php if ($droit17 == true): ?>
							<th>Supprimer</td>
							<?php endif; ?>
						</tr>
						<?php foreach($images as $image): ?>
						<tr style="font-size: 20px;">
							<td><?php echo text($image['image']); ?></td>
							<td>
								<?php 
								if (file_exists(($dossier.'/'.$image['image']))) {
									echo '<a href='.$dossier.'/'.$image['image'].' target="_blank"><img src='.$dossier.'/'.$image['image'].' style="max-height: 125px; max-width: 300px; cursor: zoom-in" ></a>';
								} else {
									echo 'Image introuvable';
								}
								?>
							</td>
							<?php if ($droit17 == true): ?>
							<td><input type="checkbox" name="images[]" value=<?php echo $image['idImages']; ?> ></td>
							<?php endif; ?>
						</tr>
						<?php endforeach; ?>
					</table>
					<?php if ($droit17 == true): ?>
					<button type="submit" name="supprimerImages" value="Valider">Supprimer les images sélectionnées</button>
					<?php endif; ?>
				</form>
			</div>
		</div>

		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/admin.js"></script>
		<script type="text/javascript" src="../../js/alerte.js"></script>
	</body>
</html>