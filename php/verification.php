<!-- VERIFICATION D'UN CODE -->

<? php
session_start();
if (isset($_SESSION['id'])) {
	header ('Location: accueil.php');
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
		header('Location: accueil.php');
		exit();
	}
}
?>



<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>

		<div class="alerte rouge">
			<p>Ce lien n'est pas ou n'est plus valide.</p>
			<a href="accueil.php">Retour à l'accueil</a>
		</div>

	</body>
</html>
