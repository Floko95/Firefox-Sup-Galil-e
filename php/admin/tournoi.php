<?php
# On redirige le visiteur s'il n'a rien à faire sur cette page
session_start();
if (!isset($_SESSION['id'])) {
	header ('Location: ../index.php');
	exit();
}
else {
	require_once '../inc/serveur.php';
	$req = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants NATURAL JOIN attributionDroitsAuxRoles WHERE id = ?');
	$req->execute(array($_SESSION['id']));
	$data = $req->fetch();
	if ($data[0] == 0) {
		header ('Location: ../index.php');
		exit();
	}
}
require_once '../inc/fonctions.php';
?>

<?php
# Requête pour savoir si l'étudiant possède le droit i
$reqDroit = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants NATURAL JOIN attributionDroitsAuxRoles WHERE id = ? AND idDroits = ?');
$reqDroit->execute(array($_SESSION['id'], 16));
$data = $reqDroit->fetch();
if ($data[0] > 0) {
	$droit16 = true;
} else {
	$droit16 = false;
}
?>

<?php
# Modification des scores
if (isset($_POST['modificationTournoi']) && $_POST['modificationTournoi'] == 'Valider') {
	
	$errors = array();
	
	# Si on ne possède pas le droit de gestion du tournoi (droit n°16)
	if ($droit16 == false) {
		$errors['droitGestionTournoiManquant'] = "Vous n'êtes pas autorisé à modifier les données du tournoi inter-filières";
	}
	
	# S'il n'y a pas d'erreur, on modifie les données du tournoi
	if (empty($errors)) {
		# On met à jour le booléen de visibilité
		$req = $bdd->prepare('UPDATE TOURNOI SET visible = 0');
		$req->execute();
		foreach($_POST['visible'] as $valeur) {
			$req = $bdd->prepare('UPDATE TOURNOI SET visible = 1 WHERE filiere = ?');
			$req->execute(array($valeur));
		}
		
		# On met à jour les scores
		$req = $bdd->prepare('SELECT * FROM TOURNOI');
		$req->execute();
		$datas = $req->fetchAll();
		foreach($datas as $data) {
			if (isset ($_POST['nb'.$data['filiere']])) {
				$req = $bdd->prepare('UPDATE TOURNOI SET score = ? WHERE filiere = ?');
				$req->execute(array($_POST['nb'.$data['filiere']], $data['filiere']));
			}
		}
		$success['donneesModifiees'] = "Les scores ont bien été mis à jour";
	}
}
?>

<?php
# On récupère les différentes formations
$req = $bdd->prepare('SELECT * FROM TOURNOI ORDER BY score DESC');
$req->execute();
$formations = $req->fetchAll();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" href="../../css/admin.css" />
		<link rel="stylesheet" href="../../css/alerte.css" />
	</head>
	<body>

		<?php require_once '../navigation.php'; ?>
		<?php require_once '../inc/erreurs.php'; ?>
		<?php require_once 'inc/menu.php'; ?>
		

		<div id="page">
		
			<div id="title">
				Tournoi inter-filières
			</div>
			
			<div id="contenu">
				<form action="tournoi.php" method="post">
					<table>
						<tr>
							<th>Filière</td>
							<th>Score</td>
							<th>Visible</td>
						</tr>
						<?php foreach ($formations as $formation): ?>
						<tr style="font-size: 20px;">
							<td><?php echo $formation['filiere']; ?></td>
							<td><input type="number" name=<?php echo 'nb'.$formation['filiere']; ?> value=<?php echo $formation['score']; ?> <?php if ($droit16 == false): ?>disabled<?php endif; ?> /></td>
							<td><input type="checkbox" name="visible[]" value=<?php echo $formation['filiere']; ?> <?php if ($formation['visible'] == 1): ?>checked<?php endif; ?> <?php if ($droit16 == false): ?>disabled<?php endif; ?> ></td>
						</tr>
						<?php endforeach; ?>
						<?php if ($droit16 == true): ?>
						<tr style="font-size: 20px;">
							<td></td>
							<td><button type="submit" name="modificationTournoi" value="Valider">Valider les changements</button></td>
							<td></td>
						</tr>
						<?php endif; ?>
					</table>
				</form>
			</div>
			
		</div>

		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/alerte.js"></script>
	</body>
</html>