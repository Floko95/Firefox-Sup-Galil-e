<!-- FICHE ETUDIANT -->

<?php
# On redirige le visiteur s'il n'a rien à faire sur cette page
session_start();
if (!isset($_SESSION['id'])) {
	header ('Location: ../accueil.php');
	exit();
}
else {
	require_once '../inc/serveur.php';
	$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE id = ? AND EXISTS (SELECT * FROM attributionRolesAuxEtudiants WHERE id = ?)'); // tous ceux qui ont un droit ?
	$req->execute(array($_SESSION['id'], $_SESSION['id']));
	$data = $req->fetch();
	if (!$data) {
		header ('Location: ../accueil.php');
		exit();
	}
}
require_once '../inc/fonctions.php';
?>

<?php
if (isset($_GET['id'])){
	$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE id = ?');
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

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" href="../../css/admin.css" />
	</head>
	<body>
		<div id="menu">
			<img src="menu.png" style="padding: 20px">
			<nav>
				<ul>
					<a href="roles.php"><li id="menu-role"></li></a><hr>
					<a href="etudiant.php"><li id="menu-etudiant"></li></a>
					<a href="calendrier.php"><li id="menu-calendrier"></li></a>
					<a href="boutique.php"><li id="menu-boutique"></li></a>
					<a href="mini-jeu.php"><li id="menu-minijeu"></li></a>
				</ul>
			</nav>
		</div>

		<div id="page">
			<div id="head">
				Accueil
			</div>
			<div id="title">
				Etudiant
			</div>


			
			<?php if(isset($id)): ?>
				<div id="contenu">
					<div id ="contenuTitle">
						<?php echo text($etudiant['prenom']).' '.strtoupper(text($etudiant['nom'])); ?> <button><img src="../../img/gears.png"></button>
						<br><button>Bannir</button> <button>Rendre muet</button>
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
					<form>
					<label>Attribuer un rôle à cet étudiant :</label>
						<select>
						</select>
						<button>Valider</button>
					</form>
					 
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