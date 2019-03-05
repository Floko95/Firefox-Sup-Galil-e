<?php
require_once '../../inc/serveur.php';
require_once '../../inc/fonctions.php';
$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE etat = 1');
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
			<td><input type="checkbox" name="enAttente'.$etudiant['id'].'"/></td>
		</tr>
		<?php endforeach; ?>
	</table>

	<?php if ($nbEtudiants == 0): ?>
		Aucun étudiant en attente de validation d'inscription
	<?php else: ?>
		<button type="submit" name="refuserInscription" value="Valider">Refuser ces inscriptions</button>
		<button type="submit" name="validerInscription" value="Valider">Valider ces inscriptions</button>
	<?php endif; ?>
</form>