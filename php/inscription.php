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
		$errors['mailUniv'] = "L'adresse mail universitaire doit faire moins de 70 caractères";
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
		$errors['mailPerso'] = "L'adresse personnelle doit faire moins de 70 caractères";
	}

	if (empty($_POST['mdp']) || strlen($_POST['mdp']) < 6) {
		$errors['mdp'] = "Le mot de passe doit contenir au minimum 6 caractères";
	} elseif ($_POST['mdp'] != $_POST['confirmation']) {
		$errors['mdp'] = "Les deux mots de passe sont différents";
	}

	if (empty($errors)) {
		require_once 'inc/fonctions.php';

		$code = chaineAleatoire(15);
		$code_hash = password_hash($code, PASSWORD_BCRYPT);
		$mdp = password_hash($_POST['mdp'], PASSWORD_BCRYPT);
		$date = date('Y-m-d H:i:s');
		
		if (isset($_POST['etudiant']) && $_POST['etudiant'] == "non") {
			$_POST['formation'] = NULL;
			$_POST['promotion'] = NULL;
		}

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
			'code' => $code_hash,
			'typeCode' => 1,
			'dateMail' => $date));
			
		# Si l'étudiant est passé par la CP2I de Sup Galilée
		if ($_POST['ancienCP2I'] == 'oui' && $_POST['formation'] != "CP2I") {
			$req = $bdd->prepare('SELECT MAX(id) FROM ETUDIANTS WHERE mailUniv = ?');
			$req->execute(array($_POST['mailUniv']));
			$id = $req->fetch();
			$req = $bdd->prepare('INSERT INTO attributionRolesAuxEtudiants(id, idRoles) VALUES(:id, :idRoles)');
			$req->execute(array(
				'id' => $id[0],
				'idRoles' => 4));
		}

		# On envoie un mail à l'étudiant contenant le code de validation d'inscription
		$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE mailUniv = ? ORDER BY dateInscription DESC');
		$req->execute(array($_POST['mailUniv']));
		$etudiant = $req->fetch();
		$id = $etudiant['id'];
		envoyerMail($_POST['mailUniv'], $id, 1, $code);

		$_SESSION['flash']['alerte'] = 'Le compte a bien été créé, un lien de confirmation vous a été envoyé sur votre adresse mail universitaire';
		header('Location: index.php');
		exit();
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
		
		<div id="formulaire-responsive" class="clearfix inscription-form">
			<form action="inscription.php" method="post">
				<h3>S'inscrire</h3>
				
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
				
				<!-- Formulaire d'inscription -->
				<div class="rang-form">
					<div class="demi-colonne">
						<label for="prenom">Prénom :</label>
						<input type="text" name="prenom" placeholder="Jean" maxlength="30" required />
					</div>
					<div class="demi-colonne">
						<label for="nom">Nom :</label>
						<input type="text" name="nom" placeholder="Dupont" maxlength="30" required />
					</div>
				</div>

				<div class="rang-form">
					<div class="colonne">
						<label for="numero">Numéro d'étudiant :</label>
						<input type="text" name="numero" placeholder="12345678" pattern="^([0-9]{8})$" required />
					</div>
				</div>
				
				<div class="rang-form">
					<div class="demi-colonne">
						<label for="mailUniv">Adresse mail universitaire :</label>
						<input type="mail" name="mailUniv" maxlength="70" placeholder="prenom.nom@edu.univ-paris13.fr" required />
					</div>
					<div class="demi-colonne">
						<label for="mailPerso">Adresse mail personnelle :</label>
						<input type="mail" name="mailPerso" maxlength="70" />
					</div>
				</div>

				<div class="rang-form">
					<div class="demi-colonne">
						<label for="formation">Formation :</label>
						<select name="formation" id="formation" size="1">
							<option>CP2I
							<option>ENER
							<option>INFO
							<option>MACS
							<option>TELE
							<option>INST
						</select>
					</div>
					<div class="demi-colonne">
						<label for="promotion">Promotion :</label>
						<?php 
						$annee = date("Y");
						if (date("m") >= 7) {
							$annee++;
						}
						?>
						<input type="number" name="promotion" min="2000" max=<?php echo $annee+4 ?>  step="1" value=<?php echo $annee ?> />
					</div>
				</div>

				<div class="rang-form" id="rangAncienCP2I">
					<div class="colonne">
						<label><i>J'ai fais une CP2I à Sup Galilée :</i></label><br>
						<input type="radio" id="oui" name="ancienCP2I" value="oui">Oui</input><br>
						<input type="radio" id="non" name="ancienCP2I" value="non" checked>Non</input>
					</div>
				</div>
				
				<div class="rang-form">
					<div class="demi-colonne">
						<label for="mdp">Mot de passe :</label>
						<input type="password" name="mdp" placeholder="******" maxlength="30" required />
					</div>
					<div class="demi-colonne">
						<label for="comfirmation">Confirmation du mot de passe :</label>
						<input type="password" name="confirmation" placeholder="******" maxlength="30" required />
					</div>
				</div>

				<div class="rang-form">
					<div class="colonne">
						<input id="check" type="checkbox" name="regagree" value="valeur" /> <b>J'ai lu et j'accepte les <a href="">CGU</a></b><br>
						<input type="submit" name="inscription" value="Valider" disabled />
					</div>
				</div>
			</form>
		</div>
		
		<br>
		
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/inscription.js"></script>
	</body>
	
</html>
