<?php
require_once '../../inc/serveur.php';
require_once '../../inc/fonctions.php';
$req = $bdd->prepare('SELECT * FROM ETUDIANTS WHERE etat >= 2 AND formation IS NULL');
$req->execute();
$etudiants = $req->fetchAll();
?>
<table>
	<?php foreach ($etudiants as $etudiant): ?>
	<tr>
		<td><?php echo strtoupper(text($etudiant['nom'])) ?></td>
		<td><?php echo text($etudiant['prenom']) ?></td>
		<td><a href="etudiant.php?id=<?php echo $etudiant['id']; ?>"><img src="../../img/id-card.png"></a></td>
		<td><?php if ($etudiant['etat'] == 3) { echo '<img src="../../img/speaker-off.png">'; } ?></td>
	</tr>
	<?php endforeach; ?>
</table>