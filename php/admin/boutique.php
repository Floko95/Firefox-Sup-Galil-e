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
						<input type="text" name="nomArticle" /><br>
						<label><u>Prix</u> :</label><br>
						<input type="text" name="prix" /><br>
						<label><u>Image de l'article</u> :</label><br>
						<select>
							<option>Aucune
							<option>AAA.png2wbmp
							<option>BBB
						</select><br>
						<div id="image"></div>
						
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