<?php
require_once 'inc/check_ban.php';
require_once 'inc/fonctions.php';
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="../css/formulaire.css">
		<link rel="stylesheet" type="text/css" href="../css/main.css">
	</head>
	<body>
	
		<?php require_once ('navigation.php'); ?>

		<?php	
		// Sélections des filières à afficher pour les boutons radio
		$forums[0] = "general";
		$reqAdmin = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants NATURAL JOIN attributionDroitsAuxRoles WHERE id = ? AND idDroits = ?');
		$reqAdmin->execute(array($_SESSION['id'], 9));
		$data = $reqAdmin->fetch();
		if ($data[0] >0) { // Si on a le droit de voir tous les forums
			$forums[1] = "INFO";
			$forums[2] = "ENER";
			$forums[3] = "CP2I";
			$forums[4] = "MACS";
			$forums[5] = "TELE";
			$forums[6] = "INST";
		
		}
		else {
			$forums[1] = $_SESSION['formation'];
			$reqrole = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants WHERE id = ? AND idRoles = 4');		//4 = role "ancien cp2i"
			$reqrole->execute(array($_SESSION['id']));
			$check_cp2i = $reqrole->fetch();
			if($_SESSION['formation'] != 'CP2I' and ($check_cp2i[0] != 0)) { // Si l'éleve n'est pas un cp2i MAIS qu'il est un ancien cp2i, alors on affiche le forum cp2i
				$forums[2] = "CP2I";
			}
		}
		?>

		<div id="formulaire-responsive" class="clearfix inscription-form">
			<form action="Topics.php" method="post">
				<h3>Créer un nouveau topic</h3>

				<div class="rang-form">
					<div class="colonne">
						<label for="title">Nom du topic :</label><br>
						<input type="text" name="title" required/>
					</div>
					<div class="colonne">
						<label for="tags">Tags (5 max, séparés par des espaces, 15 caractères par tag)</label><br>
						<input type="text" name="tags">
					</div>
					<div class="colonne">
						<label for="categorie">Catégorie du topic :</label><br>
						<?php foreach($forums as $forum):?>
							<input type="radio" name="categorie" value="<?php echo $forum;?>"/> Filière <?php echo $forum;?> <br>
						<?php endforeach;?>
					</div>
					<div class="colonne">
						<label for="msg">Votre message :</label><br>
						<textarea required name="ecriture" maxlength="2000" style="max-width:95%;width:90%; height:80px"></textarea>
					</div>
					<div class="colonne">
						<input type="submit" name="creationTopic" value="Valider" />
					</div>
				</div>
			</form>
		</div>

	</body>
</html>