<?php session_start();
if (!isset($_SESSION['id'])) {
	header ('Location: ../index.php');
	exit();
} else {
	require_once 'serveur.php';
	$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE id = ?');
	$req->execute(array($_SESSION['id']));
	$etudiant = $req->fetch();
	if (!$etudiant || $etudiant['etat'] == -1) {
		session_destroy();
		session_start();
		$_SESSION['flash']['alerte'] = 'Votre compte vient tout juste d\'être banni par un administrateur, vous avez été déconnecté et ne pouvez plus vous connecter';
		header ('Location: index.php');
		exit();
	}
	$_SESSION['prenom'] = $etudiant['prenom'];
	$_SESSION['nom'] = $etudiant['nom'];
	$_SESSION['formation'] = $etudiant['formation'];
	$_SESSION['promotion'] = $etudiant['promotion'];
	$_SESSION['etat'] = $etudiant['etat'];
}

?>