<?php
session_start();
require_once '../../inc/serveur.php';
require_once '../../inc/fonctions.php';
# Requête pour savoir si l'étudiant possède le droit i
$reqDroit = $bdd->prepare('SELECT COUNT(*) FROM ETUDIANTS NATURAL JOIN attributionRolesAuxEtudiants NATURAL JOIN attributionDroitsAuxRoles WHERE id = ? AND idDroits = ?');
$reqDroit->execute(array($_SESSION['id'], 4));
$data = $reqDroit->fetch();
$req = $bdd->prepare('SELECT * FROM ETUDIANTS NATURAL JOIN attributionRolesAuxEtudiants WHERE idRoles = 4 AND etat >= 2');
$req->execute();
$etudiants = $req->fetchAll();
$nbEtudiants = count($etudiants);
?>
	
	<form action="roles.php" method="post">
		<table>
			<?php foreach ($etudiants as $etudiant): ?>
			<tr>
				<td><?php echo strtoupper(text($etudiant['nom'])) ?></td>
				<td><?php echo text($etudiant['prenom']) ?></td>
				<td><?php echo text($etudiant['formation']) ?></td>
				<td><?php echo text($etudiant['promotion']) ?></td>
				<td class="identite"><a href="etudiant.php?id=<?php echo $etudiant['id']; ?>"><img src="../../img/id-card.png"></a></td>
				<?php if ($data[0] == 1): ?><td><input type="checkbox" name=<?php echo 'retirer'.$etudiant['id']; ?> /></td><?php endif; ?>
			</tr>
			<?php endforeach; ?>
		</table>
	<?php if ($nbEtudiants == 0): ?>
		Il n'y a aucun ancien CP2I
	<?php else: ?>
		<?php if ($data[0] > 0): ?>
			<button type="submit" name=<?php echo 'retirerRole4'; ?> value="Valider">Retirer le statut d'ancien CP2I aux étudiants sélectionnés</button>
		<?php endif; ?>
	<?php endif; ?>
	</form>
	
