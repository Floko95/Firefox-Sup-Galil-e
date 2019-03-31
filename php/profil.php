<?php
session_start();
if (!isset($_SESSION['id'])) {
	header ('Location: index.php');
	exit();
} else {
	require_once 'inc/serveur.php';
	$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE id = ?');
	$req->execute(array($_SESSION['id']));
	$etudiant = $req->fetch();
	$_SESSION['etat'] = $etudiant['etat'];
	if ($_SESSION['etat'] == -1) {
		session_destroy;
		session_start;
		$_SESSION['flash']['alerte'] = 'Votre compte vient tout juste d\'être banni par un administrateur, vous avez été déconnecté et ne pouvez plus vous connecter';
		header ('Location: index.php');
		exit();
	}
	$_SESSION['prenom'] = $etudiant['prenom'];
	$_SESSION['nom'] = $etudiant['nom'];
	$_SESSION['formation'] = $etudiant['formation'];
	$_SESSION['promotion'] = $etudiant['promotion'];
}
?>

<?php
# ...
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
