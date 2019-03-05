<?php
require_once '../../inc/serveur.php';
require_once '../../inc/fonctions.php';
$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE etat = -1');
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
			<td><?php echo text($etudiant['numero']) ?></td>
			<td><?php echo text($etudiant['mailUniv']) ?></td>
			<td><?php echo text($etudiant['mailPerso']) ?></td>
			<td><?php echo text($etudiant['formation']) ?></td>
			<td><?php echo text($etudiant['promotion']) ?></td>
			<td><input type="checkbox" name="banni'.$etudiant['id'].'"/></td>
		</tr>
		<?php endforeach; ?>
	</table>
	
	<?php if ($nbEtudiants == 0): ?>
		Aucun étudiant n'a son compte banni
	<?php else: ?>
		<button type="submit" name="supprimerCompte" value="Valider">Supprimer ces comptes</button>
		<button type="submit" name="réhabiliterCompte" value="Valider">Réhabiliter ces étudiants</button>
	<?php endif; ?>

</form>