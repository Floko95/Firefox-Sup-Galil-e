<?php
session_start();
if (!isset($_SESSION['id'])) {
	header ('Location: index.php');
	exit();
}
?>
<?php require_once 'inc/serveur.php'; ?>
<?php 
if(isset($_POST['envoi_idee']) and isset($_POST['ideeTitre']))
{
	$errors = array();
	$date = date('Y-m-d H:i:s');

	if (trim($_POST['envoi_idee'])=='' or trim($_POST['ideeTitre'])=='') {
		$errors['champ'] = "Champ(s) vide(s). Veuillez compléter le(s) champ(s) ci-dessous.";
		
	}
	elseif (strlen($_POST['envoi_idee']) > 1000 or strlen($_POST['ideeTitre']) > 50) {
		$errors['champ'] = "Trop de caractères. 50 carac max pour le titre et 1000 Caractères max pour l'idée.";
	}
	$req = $bdd->prepare('SELECT COUNT(*) FROM idees WHERE id = :id AND DATE_FORMAT(dateIdee,"%Y-%m-%d") = :day');
		$req->execute(array(	'id' => intval($_SESSION['id']),
								'day' => $date = date('Y-m-d')
							 ));
		$compteur = $req->fetch();
;
	if ($compteur[0] > 6 ) {
		$errors['flood'] = "Vous avez suggéré trop d'idées pour aujourd'hui. Réessayez demain.";
	}	
		
		
		
	if (empty($errors))
	{
	$req = $bdd->prepare('INSERT INTO idees(id,idee,dateIdee,ideeTitre) VALUES(:id , :idee , :date, :t)');
		$req->execute(array(	'id' => intval($_SESSION['id']),
								'idee' => $_POST['envoi_idee'],
								'date' => $date,
								't'    => $_POST['ideeTitre']));
		header('Location: Topics.php');
		exit();
	}
	
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="../css/formulaire.css">
		<link rel="stylesheet" type="text/css" href="../css/main.css">
	</head>
	<body>
	<?php require_once ('navigation.php');?>
		
		
		<div id="formulaire-responsive" class="clearfix inscription-form">
			<form action="boite_a_idees.php" method="post">
				<h3>Boite à idées</h3>
				
				<!-- Affichage des erreurs -->
				<?php if(!empty($errors)): ?>
					<div class="alert alert-danger">
						<strong>Vous n'avez pas rempli le formulaire correctement.</strong>
						<ul>
							<?php foreach ($errors as $error): ?>
								<li><?= $error; ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
				
				<div class="rang-form">
					<div class="colonne">
						<label for="text">Titre de l'idée</label> <br/><br/>
						<input type="text" name="ideeTitre" placeholder="30 carac max." maxlength="50" required />
					</div>
					<div class="colonne">
						<label for="text">Votre idée (1000 caractères max.) :</label><br/> <br/> <br/>
						<textarea required name="envoi_idee" style="width:80%;height:400px" maxlength="1000" ></textarea><br>
					<input type="submit" name="idee" value="Valider"/>
					</div>
					
				</div>
			</form>
		</div>
		

					