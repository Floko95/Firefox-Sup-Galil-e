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
$reqDroit->execute(array($_SESSION['id'], 19));
$data = $reqDroit->fetch();
if ($data[0] > 0) {
	$droit19 = true;
} else {
	$droit19 = false;
}
?>

<?php
# Suppression d'un score
if (isset($_POST['supprimerScores']) && $_POST['supprimerScores'] == 'Valider') {
	
	$errors = array();
	
	# Si on ne possède pas le droit de gestion du mini-jeu (droit n°19)
	if ($droit19 == false) {
		$errors['droitMinijeuIdéesManquant'] = "Vous n'êtes pas autorisé supprimer des scores";
	}
	
	# S'il n'y a pas de score à retirer
	if (!isset($_POST['scores'])) {
		$errors['aucunScoreSelectionne'] = "Aucun score n'a été sélectionné";
	}
	
	# S'il n'y a pas d'erreur, on retire les scores sélectionnés
	if (empty($errors)) {
		foreach($_POST['scores'] as $valeur) {
			$req = $bdd->prepare('DELETE FROM MINIJEU WHERE id = ?');
			$req->execute(array($valeur));
		}
		$success['scoresRetires'] = "Les scores sélectionnés ont bien été supprimés";
	}
}
?>

<?php
# Réinitialiser le minijeu
if (isset($_POST['reinitialiserMinijeu']) && $_POST['reinitialiserMinijeu'] == 'Valider') {
	
	$errors = array();
	
	# Si on ne possède pas le droit de gestion du mini-jeu (droit n°19)
	if ($droit19 == false) {
		$errors['droitMinijeuIdéesManquant'] = "Vous n'êtes pas autorisé supprimer des scores";
	}
	
	# S'il n'y a pas encore de records
	$req = $bdd->prepare('SELECT COUNT(*) FROM MINIJEU');
	$req->execute();
	$data = $req->fetch();
	if ($data[0] == 0) {
		$errors['aucunScore'] = "Il n'y a pas encore de records réalisés sur le minijeu";
	}
	
	# S'il n'y a pas d'erreur, on retire tous les scores
	if (empty($errors)) {
		$req = $bdd->prepare('DELETE FROM MINIJEU');
		$req->execute();
		$success['minijeuReinitialiser'] = "Tous les records ont bien été effacés";
	}
}
?>

<?php
# On récupère les différents scores
$req = $bdd->prepare('SELECT * FROM MINIJEU NATURAL JOIN ETUDIANTS ORDER BY score DESC LIMIT 50');
$req->execute();
$scores = $req->fetchAll();
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
				Scores du minijeu
			</div>
			
			<div id="contenu">
				<form action="mini-jeu.php" method="post">
					<table>
						<tr>
							<th>Record</td>
							<th>Prénom</td>
							<th>Nom</td>
							<th>Date</td>
							<?php if ($droit19 == true): ?>
							<th><img src="../../img/supprimer.png"></td>
							<?php endif; ?>
						</tr>
						<?php foreach ($scores as $score): ?>
						<tr style="font-size: 20px;">
							<td><?php echo text($score['score']); ?></td>
							<td><?php echo text($score['prenom']); ?></td>
							<td><?php echo text($score['nom']); ?></td>
							<td><?php echo text($score['dateScore']); ?></td>
							<?php if ($droit19 == true): ?>
							<td><input type="checkbox" name="scores[]" value=<?php echo $score['id']; ?> ></td>
							<?php endif; ?>
						</tr>
						<?php endforeach; ?>
					</table>
					<?php if ($droit19 == true): ?>
					<button type="submit" name="supprimerScores" value="Valider">Supprimer les scores sélectionnés</button>
					<?php endif; ?>
				</form>
				<form action="mini-jeu.php" method="post">
					<button type="submit" name="reinitialiserMinijeu" value="Valider">Remettre tous les records à zéro</button>
				</form>
			</div>
			
		</div>

		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/alerte.js"></script>
	</body>
</html>