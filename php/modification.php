<!-- INSERTION D'UN NOUVEAU MOT DE PASSE -->

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

				header('Location: connexion.php');
				exit('Le mot de passe a bien été modifié, vous pouvez désormais vous connecter');
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
	</head>
	<body>

		<h1>Choisir un nouveau mot de passe</h1>

		<?php if(empty($errors['lienMort'])): ?>
		<?php if(!empty($errors)): ?>
		<div class="alerte rouge">
			<p>La modification du mot de passe a échoué.</p>
			<ul>
				<?php foreach ($errors as $error): ?>
					<li><?= $error; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>

		<!-- Formulaire de modification du mot de passe -->
		<form action="" method="post">
			<input type="password" name="mdp" placeholder="Mot de passe" maxlength="30" required /><br>
			<input type="password" name="confirmation" placeholder="Mot de passe" maxlength="30" required /><br>
			<input type="submit" name="modification" value="Valider" />
		</form>

		<?php else: ?>
		<div class="alerte rouge">
			<p>Ce lien n'est pas ou n'est plus valide.</p>
			<a href="accueil.php">Retour à l'accueil</a>
		</div>
		<?php endif; ?>

	</body>
</html>
