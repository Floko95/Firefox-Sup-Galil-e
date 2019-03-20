<?php
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
		$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE mailUniv = ?');
		$req->execute(array($_POST['mailUniv']));
		$etudiant = $req->fetch();
		if ($etudiant && password_verify($_POST['mdp'], $etudiant['mdp'])) {
			# Si l'étudiant est banni
			if ($etudiant['etat'] == -1) {
				$errors['banni'] = "Vous avez été banni, vous ne pouvez plus vous connecter sur ce compte";
			}
			# Si l'étudiant n'a pas encore validé son adresse mail
			elseif ($etudiant['etat'] == 0) {
				$errors['enattente'] = "Vous devez d'abord valider votre adresse mail universitaire avant de pouvoir vous connecter";
			}
			else {
				session_start();
				$_SESSION['id'] = $etudiant['id'];
				$_SESSION['prenom'] = $etudiant['prenom'];
				$_SESSION['nom'] = $etudiant['nom'];
				$_SESSION['formation'] = $etudiant['formation'];
				$_SESSION['promotion'] = $etudiant['promotion'];
				if ($etudiant['etat'] == 1) {
					$_SESSION['flash']['alerte'] = 'Votre inscription n\'a pas encore été validée par un administrateur.<br>Par conséquent, vous ne pourrez pas poster de message sur le forum';
				}
				header('Location: index.php');
				exit();
			}
		} else {
			$errors['identifiants'] = "Identifiants invalides";
		}
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
	
		<div id="formulaire-responsive" class="clearfix">
			<form action="connexion.php" method="post">
				<h3>Se connecter</h3>
				
				<!-- Affichage des erreurs -->
				<?php if (isset($_SESSION['flash'])): ?>
					<?php foreach($_SESSION['flash'] as $type => $message): ?>
						<p class="green">
							<?= $message; ?>
						</p>
					<?php endforeach; ?>
					<?php unset($_SESSION['flash']); ?>
				<?php endif; ?>
				
				<?php if(!empty($errors)): ?>
					<div class="alert alert-danger">
						<p>La connexion a échoué.</p>
						<ul>
							<?php foreach ($errors as $error): ?>
								<li><?= $error; ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<!-- Formulaire de connexion -->
				<div class="rang-form">
					<div class="colonne">
						<label for="mailUniv">Adresse mail universitaire :</label>
						<input type="mail" name="mailUniv" placeholder="prenom.nom@edu.univ-paris13.fr" required pattern="[a-z]+[0-9]?.?[a-z]+@edu.univ-paris13.fr"/>
					</div>
				</div>
				
				<div class="rang-form">
					<div class="colonne">
						<label for="mdp">Mot de passe :</label>
						<input type="password" name="mdp" placeholder="******" maxlength="30" required />
					</div>
				</div>
				
				<div class="rang-form">
					<div class="colonne">
						<a href="reinitialisation.php">J'ai oublié mon mot de passe</a><br>
						<a href="inscription.php">Je ne suis pas encore inscrit</a><br>
						<input type="submit" name="connexion" value="Valider" />
					</div>
				</div>
			</form>
		</div>
				


	</body>
	<footer>
    <?php require_once ('footer.html') ?>
  </footer>
</html>
