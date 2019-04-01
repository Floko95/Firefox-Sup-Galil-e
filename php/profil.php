<?php
require_once 'inc/check_ban.php';
require_once 'inc/fonctions.php';
?>

<?php
# Changement de formation et/ou de promotion
if (isset($_POST['modifierFormation']) && $_POST['modifierFormation'] == 'Valider') {

	$errors = array();
	
	# Si on a coché le changement de formation mais qu'on n'est pas CP2I
	if (isset($_POST['cp2iterminee']) && $_SESSION['formation'] != 'CP2I') {
		$errors['changementFormation'] = "Seuls les CP2I peuvent changer de formation";
	}
	
	# Si on a coché le changement de formation mais qu'on n'a pas choisi de nouvelle formation
	if (isset($_POST['cp2iterminee']) && empty($_POST['formation'])) {
		$errors['nouvelleFormation'] = "Vous devez choisir une nouvelle formation";
	}
	
	# Si la nouvelle promotion est manquante
	if (empty($_POST['promotion'])) {
		$errors['nouvellePromotion'] = "Vous devez choisir une nouvelle promotion";
	}
	
	# S'il n'y a pas d'erreurs, on modifie les informations
	if (empty($errors)) {
		# On met à jour la formation
		if (isset($_POST['cp2iterminee'])) {
			$req = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants WHERE id = ? AND idRoles = 4');
			$req->execute(array($_SESSION['id']));
			$data = $req->fetch();
			if ($data[0] == 0) {
				$req = $bdd->prepare('INSERT INTO attributionRolesAuxEtudiants(id, idRoles) VALUES(:id, :idRoles)');
				$req->execute(array(
					'id' => $_SESSION['id'],
					'idRoles' => 4));
			}
			$req = $bdd->prepare('UPDATE ETUDIANTS SET formation = ? WHERE id = ?');
			$req->execute(array($_POST['formation'], $_SESSION['id']));
			$_SESSION['formation'] = $_POST['formation'];
		}
		# On met à jour la promotion
		$req = $bdd->prepare('UPDATE ETUDIANTS SET promotion = ? WHERE id = ?');
		$req->execute(array($_POST['promotion'], $_SESSION['id']));
		$success['modificationsEffectuees'] = "Les modifications ont bien été prises en compte";
		$_SESSION['promotion'] = $_POST['promotion'];
	}
	
}
?>

<?php
# Changement de mot de passe
if (isset($_POST['modifierMdp']) && $_POST['modifierMdp'] == 'Valider') {

	$errors = array();
	
	# Si le mot de passe actuel n'est pas bon
	if (empty($_POST['mdpActuel'])) {
		$errors['mdpActuel'] = "Le mot de passe actuel entré n'est pas le bon";
	} else {
		if (!password_verify($_POST['mdpActuel'], $etudiant['mdp'])) {
			$errors['mdpActuel'] = "Le mot de passe actuel entré n'est pas le bon";
		}
	}

	# Si le nouveau mot de passe n'est pas conforme ou la confirmation est différente
	if (empty($_POST['mdp']) || strlen($_POST['mdp']) < 6) {
		$errors['mdp'] = "Le mot de passe doit contenir au minimum 6 caractères";
	} elseif ($_POST['mdp'] != $_POST['confirmation']) {
		$errors['mdp'] = "Les deux mots de passe sont différents";
	}
	
	# S'il n'y a pas d'erreur, on modifie le mot de passe
	if (empty($errors)) {
		$req = $bdd->prepare('UPDATE ETUDIANTS SET mdp = ? WHERE id = ?');
		$req->execute(array(password_hash($_POST['mdp'], PASSWORD_BCRYPT), $_SESSION['id']));
		$success['mdpModifie'] = "Le mot de passe a bien été modifié";
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
			
			<h3>Mon compte</h3>
				
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
			
			<!-- Affichage des succès -->
			<?php if(!empty($success)): ?>
				<div class="alert alert-success">
					<strong>
					<ul>
						<?php foreach ($success as $succes): ?>
							<li><?= $succes; ?></li>
						<?php endforeach; ?>
					</ul>
					</strong>
				</div>
			<?php endif; ?>
				
			<div class="rang-form">
				<fieldset>
					<legend>Mes informations personnelles</legend>
					<div class="colonne">
						<label for="prenom">Prénom : </label> <?php echo text($_SESSION['prenom']); ?>
					</div>
					<div class="colonne">
						<label for="nom">Nom : </label> <?php echo text($_SESSION['nom']); ?>
					</div>
					<div class="colonne">
						<label for="numero">Numéro d'étudiant : </label> <?php echo text($etudiant['numero']); ?>
					</div>
					<div class="colonne">
						<label for="mailUniv">Adresse mail universitaire :</label><br> <?php echo text($etudiant['mailUniv']); ?>
					</div>
					<div class="colonne">
						<label for="mailPerso">Adresse mail personnelle :</label><br> <?php if (!empty($etudiant['mailPerso'])) {echo text($etudiant['mailPerso']);} else { echo '-';} ?>
					</div>
				</fieldset>
				<form action="profil.php" method="post">
					<fieldset>
						<legend>Ma formation</legend>
						<div class="colonne">
							<label for="formation">Formation : </label> <?php echo text($_SESSION['formation']); ?>
						</div>
						<?php if ($_SESSION['formation'] == 'CP2I'): ?>
						<div class="colonne">
							<label><i>J'ai terminé ma CP2I :</i></label>
							<input id="cp2iterminee" type="checkbox" name="cp2iterminee" value="valeur" />
						</div>
						<div class="colonne" id="nouvelleFormation" style="display:none">
							<label for="formation">Nouvelle formation :</label>
							<select name="formation" id="formationP" size="1">
								<option>ENER
								<option>INFO
								<option>MACS
								<option>TELE
								<option>INST
							</select>
						</div>
						<?php endif; ?>
						<div class="colonne">
							<label for="promotion">Promotion :</label> 
							<?php $annee = date("Y"); if (date("m") >= 7) { $annee++; } ?>
							<input type="number" name="promotion" min="2000" max=<?php echo $annee+4 ?>  step="1" value=<?php echo text($_SESSION['promotion']) ?> />
						</div>
						<div class="colonne">
							<input type="submit" name="modifierFormation" value="Valider" />
						</div>
					</fieldset>
				</form>
				<form action="profil.php" method="post">
					<fieldset>
						<legend>Modifier mon mot de passe</legend>
						<div class="colonne">
							<label for="mdp">Mot de passe actuel:</label>
							<input type="password" name="mdpActuel" placeholder="******" maxlength="30" required />
						</div>
						<div class="colonne">
							<label for="comfirmation">Nouveau mot de passe :</label>
							<input type="password" name="mdp" placeholder="******" maxlength="30" required />
						</div>
						<div class="colonne">
							<label for="comfirmation">Confirmer :</label>
							<input type="password" name="confirmation" placeholder="******" maxlength="30" required />
						</div>
						<div class="colonne">
							<input type="submit" name="modifierMdp" value="Valider" />
						</div>
					</fieldset>
				</form>
				</div>

			
		</div>
		
		<br>

		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/inscription.js"></script>
	</body>
	
</html>
