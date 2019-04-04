
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="../css/formulaire.css">
		<link rel="stylesheet" type="text/css" href="../css/main.css">
	</head>
	<body>
	<?php require_once 'inc/serveur.php' ;?>
	<?php require_once 'inc/check_ban.php'; ?>
	<?php require_once ('navigation.php'); ?>


	<?php
			
			
			
	//sélections des fillières à afficher pour les boutons radio
	$forums[0] = "general";

	$reqAdmin = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants NATURAL JOIN attributionDroitsAuxRoles WHERE id = ? AND idDroits = ?');
	$reqAdmin->execute(array($_SESSION['id'], 9));
	$data = $reqAdmin->fetch();


	if ($data[0] >0)//si on est un admin
	{
		$forums[1] = "INFO";
		$forums[2] = "ENER";
		$forums[3] = "CP2I";
		$forums[4] = "MACS";
		$forums[5] = "TELE";
		$forums[6] = "INST";
	
	}

	else
	{
		$forums[1] = $_SESSION['formation'];


		$reqrole = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants  WHERE id = ? AND idRoles = 4');		//4 = role "ancien cp2i"
		$reqrole->execute(array($_SESSION['id']));
		$check_cp2i = $reqrole->fetch();


		if($_SESSION['formation'] != 'CP2I' and ($check_cp2i[0] != 0) )
		{
			$forums[2] = "CP2I";
		}// si l'éleve n'est pas un cp2i MAIS qu'il est un ancien cp2i, alors on affiche le forum cp2i
}

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
					<label for="tags">Tags (5 max,séparés par des espaces, 15 caractères par tag)</label><br>
					<input type="text" name="tags" required ><br>
					<label for="categorie">Catégorie du topic</label><br>
					
					<?php foreach($forums as $forum):?>
					<input type="radio" name="categorie" value="<?php echo $forum;?>"/> Fillière <?php echo $forum;?><br> 
					<?php endforeach;?>
					
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
