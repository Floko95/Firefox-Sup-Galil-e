<?php require_once("inc/serveur.php"); ?>

<?php session_start(); ?>

<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="../css/main.css">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	</head>
	<body>
		<?php require_once ('navigation.html') ?>
		<div style="top:0;z-index: -100;position: sticky;">
			<video width="100%"autoplay muted loop style="margin-top: -3%">
				<source src="../img/composition_planete_tournante_compresse.mp4" type="video/mp4">
			</video> 
		</div>

		<div class="row bue-row " id="Equipe">
			<div class="col-md-12">

			</div>
		</div>
		<div class="row orange-row" id="Evenements">
			
		</div>
		<div class="row bue-row" id="Clubs">
			
		</div>

	</body>
	<footer>
		<?php require_once ('footer.html') ?>
	</footer>
</html>
