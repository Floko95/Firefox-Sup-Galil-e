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
$reqDroit->execute(array($_SESSION['id'], 18));
$data = $reqDroit->fetch();
if ($data[0] > 0) {
	$droit18 = true;
} else {
	$droit18 = false;
}
?>

<?php
# Suppression d'un idée
if (isset($_POST['supprimerIdees']) && $_POST['supprimerIdees'] == 'Valider') {
	
	$errors = array();
	
	# Si on ne possède pas le droit de gestion de la boîte à idées (droit n°18)
	if ($droit18 == false) {
		$errors['droitGestionIdéesManquant'] = "Vous n'êtes pas autorisé à retirer des idées de la boîte à idées";
	}
	
	# S'il n'y a pas d'idée à retirer
	if (!isset($_POST['idees'])) {
		$errors['aucuneIdeeSelectionnee'] = "Aucune idée n'a été sélectionnée";
	}
	
	# S'il n'y a pas d'erreur, on retire les idées sélectionnées
	if (empty($errors)) {
		foreach($_POST['idees'] as $valeur) {
			$req = $bdd->prepare('DELETE FROM IDEES WHERE idIdees = ?');
			$req->execute(array($valeur));
		}
		$success['ideesRetirees'] = "Les idées sélectionnées ont bien été retirées de la boîte à idées";
	}
}
?>

<?php
# On récupère les différentes idées
$req = $bdd->prepare('SELECT idIdees, idee, dateIdee FROM IDEES ORDER BY dateIdee DESC');
$req->execute();
$idees = $req->fetchAll();
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
				Boîte à idées
			</div>
			
			<div id="contenu">
				<form action="idees.php" method="post">
					<table>
						<tr>
							<th>Idée</td>
							<th>Date</td>
							<?php if ($droit18 == true): ?>
							<th>Supprimer</td>
							<?php endif; ?>
						</tr>
						<?php foreach ($idees as $idee): ?>
						<tr style="font-size: 20px;">
							<td><?php echo $idee['idee']; ?></td>
							<td><?php echo $idee['dateIdee']; ?></td>
							<?php if ($droit18 == true): ?>
							<td><input type="checkbox" name="idees[]" value=<?php echo $idee['idIdees']; ?> ></td>
							<?php endif; ?>
						</tr>
						<?php endforeach; ?>
					</table>
					<?php if ($droit18 == true): ?>
					<button type="submit" name="supprimerIdees" value="Valider">Supprimer les idées sélectionnées</button>
					<?php endif; ?>
				</form>
			</div>
			
		</div>

		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/alerte.js"></script>
	</body>
</html>