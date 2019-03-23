<?php
session_start();
if (isset($_SESSION['id'])) {
	header ('Location: index.php');
	exit();
}
?>

<?php require_once 'inc/serveur.php' ?>

<?php // Faire en sorte qu'un code dure 1h max

if (!empty($_GET['id']) && !empty($_GET['code'])) {
	$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE id = ? AND typeCode = 2');
	$req->execute(array($_GET['id']));
	$etudiant = $req->fetch();
	if ($etudiant) {
		# On vérifie que le code soit bon
		if (password_verify($_GET['code'], $etudiant['code'])) {
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

} else {
	$errors['lienMort'] = "Le lien n'est pas valide";
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
		
		<div id="formulaire-responsive" class="clearfix">
			
				
			<?php if(empty($errors['lienMort'])): ?>
				<form action="" method="post">
					<h3>Choisir un nouveau mot de passe</h3>				
					
					<!-- Affichage des erreurs -->
					<?php if(!empty($errors)): ?>
						<div class="alert alert-danger">
							<strong>Vous n'avez pas rempli le formulaire correctement.</strong>
							<ul>
								<?php foreach ($errors as $error): ?>
									<li><?= $error; ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				
					<!-- Formulaire de modification du mot de passe -->
					<div class="rang-form">
						<div class="colonne">
							<label for="mdp">Nouveau mot de passe :</label>
							<input type="password" name="mdp" placeholder="******" maxlength="30" required />
						</div>
					</div>
					
					<div class="rang-form">
						<div class="colonne">
							<label for="confirmation">Confirmation du mot de passe :</label>
							<input type="password" name="confirmation" placeholder="******" maxlength="30" required />
						</div>
					</div>
					
					<div class="rang-form">
						<div class="colonne">
							<input type="submit" name="modification" value="Valider" />
						</div>
					</div>
				</form>
			<?php else: ?>
				<div class="alert alert-danger">
					<center><strong>
						Ce lien n'est pas ou n'est plus valide.<br>
						<a href="index.php">Retour à l'accueil</a>
					</strong></center>
				</div>
			<?php endif; ?>		
		</div>	
			
	</body>
	<footer>
    	<?php require_once ('footer.html') ?>
  </footer>
</html>
