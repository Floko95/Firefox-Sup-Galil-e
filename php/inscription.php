<?php
session_start();
if (isset($_SESSION['id'])) {
	header ('Location: index.php');
	exit();
}
?>

<?php require_once 'inc/serveur.php' ?>

<?php
// L'adresse perso peut etre réutilisée ...
// hacher le code ?

if (isset($_POST['inscription']) && $_POST['inscription'] == 'Valider') {

	$errors = array();

	if (empty($_POST['prenom'])) {
		$errors['prenom'] = "Prenom manquant";
	} elseif (strlen($_POST['prenom']) > 30) {
		$errors['prenom'] = "Le prénom doit faire moins de 30 caractères";
	}

	if (empty($_POST['nom'])) {
		$errors['nom'] = "Nom manquant";
	} elseif (strlen($_POST['nom']) > 30) {
		$errors['nom'] = "Le nom doit faire moins de 30 caractères";
	}

	if (empty($_POST['numero'])) {
		$errors['numero'] = "Numéro d'étudiant manquant";
	} elseif (!preg_match('#^([0-9]{8})$#', $_POST['numero'])){
		$errors['numero'] = "Numéro d'étudiant invalide (8 chiffres)";
	}

	if (empty($_POST['mailUniv'])) {
		$errors['mailUniv'] = "Adresse mail universitaire manquante";
	} elseif (!preg_match('#^([a-z]+.[a-z]+@edu.univ-paris13.fr)$#', $_POST['mailUniv'])){
		$errors['mailUniv'] = "L'adresse mail universitaire doit respecter le format prenom.nom@edu.univ-paris13.fr";
	} elseif (strlen($_POST['mailUniv']) > 70) {
		$errors['mailUniv'] = "L'adresse mail universitaire doit faire moins de 30 caractères";
	} else {
		$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE mailUniv = ? AND etat > 0');
		$req->execute(array($_POST['mailUniv']));
		$data = $req->fetch();
		if ($data) {
			$errors['mailUniv'] = "Cette adresse mail universitaire est déjà utilisée";
		}
	}

	if (!empty($_POST['mailPerso']) && !filter_var($_POST['mailPerso'], FILTER_VALIDATE_EMAIL)) {
		$errors['mailPerso'] = "Laisser le champ d'adresse mail personnelle vide ou entrer une adresse mail valide ";
	} elseif (strlen($_POST['mailPerso']) > 70) {
		$errors['mailPerso'] = "L'adresse personnelle doit faire moins de 30 caractères";
	}

	if (empty($_POST['mdp']) || strlen($_POST['mdp']) < 6) {
		$errors['mdp'] = "Le mot de passe doit contenir au minimum 6 caractères";
	} elseif ($_POST['mdp'] != $_POST['confirmation']) {
		$errors['mdp'] = "Les deux mots de passe sont différents";
	}

	if (empty($errors)) {
		require_once 'inc/fonctions.php';

		$code = chaineAleatoire(15);
		$mdp = password_hash($_POST['mdp'], PASSWORD_BCRYPT);
		$date = date('Y-m-d H:i:s');

		$req = $bdd->prepare('INSERT INTO ETUDIANTS(mailUniv, mailPerso, nom, prenom, numero, mdp, formation, promotion, dateInscription, code, typeCode, dateMail) VALUES(:mailUniv, :mailPerso, :nom, :prenom, :numero, :mdp, :formation, :promotion, :dateInscription, :code, :typeCode, :dateMail)');
		$req->execute(array(
			'mailUniv' => $_POST['mailUniv'],
			'mailPerso' => $_POST['mailPerso'],
			'nom' => $_POST['nom'],
			'prenom' => $_POST['prenom'],
			'numero' => $_POST['numero'],
			'mdp' => $mdp,
			'formation' => $_POST['formation'],
			'promotion' => $_POST['promotion'],
			'dateInscription' => $date,
			'code' => $code,
			'typeCode' => 1,
			'dateMail' => $date));

		# On envoie un mail à l'étudiant contenant le code de validation d'inscription
		$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE mailUniv = ? ORDER BY dateInscription DESC');
		$req->execute(array($_POST['mailUniv']));
		$etudiant = $req->fetch();
		$id = $etudiant['id'];
		mail($_POST['mailUniv'], "BDE : Validation de votre compte", "Afin de valider votre compte, veuillez cliquer sur ce lien\n\nhttp:://verification.php?id=$id&code=$code");

		session_start();
		$_SESSION['flash']['alerte'] = 'Le compte a bien été créé, un lien de confirmation vous a été envoyé sur votre adresse mail universitaire';
		header('Location: index.php');
		exit();
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
	
		<?php require_once ('navigation.php') ?>
		
		<div class="row top-page">
			<div class="offset-md-4 col-md-3 title">
				<h1>S'inscrire</h1>
			</div>
		</div>
		<div class="offset-md-2 col-md-2">
			<?php if(!empty($errors)): ?>
			<div class="alert alert-danger">
				<p>Vous n'avez pas rempli le formulaire correctement.</p>
				<ul>
					<?php foreach ($errors as $error): ?>
						<li><?= $error; ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>
		</div>
		<!-- Formulaire d'inscription -->
		<div class="row ">
			<div class="offset-md-4 col-md-3 block">
			<form action="inscription.php" method="post">
				<label for="prenom">Prénom</label><br>
				<input type="text" name="prenom" placeholder="ex: Jean" maxlength="30" required /><br>
				<label for="nom">Nom</label><br>
				<input type="text" name="nom" placeholder="ex: Dupont" maxlength="20" required /><br>
				<label for="numero">Numéro d'étudiant</label><br>
				<input type="text" name="numero" placeholder="ex: 11****37" minlength="8" maxlength="8" required /><br>
				<label for="mailUniv">Adresse mail universitaire</label><br>
				<input type="mail" name="mailUniv" placeholder="ex: jean.dupond@edu.univ-paris13.fr" required /><br>
				<label for="mailPerso">Adresse mail personnelle</label><br>
				<input type="mail" name="mailPerso" placeholder="ex: jean.dupond@hotmail.fr" /><br>
				<label for="formation">Formation</label><br>
				<select name="formation" size="1">
					<option>CP2I
					<option>ENER
					<option>INFO
					<option>MACS
					<option>TELE
				</select><br>
				<label for="promotion">Promotion</label><br>
				<select name="promotion" size="1">
					<option>2019
					<option>2020
					<option>2021
					<option>2022
					<option>2023
				</select><br>
				<label for="mdp">Mot de passe (6 caractère minimum)</label>	<br>
				<input type="password" name="mdp" placeholder="********" maxlength="30" required /><br>
				<label for="comfirmation">Confirmation du mot de passe</label><br>
				<input type="password" name="confirmation" placeholder="********" maxlength="30" required /><br>
				<input id="check" type="checkbox" name="regagree" value="valeur" /> Je certifie avoir pris connaissance du règlement<br>
				<input type="submit" name="inscription" value="Valider" />
			</form>
			</div>
		</div>
	</body>
	<footer>
 	   <?php require_once ('footer.html') ?>
  </footer>
</html>
