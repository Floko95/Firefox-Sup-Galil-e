<?php session_start(); ?>
<?php require_once("inc/serveur.php"); ?>

<?php
# On récupère toutes les actualités
$req = $bdd->prepare('SELECT * FROM ACTUALITES ORDER BY idActualites DESC LIMIT 10');
$req->execute();
$actualites = $req->fetchAll();
?>

<?php 
# On récupère tous les clubs
$req = $bdd->prepare('SELECT * FROM CLUBS ORDER BY idClubs ASC');
$req->execute();
$clubs = $req->fetchAll();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="../css/footer.css">
		<link rel="stylesheet" type="text/css" href="../css/main.css">
		<link rel="stylesheet" type="text/css" href="../css/index.css">
		<link rel="stylesheet" type="text/css" href="../css/alerte.css" />
		<link rel="stylesheet" type="text/css" href="../css/formulaire.css">
	</head>
	<body>
	
		<?php require_once ('navigation.php') ?>
		
		<div style="top:0;z-index: -100;position: sticky;">
			<div id="topVideo">
				<video width="100%" autoplay muted loop style="margin-top: -3%">
					<source src="../img/composition_planete_tournante_compresse.mp4" type="video/mp4">
				</video>
			</div>
			<div id="topImage">
				<img src="../img/pic07.gif" width="100%">
			</div>
		</div>

		<?php require_once 'inc/erreurs.php'; ?>

		<div class="section fond-bleu" id="s-equipe">
			<div class="wrapper">
				<h1 class="fond-blanc color-bleu">Equipe du BDE</h1>
			</div>
			<div class="rang-form">
				<div class="quart-colonne">
					<div class="membre">
						<div class="membreRole">
							Président
						</div>
						<img src="../img/président.jpg" width=100%>
						<div class="membreNom">
							Matthieu Desir
						</div>
					</div>
				</div>
				<div class="quart-colonne">
					<div class="membre">
						<div class="membreRole">
							Vice-présidents
						</div>
						<img src="../img/vice-présidents.jpg" width=100%>
						<div class="membreNom">
							Pierre Lepagnol<br>
							Camille Traina
						</div>
					</div>
				</div>
				<div class="quart-colonne">
					<div class="membre">
						<div class="membreRole">
							Trésoriers
						</div>
						<img src="../img/trésoriers.jpg" width=100%>
						<div class="membreNom">
							Romain Fleurette<br>
							Yannis Lefevre
						</div>
					</div>
				</div>
				<div class="quart-colonne">
					<div class="membre">
						<div class="membreRole">
							Secrétaires
						</div>
						<img src="../img/secrétaires.jpg" width=100%>
						<div class="membreNom">
							Daniel Zegarra<br>
							Miora Randrianantoanina
						</div>
					</div>
				</div>
				
			</div><br>

		</div>
		
		<div class="section fond-orange" id="s-equipe">
			<div class="wrapper">
				<h1 class="fond-blanc color-orange">Actualités</h1>
			</div>
			aa	<br><br><br><br><br><br><br>
		</div>
		
		<div class="section fond-bleu" id="s-equipe">
			<div class="wrapper">
				<h1 class="fond-blanc color-bleu">Clubs</h1>
			</div>
			aa	<br><br><br><br><br><br><br><br><br><br><br><br><br><br>
		</div>
		
		<footer>
			<?php require_once ('footer.html') ?>
		</footer>
		
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/alerte.js"></script>
	</body>
</html>
