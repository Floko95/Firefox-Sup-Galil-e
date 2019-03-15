<?php
session_start();
require_once '../../inc/serveur.php';
require_once '../../inc/fonctions.php';
$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE etat = -1');
$req->execute();
$etudiants = $req->fetchAll();
$nbEtudiants = count($etudiants);
# Requête pour savoir si l'étudiant possède le droit i
$reqDroit = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants NATURAL JOIN attributionDroitsAuxRoles WHERE id = ? AND idDroits = ?');
?>
<form action="roles.php" method="post">
	<table>
		<?php foreach ($etudiants as $etudiant): ?>
		<tr>
			
			<td><?php echo strtoupper(text($etudiant['nom'])) ?></td>
			<td><?php echo text($etudiant['prenom']) ?></td>
			<td><?php echo text($etudiant['numero']) ?></td>
			<td><?php echo text($etudiant['mailUniv']) ?></td>
			<td><?php echo text($etudiant['mailPerso']) ?></td>
			<td><?php echo text($etudiant['formation']) ?></td>
			<td><?php echo text($etudiant['promotion']) ?></td>
			<td><input type="checkbox" name=<?php echo 'banni'.$etudiant['id']; ?> /></td>
		</tr>
		<?php endforeach; ?>
	</table>
	
	<?php if ($nbEtudiants == 0): ?>
		Aucun étudiant n'a son compte banni
	<?php else: ?>
		<form action="roles.php" method="post">
			<?php
			$reqDroit->execute(array($_SESSION['id'], 9));
			$data = $reqDroit->fetch();
			if ($data[0] > 0):
			?>
				<button type="submit" name="supprimerCompte" value="Valider">Supprimer ces comptes</button>
			<?php endif; ?>
			<?php
			$reqDroit->execute(array($_SESSION['id'], 8));
			$data = $reqDroit->fetch();
			if ($data[0] > 0):
			?>
				<button type="submit" name="réhabiliterCompte" value="Valider">Réhabiliter ces étudiants</button>
			<?php endif; ?>
		</form>
	<?php endif; ?>

</form>