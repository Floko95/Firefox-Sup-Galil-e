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
$reqDroit->execute(array($_SESSION['id'], 15));
$data = $reqDroit->fetch();
if ($data[0] > 0) {
	$droit15 = true;
} else {
	$droit15 = false;
}
?>

<?php
# Créer un nouvel article
if (isset($_POST['creationArticle']) && $_POST['creationArticle'] == 'Valider') {
	
	$errors = array();
	
	# Si on ne possède pas le droit de gestion de la boutique (droit n°15)
	if ($droit15 == false) {
		$errors['droitBoutiqueManquant'] = "Vous n'êtes pas autorisé à ajouter ou retirer des articles de la boutique";
	}
	
	# Si le nom de l'article n'est pas bon (vide, trop grand, déjà utilisé)
	if (empty($_POST['article'])) {
		$errors['article'] = "Nom de l'article manquant";
	} else if (strlen($_POST['article']) > 100) {
		$errors['article'] = "Le nom de l'article doit faire moins de 100 caractères";
	} else {
		$req = $bdd->prepare('SELECT COUNT(*) FROM BOUTIQUE WHERE article = ?');
		$req->execute(array($_POST['article']));
		$data = $req->fetch();
		if ($data[0] > 0) {
			$errors['article'] = "Le nom de l'article est déjà utilisé";
		}
	}
	
	# Si la description est trop longue
	if (!empty($_POST['descriptionArticle']) && strlen($_POST['descriptionArticle']) > 1000) {
		$errors['descriptionArticle'] = "La description de l'article doit faire au plus 1000 caractères";
	}
	
	# Si le prix n'est pas bon (vide, n'est pas un nombre, négatif ou supérieur à 1000)
	if (empty($_POST['prix'])) {
		$errors['prix'] = "Prix de l'article manquant";
	} else if (!is_numeric($_POST['prix'])) {
		$errors['prix'] = "Le prix de l'article doit être un nombre réel entre 0 et 1000 exclus";
	} else if ($_POST['prix'] >= 1000 || $_POST['prix'] <= 0) {
		$errors['prix'] = "Le prix de l'article doit être un nombre réel entre 0 et 1000 exclus";
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
	
	# S'il n'y a pas d'erreur, on ajoute l'article
	if (empty($errors)) {
		$req = $bdd->prepare('INSERT INTO BOUTIQUE(idImages, article, descriptionArticle, prix) VALUES(:idImages, :article, :descriptionArticle, :prix)');
		$req->execute(array(
			'idImages' => $idImages,
			'article' => $_POST['article'],
			'descriptionArticle' => $_POST['descriptionArticle'],
			'prix' => $_POST['prix']));
		$success['articleAjouté'] = "L'article a bien été ajouté";
	}
}
?>

<?php
# Retirer les articles sélectionnés

?>

<?php
# On récupère les différents articles
/*
$req = $bdd->prepare('SELECT * FROM BOUTIQUE ORDER BY prix DESC');
$req->execute();
$articles = $req->fetchAll();
*/
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
				Boutique
			</div>
			
			<?php if($droit15 == true): ?>
			<div id="contenu">
				<div class="plus" id ="contenuTitle">
					Ajouter un nouvel article
				</div>
				<div id="creerRoleMiddle">
					<br>
					<form action="boutique.php" method="post">
						<label><u>Nom de l'article</u> :</label><br>
						<input type="text" name="article" /><br>
						<label><u>Description</u> :</label><br>
						<textarea name="descriptionArticle"></textarea><br>
						<label><u>Prix</u> :</label><br>
						<input type="text" name="prix" /><br>
						<label><u>Image de l'article</u> :</label><br>
						<select name="image" id="selectImage">
							<option>Aucune
							<?php foreach($images as $image): ?>
							<option><?php echo $image['image']; ?>
							<?php endforeach; ?>
						</select><br>
						<div id="apercuImage"></div>
						
						<button type="submit" name="creationArticle" value="Valider">Créer cet article</button>
					</form>
				</div>
			</div>
			<?php endif; ?>
			
			<div id="contenu">
				sss
			</div>
		</div>

		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/admin.js"></script>
		<script type="text/javascript" src="../../js/alerte.js"></script>
	</body>
</html>