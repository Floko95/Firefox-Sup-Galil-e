<!-- REINITIALISATION D'UN MOT DE PASSE -->

<? php
session_start();
if (isset($_SESSION['id'])) {
	header ('Location: index.php');
	exit();
}
?>

<?php require_once 'inc/serveur.php' ?>

<?php
if (isset($_POST['reinitialisation']) && $_POST['reinitialisation'] == 'Valider') {

	$errors = array();

	if (empty($_POST['mailUniv'])) {
		$errors['mailUniv'] = "Adresse mail universitaire manquante";
	} else {
		$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE mailUniv = ? AND etat > 0');
		$req->execute(array($_POST['mailUniv']));
		$etudiant = $req->fetch();
		if (!$etudiant) {
			$errors['mailUniv'] = "Adresse mail universitaire invalide";
		} else {
			# On génère un code de confirmation
			require_once 'inc/fonctions.php';
			$code = chaineAleatoire(50);
			$date = date('Y-m-d H:i:s');

			# On insère ce code dans la table étudiants
			$req = $bdd->prepare('UPDATE ETUDIANTS SET code = ?, typeCode = ?, dateMail = ? WHERE mailUniv = ?');
			$req->execute(array($code, 2, $date, $_POST['mailUniv']));

			# On envoie un mail à l'étudiant contenant le code de validation de réinitialisation de mot de passe
			$id = $etudiant['id'];
			mail($_POST['mailUniv'], "BDE : réinitialisation de votre mot de passe", "Afin de choisir un nouveau mot de passe, veuillez cliquer sur ce lien\n\nhttp:://modification.php?id=$id&code=$code");

			# On redirige l'étudiant vers la page d'accueil
			session_start();
			$_SESSION['flash']['alerte'] = 'Un lien de confirmation vous a été envoyé sur votre adresse mail universitaire afin de choisir un nouveau mot de passe';
			header('Location: index.php');
			exit();
		}
	}

}
?>



<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="../css/main.css">
		<link rel="stylesheet" type="text/css" href="../css/formulaire.css">
	</head>
	<body>
		<?php require_once ('navigation.html') ?>
		<div class="row top-page">
			<div class="offset-md-4 col-md-3 title">
				<h1>Réinitialiser son mot de passe</h1>
			</div>
		</div>

		<?php if(!empty($errors)): ?>
		<div class="row">
			<div class="offset-md-4 col-md-3 block">
				<div class="alerte rouge">
					<p>La réinitialisation du mot de passe a échoué.</p>
					<ul>
						<?php foreach ($errors as $error): ?>
							<li><?= $error; ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<!-- Formulaire de réinitialisation du mot de passe -->
		<div class="row">
			<div class="offset-md-4 col-md-3 block">
				<form action="" method="post">
					<label for="mailUniv">Adresse mail universitaire</label><br>
					<input type="mail" name="mailUniv" placeholder="jean.dupont@univ-paris13.fr" required /><br>
					<input type="submit" name="reinitialisation" value="Valider" />
				</form>
			</div>
		</div>
	</body>
	<footer>
    	<?php require_once ('footer.html') ?>
  </footer>
</html>
