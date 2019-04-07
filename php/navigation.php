<?php
# Si on possède un droit, on pourra accéder à la page d'administration
if(isset($_SESSION['id'])) {
	$req = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants NATURAL JOIN attributionDroitsAuxRoles WHERE id = ?');
	$req->execute(array($_SESSION['id']));
	$data = $req->fetch();
	if ($data[0] > 0) {
		$possedeRole = true;
	}
	else {
		$possedeRole = false;
	}
}

require_once 'inc/fonctions.php';
?>


<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="/Firefox-Sup-Galil-e/css/navigation.css">

	</head>
	<body>
	
		<nav>
			<ul>
				<div id="left">
					<a href="/Firefox-Sup-Galil-e/php/index.php" id="accueil"><li><span>Accueil</span></li></a>
					<li id="hamburger">
						<ul>
							<a href="/Firefox-Sup-Galil-e/php/index.php#equipe" id="h-equipe"><li><span>L'équipe</span></li></a>
							<a href="/Firefox-Sup-Galil-e/php/index.php#actualites" id="h-actualites"><li><span>Actualités</span></li></a>
							<a href="/Firefox-Sup-Galil-e/php/index.php#clubs" id="h-clubs"><li><span>Clubs</span></li></a>
							<?php if(isset($_SESSION['id'])): ?>
							<a href="/Firefox-Sup-Galil-e/php/Topics.php" id="h-forum"><li><span>Forum</span></li></a>
							<a href="/Firefox-Sup-Galil-e/php/index.php#boutique" id="h-boutique"><li><span>Boutique</span></li></a>
							<?php if($possedeRole == true): ?>
							<a href="/Firefox-Sup-Galil-e/php/admin/roles.php" id="h-admin"><li><span>Gérer</span></li></a>
							<?php endif; ?>
							<?php endif; ?>
							<?php if(!isset($_SESSION['id'])): ?>
							<a href="/Firefox-Sup-Galil-e/php/inscription.php" id="h-inscription"><li><span>Créer un compte</span></li></a>
							<?php endif; ?>
							<a href="/Firefox-Sup-Galil-e/php/index.php#contact" id="h-contact"><li><span>Contact</span></li></a>
						</ul>
					</li>
					<a href="/Firefox-Sup-Galil-e/php/index.php#equipe" id="equipe"><li><span>L'équipe</span></li></a>
					<a href="/Firefox-Sup-Galil-e/php/index.php#actualites" id="actualites"><li><span>Actualités</span></li></a>
					<a href="/Firefox-Sup-Galil-e/php/index.php#clubs" id="clubs"><li><span>Clubs</span></li></a>
					<?php if(isset($_SESSION['id'])): ?>
					<a href="/Firefox-Sup-Galil-e/php/Topics.php" id="forum"><li><span>Forum</span></li></a>
					<a href="/Firefox-Sup-Galil-e/php/index.php#boutique" id="boutique"><li><span>Boutique</span></li></a>
					<?php if($possedeRole == true): ?>
					<a href="/Firefox-Sup-Galil-e/php/admin/roles.php" id="admin"><li><span>Gérer</span></li></a>
					<?php endif; ?>
					<?php endif; ?>
					<?php if(!isset($_SESSION['id'])): ?>
						<a href="/Firefox-Sup-Galil-e/php/inscription.php" id="inscription"><li><span>Créer un compte</span></li></a>
					<?php endif; ?>
					<a href="/Firefox-Sup-Galil-e/php/index.php#contact" id="contact"><li><span>Contact</span></li></a>
				</div>
				<div id="right">
					<li id="profil">
						<?php if(isset($_SESSION['prenom'])): ?>
							<div id="bonjour">
								<?php echo 'Bonjour '.text($_SESSION['prenom']); ?>
							</div>
						<?php endif;?>
					</li>
				</div>
			</ul>
		</nav>
	
	
		<div id="connexion">
			<?php if(!isset($_SESSION['id'])): ?>
				<form action="connexion.php" method="post">
					<input type="mail" name="mailUniv" placeholder="Adresse universitaire" required /><br>
					<input type="password" name="mdp" placeholder="Mot de passe" maxlength="30" required />
					<a href="inscription.php">S'inscrire</a><br>
					<a href="reinitialisation.php">Mot de passe oublié</a><br>				
					<input type="submit" name="connexion" value="Valider" />						
				</form>
			<?php else: ?>
				<a href="/Firefox-Sup-Galil-e/php/profil.php">Voir mon profil</a><br><br>
				<a href="/Firefox-Sup-Galil-e/php/deconnexion.php">Déconnexion</a>
			<?php endif; ?>
		</div>
		
		<script type="text/javascript" src="/Firefox-Sup-Galil-e/js/jquery.js"></script>
		<script type="text/javascript" src="/Firefox-Sup-Galil-e/js/navigation.js"></script>
	</body>
</html>