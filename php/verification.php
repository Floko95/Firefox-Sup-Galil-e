<?php
session_start();
if (isset($_SESSION['id'])) {
	header ('Location: index.php');
	exit();
}
?>

<?php require_once 'inc/serveur.php' ?>

<?php // Faire en sorte qu'un code dure 1h max

if (!empty($_GET['id']) && !empty($_GET['code']) ) {
	$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE id = ? AND code = ? AND typeCode = 1');
	$req->execute(array($_GET['id'], $_GET['code']));
	$etudiant = $req->fetch();
	if ($etudiant) {
		# On met l'état de l'étudiant à 1 (en attente de validation de l'inscription par un admin)
		$req = $bdd->prepare('UPDATE ETUDIANTS SET etat = 1 WHERE id = ?');
		$req->execute(array($_GET['id']));

		# On supprime tous les comptes qui ont la même adresse universitaire et sont encore dans l'état 0 (en attente de validation de l'inscription par un l'étudiant)
		# Il peut en effet y avoir plusieurs entrées dans la table ETUDIANTS avec la même adresse universitaire
		# En revanche, il ne peut pas y avoir plusieurs entrées avec la même adresse universitaire et dans des états > 0
		$req = $bdd->prepare('DELETE FROM ETUDIANTS WHERE mailUniv = ? AND etat = 0');
		$req->execute(array($etudiant['mailUniv']));

		# On supprime le code pour qu'il ne puisse pas être réutilisé
		$req = $bdd->prepare('UPDATE ETUDIANTS SET code = NULL, typeCode = 0, dateMail = NULL WHERE id = ?');
		$req->execute(array($_GET['id']));

		# On connecte l'étudiant
		session_start();
		$_SESSION['id'] = $etudiant['id'];
		$_SESSION['prenom'] = $etudiant['prenom'];
		$_SESSION['nom'] = $etudiant['nom'];
		$_SESSION['formation'] = $etudiant['formation'];
		$_SESSION['promotion'] = $etudiant['promotion'];
		$_SESSION['etat'] = $etudiant['etat'];
		header('Location: index.php');
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
	
		<?php require_once ('navigation.php') ?>
		<?php require_once 'inc/erreurs.php'; ?>
		
		<div id="formulaire-responsive" class="clearfix">
			<div class="rang-form">
				<div class="colonne">
					<p>Ce lien n'est pas ou n'est plus valide.</p><br>
					<a href="index.php">Retour à l'accueil</a>
				</div>
			</div>
		</div>

	</body>
</html>
