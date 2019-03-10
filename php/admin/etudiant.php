<!-- FICHE ETUDIANT -->

<?php
# On redirige le visiteur s'il n'a rien à faire sur cette page
session_start();
if (!isset($_SESSION['id'])) {
	header ('Location: ../index.php');
	exit();
}
else {
	require_once '../inc/serveur.php';
	$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE id = ? AND EXISTS (SELECT * FROM attributionRolesAuxEtudiants WHERE id = ?)'); // tous ceux qui ont un droit ?
	$req->execute(array($_SESSION['id'], $_SESSION['id']));
	$data = $req->fetch();
	if (!$data) {
		header ('Location: ../index.php');
		exit();
	}
}
require_once '../inc/fonctions.php';
?>

<?php
# Si l'id passé en url est bon, on récupère les rôles de l'étudiant possédant cet id
if (isset($_GET['id'])){
	$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE id = ? AND etat != 0');
	$req->execute(array(intval($_GET['id'])));
	$etudiant = $req->fetch();
	if ($etudiant) {
		$id = intval($_GET['id']);
		$req = $bdd->prepare('SELECT * FROM attributionRolesAuxEtudiants NATURAL JOIN ROLES WHERE id = ?');
		$req->execute(array($id));
		$rolesPossedes = $req->fetchAll();
	}
}
?>

<?php
# Requête pour savoir si l'étudiant possède le droit i
$reqDroit = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants NATURAL JOIN attributionDroitsAuxRoles WHERE id = ? AND idDroits = ?');
# Requête pour savoir si l'étudiant a possède tous les droits possédés par l'étudiant b
$reqPossedeTout = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants A1 NATURAL JOIN attributionDroitsAuxRoles B1 WHERE A1.id = ? 
	AND NOT EXISTS (SELECT * FROM attributionRolesAuxEtudiants A2 NATURAL JOIN attributionDroitsAuxRoles B2 WHERE A2.id = ? AND B1.idDroits = B2.idDroits)');

# Requête pour savoir quels rôles l'étudiant a peut attribuer à l'étudiant b
$reqQuelsRoles = $bdd->prepare('SELECT * FROM ROLES R WHERE R.idRoles NOT IN (2,3,4,5)
	AND NOT EXISTS (SELECT * FROM attributionDroitsAuxRoles A1 WHERE R.idRoles = A1.idRoles
	AND NOT EXISTS (SELECT * FROM attributionRolesAuxEtudiants B1 NATURAL JOIN attributionDroitsAuxRoles A2 WHERE B1.id = ? AND A1.idDroits = A2.idDroits))
	AND NOT EXISTS (SELECT * FROM attributionRolesAuxEtudiants B2 WHERE B2.id = ? AND B2.idRoles = R.idRoles)');
?>

<?php
if (isset($_POST['rechercheEtudiant']) && $_POST['rechercheEtudiant'] == 'Valider') {
	if (isset($_POST['prenom'], $_POST['nom'], $_POST['formation'])){
		if (empty($_POST['formation'])) {
			$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE prenom LIKE ? AND nom LIKE ? ORDER BY nom ASC');
			$req->execute(array('%'.$_POST['prenom'].'%', '%'.$_POST['nom'].'%'));
		} else {
			$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE prenom LIKE ? AND nom LIKE ? AND formation LIKE ? ORDER BY nom ASC');
			$req->execute(array('%'.$_POST['prenom'].'%', '%'.$_POST['nom'].'%', '%'.$_POST['formation'].'%'));
		}
		$resultats = $req->fetchAll();
		echo strval($_POST['prenom']);
	}
}
?>

<?php
$errors = array();
$success = array();
?>

<?php
# Attribuer un rôle
if (isset($_POST['attribuerRole']) && $_POST['attribuerRole'] == 'Valider') {
	# S'il n'y a pas d'étudiant de destination
	# Sinon s'il n'y a pas de rôle sélectionné
	# Sinon si l'étudiant destination possède déjà ce rôle
	# Sinon si l'étudiant ne possède pas tous les droits de ce rôle
	if (!isset($id)) {
		$errors['étudiantManquant'] = "Vous devez choisir un étudiant à qui attribuer ce rôle";
	} else {
		if (empty($_POST['nomRole'])) {
			$errors['roleManquant'] = "Aucun rôle n'a été spécifié";
		} else {
			$req = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants NATURAL JOIN ROLES WHERE id = ? AND role = ?');
			$req->execute(array($id, $_POST['nomRole']));
			$data = $req->fetch();
			if ($data[0] > 0) {
				$errors['roleDejaPossede'] = "L'étudiant possède déjà ce rôle";
			}
			$req = $bdd->prepare('SELECT COUNT(*) FROM ETUDIANTS E WHERE E.id = ? 
				AND NOT EXISTS (SELECT * FROM attributionRolesAuxEtudiants A2 NATURAL JOIN attributionDroitsAuxRoles B2 NATURAL JOIN ROLES C WHERE C.role = ? 
				AND NOT EXISTS (SELECT * FROM attributionRolesAuxEtudiants A3 NATURAL JOIN attributionDroitsAuxRoles B3 WHERE A3.id = E.id))');
			$req->execute(array($_SESSION['id'], $_POST['nomRole']));
			$data = $req->fetch();
			if ($data[0] == 0) {
				$errors['droitManquant'] = "Vous devez posséder tous les droits conférés par ce rôle";
			}
		}
	}
	
	# Si l'étudiant n'est pas autorisé à attribuer un rôle
	$reqDroit->execute(array($_SESSION['id'], 3));
	$data = $reqDroit->fetch();
	if ($data[0] == 0) {
		$errors['droitAttributionManquant'] = "Vous n'êtes pas autorisé à attribuer un rôle";
	}
	
	# S'il n'y a pas d'erreur, on attribue le rôle à l'étudiant
	if (empty($errors)) {
		$req = $bdd->prepare('SELECT * FROM ROLES WHERE role = ?');
		$req->execute(array($_POST['nomRole']));
		$data = $req->fetch();
		$req = $bdd->prepare('INSERT INTO attributionRolesAuxEtudiants(id, idRoles) VALUES(:id, :idRoles)');
		$req->execute(array(
			'id' => $id,
			'idRoles' => $data['idRoles']));
		$success['roleAttribué'] = 'Le rôle a bien été attribué';
		# On met à jour les rôles possédés
		$req = $bdd->prepare('SELECT * FROM attributionRolesAuxEtudiants NATURAL JOIN ROLES WHERE id = ?');
		$req->execute(array($id));
		$rolesPossedes = $req->fetchAll();
	}
}
?>

<?php
# Bannir un étudiant
if (isset($_POST['bannir'])) {
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" href="../../css/admin.css" />
	</head>
	<body>
		
		<?php require_once '/inc/menu.php'; ?>

		<div id="page">
			<div id="head">
				Accueil
			</div>
			<div id="title">
				Etudiant
			</div>

			<?php require_once '/inc/erreurs.php'; ?>
			
			<?php if(isset($id)): ?>
				<div id="contenu">
					<div id ="contenuTitle">
						<?php
						$reqPossedeTout->execute(array($_SESSION['id'], $id)); # Pour savoir si on est autorisé à agir sur l'étudiant sélectionné
						$droitTout = $reqPossedeTout->fetch();
						$reqDroit->execute(array($_SESSION['id'], 3)); # Attribuer un rôle
						$droit3 = $reqDroit->fetch();
						$reqDroit->execute(array($_SESSION['id'], 5)); # Valider l'inscription
						$droit5 = $reqDroit->fetch();
						$reqDroit->execute(array($_SESSION['id'], 6)); # Refuser l'inscription
						$droit6 = $reqDroit->fetch();
						$reqDroit->execute(array($_SESSION['id'], 7)); # Modifier le profil
						$droit7 = $reqDroit->fetch();
						$reqDroit->execute(array($_SESSION['id'], 8)); # Bannir
						$droit8 = $reqDroit->fetch();
						$reqDroit->execute(array($_SESSION['id'], 10)); # Rendre muet
						$droit10 = $reqDroit->fetch();
						?>
						<?php 
						echo text($etudiant['prenom']).' '.strtoupper(text($etudiant['nom']));
						if ($droit7[0] > 0 && $etudiant['etat'] >= 1) {
							echo ' <button><img src="../../img/gears.png"></button>';
						}
						?> 
						<?php 
						if ($etudiant['etat'] == 0 || $etudiant['etat'] == 1) {
							echo '<img src="../../img/sands-of-time.png">';
						} elseif ($etudiant['etat'] == 2) {
							echo '<img src="../../img/speaker.png">';
						} elseif ($etudiant['etat'] == 3) {
							echo '<img src="../../img/speaker-off32.png">';
						} else {
							echo '<img src="../../img/padlock.png">';
						}
						echo '<br>';
						if ($droit8[0] > 0 && $etudiant['etat'] >= 2 && $droitTout[0] == 0) {
							echo '<button>Bannir</button> ';
						}
						if ($droit8[0] > 0 && $etudiant['etat'] == -1) {
							echo '<button>Réhabiliter</button> ';
						}
						if ($droit5[0] > 0 && $etudiant['etat'] == 1) {
							echo '<button>Valider l\'inscription</button> ';
						}
						if ($droit6[0] > 0 && $etudiant['etat'] == 1) {
							echo '<button>Refuser l\'inscription</button> ';
						}
						if ($droit10[0] > 0 && $etudiant['etat'] == 2 && $droitTout[0] == 0) {
							echo '<button>Rendre muet</button> ';
						}
						if ($droit10[0] > 0 && $etudiant['etat'] == 3) {
							echo '<button>Redonner le droit de parole</button> ';
						}
						?>
						
					</div>
					<h1>Informations personnelles :</h1>
					<table style="width: 500px; margin-left: auto; margin-right: auto;">
						<tr>
							<td>Prénom</td>
							<td><?php echo text($etudiant['prenom']) ?>
						</tr>
						<tr>
							<td>Nom</td>
							<td><?php echo text($etudiant['nom']) ?>
						</tr>
						<tr>
							<td>Numéro étudiant</td>
							<td><?php echo text($etudiant['numero']) ?>
						</tr>
						<tr>
							<td>Mail universitaire</td>
							<td><?php echo text($etudiant['mailUniv']) ?>
						</tr>
						<tr>
							<td>Mail personnel</td>
							<td><?php echo text($etudiant['mailPerso']) ?>
						</tr>
						<tr>
							<td>Formation</td>
							<td><?php echo text($etudiant['formation']) ?>
						</tr>
						<tr>
							<td>Promotion</td>
							<td><?php echo text($etudiant['promotion']) ?>
						</tr>
					</table>
					<?php if ($etudiant['etat'] >= 2): ?>
						<hr>
						<h1>Rôles possédés :</h1>
						<?php 
						if (count($rolesPossedes) > 0) { 
							foreach ($rolesPossedes as $rolePossede){
								echo '- '.$rolePossede['role'].'<br>';
							}
						} else { 
							echo 'Aucun rôle n\'est possédé par cet étudiant';
						}
						?>
						<?php 
						if ($droit3[0] > 0): 
							$reqQuelsRoles->execute(array($_SESSION['id'], $id));
							$rolesAjoutables = $reqQuelsRoles->fetchAll();
						?>
						<form method="post">
						<label>Attribuer un rôle à cet étudiant :</label>
							<select name="nomRole">
								<?php 
								foreach ($rolesAjoutables as $roleAjoutable){
									echo '<option>'.$roleAjoutable['role'];
								}
								?>
							</select>
							<button type="submit" name="attribuerRole" value="Valider">Valider</button>
						</form>
						<?php endif; ?>
					<?php endif; ?>
					 
				</div>
			<?php endif; ?>
			<div id="contenu">
				<div id ="contenuTitle">
					Rechercher un étudiant
				</div>
				<form action="etudiant.php" method="post">
					<label><u>Prénom</u> :</label><br>
					<input type="text" name="prenom"/><br>
					<label><u>Nom</u> :</label><br>
					<input type="text" name="nom" style="margin-bottom: 5px"/><br>
					<label><u>Formation</u> :</label>
					<select name="formation" size="1">
						<option>
						<option>CP2I
						<option>ENER
						<option>INFO
						<option>MACS
						<option>TELE
					</select><br>
					<button type="submit" name="rechercheEtudiant" value="Valider">Rechercher</button>
				</form>
			</div>
			<?php if(isset($resultats) && !isset($id)): ?>
				<div id="contenu">
					<div id ="contenuTitle">
						<?php echo count($resultats) ?> résultat(s)
					</div>
					<table>
						<?php foreach ($resultats as $resultat): ?>
							<tr>
								<td><?php echo text($resultat['prenom']) ?></td>
								<td><?php echo strtoupper(text($resultat['nom'])) ?></td>
								<td><a href="etudiant.php?id=<?php echo $resultat['id']; ?>"><img src="../../img/id-card.png"></a></td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>
			<?php endif; ?>
		</div>
	</body>
</html>