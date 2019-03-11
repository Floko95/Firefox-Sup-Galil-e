<!-- CONNEXION -->

<? php
session_start();
if (isset($_SESSION['id'])) {
	header ('Location: index.php');
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
			header('Location: index.php');
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
		<link rel="stylesheet" type="text/css" href="../css/formulaire.css">
                <link rel="stylesheet" type="text/css" href="../css/main.css">
	</head>
	<body>
	<?php require_once ('navigation.html') ?>
	<div class="row top-page">
			<div class="offset-md-4 col-md-3 title">
				<h1>Connexion</h1>
			</div>
		</div>

		<?php if (isset($_SESSION['flash'])): ?>
			<?php foreach($_SESSION['flash'] as $type => $message): ?>
				<p class="green">
					<?= $message; ?>
				</p>
			<?php endforeach; ?>
			<?php unset($_SESSION['flash']); ?>
		<?php endif; ?>
		
		<?php if(!empty($errors)): ?>
		<div class="row">
			<div class="offset-md-4 col-md-3 block">
				<div class="alerte rouge">
					<p>La connexion a échoué.</p>
					<ul>
						<?php foreach ($errors as $error): ?>
							<li><?= $error; ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<!-- Formulaire de connexion -->
		<div class="row">
			<div class="offset-md-4 col-md-3 block">
				<form action="connexion.php" method="post">
					<label for="mailUniv">Adresse mail universitaire</label><br>
					<input type="mail" name="mailUniv" placeholder="jean.dupont@univ-paris13.fr" required pattern="[a-z]+[0-9]?.?[a-z]+@univ-paris13.fr"/><br>
					<label for="mdp">Mot de passe</label><br>
					<input type="password" name="mdp" placeholder="********" maxlength="30" required pattern=".{8,}"/><br>
					<input type="submit" name="connexion" value="Valider" />
				</form>
				<a href="reinitialisation.php">J'ai oublié mon mot de passe</a>
			</div>
		</div>

	</body>
	<footer>
    <?php require_once ('footer.html') ?>
  </footer>
</html>
