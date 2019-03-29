<?php session_start(); ?>
<?php require_once("inc/serveur.php"); ?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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

		<div class="row blue-row " id="Equipe">
			<div class="col-md-12">
				<div class="row">
					<div class="offset-md-4 col-md-3 title">
						L'équipe du BDE SupGalilée
					</div>
				</div>
				<div class="row">
					<div class="col-md-1 offset-md-2">
						<img src="../img/face_test.jpg" height=200px width=200px alt="Presidente" class="photo-individu img-circle">
					</div>
					<div class="col-md-1 offset-md-2">
						<img src="../img/face_test.jpg" alt="Presidente" height=200px width=200px class="photo-individu">
					</div>
					<div class="col-md-1 offset-md-2">
						<img src="../img/face_test.jpg" alt="Presidente" height=200px width=200px class="photo-individu">
					</div>
				</div>
				<div class="row ">
					<div class="col-md-1 offset-md-2 desc_img">
						Président(e)
					</div>
					<div class="col-md-1 offset-md-2 desc_img">
						Trésorier
					</div>
					<div class="col-md-1 offset-md-2 desc_img">
						Secrétaire
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<img src="../img/group_test.jpg" alt="Equipe" class="photo-group">
					</div>
				</div>
			</div>
		</div>
		
		<div class="row orange-row" id="Actualité">	
			<div class="col">
				<div class="row title actu-title">
					<div class="col"> Actualités</div>
				</div>
				<div class="row">
					<div class="offset-md-1 col-md-10">
						<?php 
							$req = $bdd->prepare('SELECT creator, title, content FROM actualite ORDER BY idActualite DESC');
							$req->execute();
							for($i=0; $i<4;$i++):
								$actu = $req->fetch();
								if(isset($actu['title'])):
						?>
						<div class="row actualite">
							<div class="col">
								<div class="row">
									<div class="col-md-2 creator">
										<?php echo $actu['creator']; ?> :
									</div>
									<div class="col-md-9 title">
										<?php echo $actu['title']; ?>
									</div>
								</div>
								<div class="row">
									<div class="offset-md-2 col-ms-9 content"><?php echo $actu['content']; ?></div>
								</div>
								
								
								
							</div>
						</div>
							<?php
								endif; 
							endfor;?>
					</div>
				</div>
			</div>
		</div>

		<div class="row blue-row" id="Clubs">
			<div class="col">
				<div class="row title">
					<div class="offset-md-4 col-md-4">Clubs & Associations</div>
				</div>
				<!--Club-->
				<div class="row">
					<div class="offset-md-1 col-md-10 club ">
						<div class="row club-title">
							<div class="col">
								Fablab de SupGalilée
							</div>
						</div>
						<div class="row club-content">
							<div class="col-md-4">
								<img src="../img/club_test.jpg" alt="Club_BlaBla" class="photo-club">
							</div>
							<div class="col-md-8 club-message">
								Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
								Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
								Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. 
								Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/alerte.js"></script>
	</body>
	<footer>
		<?php require_once ('footer.html') ?>
	</footer>
</html>
