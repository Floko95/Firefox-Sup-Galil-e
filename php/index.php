<?php session_start(); ?>
<?php 
require_once("inc/serveur.php");
require_once 'inc/fonctions.php';
 ?>

<?php
# On récupère toutes les actualités
$req = $bdd->prepare('SELECT * FROM ACTUALITES NATURAL LEFT JOIN IMAGES ORDER BY idActualites DESC LIMIT 15');
$req->execute();
$actualites = $req->fetchAll();
?>

<?php 
# On récupère tous les clubs
$req = $bdd->prepare('SELECT * FROM CLUBS ORDER BY idClubs ASC LIMIT 20');
$req->execute();
$clubs = $req->fetchAll();
?>

<?php 
# On récupère les données du tournoi
$req = $bdd->prepare('SELECT * FROM TOURNOI ORDER BY score DESC');
$req->execute();
$clubs = $req->fetchAll();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="/css/main.css">
		<link rel="stylesheet" type="text/css" href="/css/index.css">
		<link rel="stylesheet" type="text/css" href="/css/alerte.css" />
		<link rel="stylesheet" type="text/css" href="/css/formulaire.css">
		<link rel="stylesheet" type="text/css" href="/css/footer.css">
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
		
		<div class="section fond-orange" id="s-actualites">
			<div class="wrapper">
				<h1 class="fond-blanc color-orange">Actualités</h1>
			</div>
			<?php foreach($actualites as $actualite): ?>
			
				<div class="rang-form">
					<div class="colonne">
						<div class="actualite" id=<?php echo 'actualite'.$actualite['idActualites']; ?>>
							<h2><?php echo $actualite['actualite']; ?></h2>
							<div class="actualiteContenu" id=<?php echo 'contenuactualite'.$actualite['idActualites']; ?>>
								<?php if (!empty($actualite['idImages'])): ?>
									<div class="actualiteImage">
										<?php echo '<img src=../img/imports/'.$actualite['image'].'>'; ?>
									</div>
								<?php endif; ?>
								<?php if (!empty($actualite['descriptionActualite'])): ?>
									<div class="actualiteDescription">
										<?php echo text($actualite['descriptionActualite']); ?>
									</div>
								<?php endif; ?>
							</div>
							<div class="actualiteCreateur">
								<?php 
								if (!empty(text($actualite['createur']))) {
									echo 'par : '.text($actualite['createur']); 
								} else {
									$req = $bdd->prepare('SELECT prenom, nom FROM ETUDIANTS WHERE id = ?');
									$req->execute(array($actualite['id']));
									$data = $req->fetch();
									echo 'par : '.$data['prenom'].' '.$data['nom'];
								}
								?>
							</div>
						</div>
					</div>
				</div>
			
			<?php endforeach; ?>
		</div>
		
		<div class="section fond-bleu" id="s-clubs">
			<div class="wrapper">
				<h1 class="fond-blanc color-bleu">Clubs</h1>
			</div>
			aa	<br><br><br><br><br><br><br><br><br><br><br><br><br><br>
		</div>
		
		<footer id="s-contact">
			<?php require_once ('footer.html') ?>
		</footer>
		
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/alerte.js"></script>
		<script type="text/javascript" src="../js/index.js"></script>
	</body>
</html>
