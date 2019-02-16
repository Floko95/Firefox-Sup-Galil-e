<?php require_once("inc/serveur.php"); ?>

<?php session_start(); ?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	</head>
	<body>

		<?php
		if (isset($_SESSION['id'])) {
			echo '
					<a href="deconnexion.php">Deconnexion</a>
			';
		}
		else {
			echo '
			<a href="inscription.php">Inscription</a>
					<a href="connexion.php">Connexion</a>
					<a href="verification.php">Verification</a>
					<a href="reinitialisation.php">Reinitialisation</a>
					<a href="modification.php">Modification</a>
			';
		}
		?>
					

	</body>
</html>
