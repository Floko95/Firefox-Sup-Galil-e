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

?>

<?php
# Suppression d'un idée

?>

<?php
# On récupère les différentes idées

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
				<form action="tournoi.php" method="post">
					<table>
						<tr>
							<th>Idée</td>
							<th>Date</td>
							<th>Supprimer</td>
						</tr>
						
					</table>
				</form>
			</div>
			
		</div>

		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/alerte.js"></script>
	</body>
</html>