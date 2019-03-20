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
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="../css/navigation.css">
		
		
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.0/css/all.css" integrity="sha384-aOkxzJ5uQz7WBObEZcHvV5JvRW3TUc2rNPA7pe3AwnsUohiw1Vj2Rgx2KSOkF5+h" crossorigin="anonymous">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5mdXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

	</head>
	<body>
	
		<nav>
			<ul>
				<div id="left">
					<a href="index.php" id="accueil"><li><span>Accueil</span></li></a>
					<li id="hamburger">
						<ul>
							<a href="index.php#equipe" id="h-equipe"><li><span>L'équipe</span></li></a>
							<a href="index.php#actualites" id="h-actualites"><li><span>Actualités</span></li></a>
							<a href="index.php#boutique" id="h-boutique"><li><span>Boutique</span></li></a>
							<?php if(isset($_SESSION['id'])): ?>
							<a href="Topics.php" id="h-forum"><li><span>Forum</span></li></a>
							<?php if($possedeRole == true): ?>
							<a href="admin/roles.php" id="h-admin"><li><span>Gérer</span></li></a>
							<?php endif; ?>
							<?php endif; ?>
							<a href="index.php#contact" id="h-contact"><li><span>Contact</span></li></a>
						</ul>
					</li>
					<a href="index.php#equipe" id="equipe"><li><span>L'équipe</span></li></a>
					<a href="index.php#actualites" id="actualites"><li><span>Actualités</span></li></a>
					<a href="index.php#boutique" id="boutique"><li><span>Boutique</span></li></a>
					<?php if(isset($_SESSION['id'])): ?>
					<a href="Topics.php" id="forum"><li><span>Forum</span></li></a>
					<?php if($possedeRole == true): ?>
					<a href="admin/roles.php" id="admin"><li><span>Gérer</span></li></a>
					<?php endif; ?>
					<?php endif; ?>
					<a href="index.php#contact" id="contact"><li><span>Contact</span></li></a>
				</div>
				<div id="right">
					<li href="" id="profil"><?php if(isset($_SESSION['id'])):
													$req = $bdd->prepare('SELECT nom, prenom FROM etudiants WHERE id = ?');
													$req->execute(array($_SESSION['id']));
													$data = $req->fetch();
													echo (' '.strtoupper($data[0]).' '.$data[1]);
											endif;?>
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
				<a href="deconnexion.php">Déconnexion</a>
			<?php endif; ?>
		</div>
		
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/navigation.js"></script>
	</body>
</html>