<!-- INSERTION D'UN NOUVEAU MOT DE PASSE -->

<? php
session_start();
if (isset($_SESSION['id'])) {
	header ('Location: index.php');
	exit();
}
?>

<?php require_once 'inc/serveur.php' ?>

<?php // Faire en sorte qu'un code dure 1h max

if (!empty($_GET['id']) && !empty($_GET['code']) ) {
	$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE id = ? AND code = ? AND typeCode = 2');
	$req->execute(array($_GET['id'], $_GET['code']));
	$etudiant = $req->fetch();
	if ($etudiant) {
		if (isset($_POST['modification']) && $_POST['modification'] == 'Valider') {

			$errors = array();

			if (empty($_POST['mdp']) || strlen($_POST['mdp']) < 6) {
				$errors['mdp'] = "Le mot de passe doit contenir au minimum 6 caractères";
			} elseif ($_POST['mdp'] != $_POST['confirmation']){
				$errors['mdp'] = "Les deux mots de passe sont différents";
			} else {
				# On met à jour le mot de passe de l'étudiant
				$mdp = password_hash($_POST['mdp'], PASSWORD_BCRYPT);
				$req = $bdd->prepare('UPDATE ETUDIANTS SET mdp = ? WHERE id = ?');
				$req->execute(array($mdp, $_GET['id']));

				# On supprime le code pour qu'il ne puisse pas être réutilisé
				$req = $bdd->prepare('UPDATE ETUDIANTS SET code = NULL, typeCode = 0, dateMail = NULL WHERE id = ?');
				$req->execute(array($_GET['id']));

				session_start();
				$_SESSION['flash']['alerte'] = 'Le mot de passe a bien été modifié, vous pouvez désormais vous connecter';
				header('Location: connexion.php');
				exit();
			}

		}
	} else {
		$errors['lienMort'] = "Le lien n'est pas valide";
	}

} else {
	$errors['lienMort'] = "Le lien n'est pas valide";
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
				<h1>Choisir un nouveau mot de passe</h1>
			</div>
		</div>
		<?php if(empty($errors['lienMort'])): ?>
			<div class="row">
				<?php if(!empty($errors)): ?>						
					<div class="offset-md-4 col-md-3">
						<div class="alerte rouge">
							<p>La modification du mot de passe a échoué.</p>
							<ul>
								<?php foreach ($errors as $error): ?>
									<li><?= $error; ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				<?php endif; ?>
				
		
		<!-- Formulaire de modification du mot de passe -->
			<div class="row">
				<div class="offset-md-4 col-md-3 block">
					<form action="" method="post">
						<label for="mdp">Mot de passe</label><br>
						<input type="password" name="mdp" placeholder="********" maxlength="30" required /><br>
						<label for="confirmation">Confirmation du mot de passe</label><br>
						<input type="password" name="confirmation" placeholder="********" maxlength="30" required /><br>
						<input type="submit" name="modification" value="Valider" />
					</form>
				</div>
			</div>
		<?php else: ?>
		<div class="row">
			<div class="offset-md-4 col-md-3 block alerte rouge">
				<p>Ce lien n'est pas ou n'est plus valide.</p>
				<a href="index.php">Retour à l'accueil</a>
			</div>
		</div>
		<?php endif; ?>

	</body>
	<footer>
    	<?php require_once ('footer.html') ?>
  </footer>
</html>
