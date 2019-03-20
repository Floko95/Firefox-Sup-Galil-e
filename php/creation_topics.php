
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="../css/formulaire.css">
		<link rel="stylesheet" type="text/css" href="../css/main.css">
	</head>
	<body>
	<?php require_once 'inc/serveur.php' ;?>
	<?php session_start(); ?>
	<?php require_once ('navigation.php'); ?>


	<?php
		$req = $bdd->prepare('SELECT formation FROM etudiants WHERE id = :id');
			$req->bindValue(':id',intval($_SESSION['id']));
			$req->execute();
			$f = $req->fetch();
			?>

	<div class="row top-page">
			<div class="offset-md-4 col-md-3 title">
				<h1>Création d'un nouveau topic</h1>
			</div>
		</div>

		<!-- Formulaire de connexion -->
		<div class="row">
			<div class="offset-md-4 col-md-3 block">
				<form action="Topics.php" method="post">
					<label for="title">Titre du topic</label><br>
					<input type="text" name="title" required/><br>
					<label for="tags">Tags</label><br>
					<input type="text" name="tags" required ><br
					<label for="categorie">Catégorie du topic</label><br>
					<input type="radio" name="categorie" value="general"/>Général<br>
					<input type="radio" name="categorie" value="filliere"/> Fillière <?php echo $f['formation'];?><br>
					<br>
					<label for="msg">Votre message</label><br>
					<textarea required name="ecriture"> Message</textarea><br>
					<input type="submit" name="connexion" value="Valider" />
				</form>
			</div>
		</div>

	</body>
	<footer>
    <?php require_once ('footer.html') ?>
  </footer>
</html>
