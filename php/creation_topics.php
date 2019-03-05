
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="../css/formulaire.css">
                <link rel="stylesheet" type="text/css" href="../css/main.css">
	</head>
	<body>
	<?php require_once ('navigation.html') ?>
	<div class="row top-page">
			<div class="offset-md-4 col-md-3 title">
				<h1>Création d'un nouveau topic</h1>
			</div>
		</div>

		<!-- Formulaire de connexion -->
		<div class="row">
			<div class="offset-md-4 col-md-3 block">
				<form action="connexion.php" method="post">
					<label for="title">Titre du topic</label><br>
					<input type="text" name="title" required ><br>
					<label for="msg">Votre message</label><br>
					<textarea required> Message</textarea><br>
					<input type="submit" name="connexion" value="Valider" />
				</form>
			</div>
		</div>

	</body>
	<footer>
    <?php require_once ('footer.html') ?>
  </footer>
</html>
