<!-- CONNEXION -->

<? php
session_start();
if (isset($_SESSION['id'])) {
	header ('Location: accueil.php');
	exit();
}
?>

<?php require_once 'inc/serveur.php' ?>

<?php
if (isset($_POST['connexion']) && $_POST['connexion'] == 'Valider') {

	$errors = array();

	if (empty($_POST['mailUniv'])) {
		$errors['mailUniv'] = "Adresse mail universitaire manquante";
	}

	if (empty($_POST['mdp'])) {
		$errors['mdp'] = "Mot de passe manquant";
	}

	if (empty($errors)) {
		$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE mailUniv = ? AND etat > 0');
		$req->execute(array($_POST['mailUniv']));
		$etudiant = $req->fetch();
		if ($etudiant && password_verify($_POST['mdp'], $etudiant['mdp'])) {
			session_start();
			$_SESSION['id'] = $etudiant['id'];
			$_SESSION['prenom'] = $etudiant['prenom'];
			$_SESSION['nom'] = $etudiant['nom'];
			$_SESSION['formation'] = $etudiant['formation'];
			$_SESSION['promotion'] = $etudiant['promotion'];
			$_SESSION['etat'] = $etudiant['etat'];
			header('Location: accueil.php');
			exit();
		} else {
			$errors['identifiants'] = "Identifiants invalides";
		}
	}

}
?>



<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>

		<h1>Connexion</h1>

		<?php if(!empty($errors)): ?>
		<div class="alerte rouge">
			<p>La connexion a échoué.</p>
			<ul>
				<?php foreach ($errors as $error): ?>
					<li><?= $error; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>

		<!-- Formulaire de connexion -->
		<form action="connexion.php" method="post">
			<input type="mail" name="mailUniv" placeholder="Adresse universitaire" required /><br>
			<input type="password" name="mdp" placeholder="Mot de passe" maxlength="30" required /><br>
			<input type="submit" name="connexion" value="Valider" />
		</form>
		<a href="reinitialisation.php">J'ai oublié mon mot de passe</a>

	</body>
</html>
