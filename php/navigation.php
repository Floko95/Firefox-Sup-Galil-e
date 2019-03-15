<!DOCTYPE html>

<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link rel="stylesheet" type="text/css" href="../css/navigation.css">
	<link rel="stylesheet" type="text/css" href="../css/main.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.0/css/all.css" integrity="sha384-aOkxzJ5uQz7WBObEZcHvV5JvRW3TUc2rNPA7pe3AwnsUohiw1Vj2Rgx2KSOkF5+h" crossorigin="anonymous">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5mdXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>


<nav>
	<div class="row nav">
			<div class="col-sm-1 nav-tab "><a href="index.php"><span class="fa fa-home"></span> Accueil</a></div>
			<div class="col-sm-1 nav-tab "><a href="#Equipe"><span class="fas fa-users"></span> L'équipe</a></div>
			<div class="col-sm-1 nav-tab "><a href="#Clubs"><span class="fas fa-handshake"></span> Clubs</a></div>
			<div class="col-sm-1 nav-tab "><a href="#Evenements"><span class="fas fa-calendar-alt"></span> Evénements</a></div>
			<div class="col-sm-1 nav-tab "><a href="#a"><span class="fas fa-shopping-cart"></span> Boutique</a></div>
			<?php if(isset($_SESSION['id'])): ?>
			<div class="col-sm-1 nav-tab "><a href="Topics.php"><span class="fas fa-comments"></span> Forum</a></div>
			<div class="col-sm-1 nav-tab "><a href="admin/roles.php"><span class="fas fa-comments"></span> Admin</a></div>
			<?php endif; ?>
			<div class="col-sm-1 nav-tab " id="contact-tab"><a href="#a"><span class="fas fa-envelope"></span> Contact</a></div>
			<div class="col-sm-1" id="facebook-tab"><a href="https://www.facebook.com/BdeSupGalilee/"><span class="fab fa-facebook-f"  id="facebook_logo"></span></a></div>
			<?php if(isset($_SESSION['id'])):?>
				<div class="offset-sm-2 col-sm-1" id="connect-tab">
			<?php else:?>
				<div class="offset-sm-3 col-sm-2" id="connect-tab">
			<?php endif;?>
					<a  onclick="connexion()"><span class="fas fa-user-circle"></span></a>
					
					<div id="connexion">
							<?php if(!isset($_SESSION['id'])): ?>
							<form action="connexion.php" method="post">
								<input type="mail" name="mailUniv" placeholder="Adresse universitaire" required /><br>
								<input type="password" name="mdp" placeholder="Mot de passe" maxlength="30" required /><br>
								<input type="submit" name="connexion" value="Valider" /><br>
								<a href="inscription.php">S'inscrire</a>
							</form>
							<?php else: ?>
								<a href="deconnexion.php">Déconnexion</a>
							<?php endif; ?>

					</div>
				
			</div>
	</div>

</nav>



<script>
i = false;
function connexion(){
	if (i == false){
		document.getElementById("connexion").style.display = "block";
		i = true;
	}
	else{
		document.getElementById("connexion").style.display = "none";
		i = false;
	}
}

</script>


</body>
</html>