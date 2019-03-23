<?php
require_once '../../inc/serveur.php';
require_once '../../inc/fonctions.php';
/* Les anciens étudiants sont les étudiants dont l'année de promotion est dépassée :
	Si l'étudiant a pour formation CP2I, de promotion 2000 et qu'on est après le premier octobre 2000, il est ancien étudiant
	Si l'étudiant a une autre formation, de promotion 2000 et qu'on est après le premier aout 2000, il est ancien étudiant
*/
$annee = date("Y");
$annee1 = $annee;
$annee2 = $annee;
if (date("m") >= 10) {
	$annee1++;
}
if (date("m") >= 8) {
	$annee2++;
}

$req = $bdd->prepare('SELECT DISTINCT * FROM ETUDIANTS WHERE etat >= 2 AND
	((formation = "CP2I" AND promotion < ?)
	OR
	(formation != "CP2I" AND promotion < ?))
');
$req->execute(array($annee1, $annee2));
$etudiants = $req->fetchAll();
$nbEtudiants = count($etudiants);
?>
<table>
	<?php foreach ($etudiants as $etudiant): ?>
	<tr>
		<td><?php echo strtoupper(text($etudiant['nom'])) ?></td>
		<td><?php echo text($etudiant['prenom']) ?></td>
		<td class="identite"><a href="etudiant.php?id=<?php echo $etudiant['id']; ?>"><img src="../../img/id-card.png"></a></td>
		<td><?php if ($etudiant['etat'] == 3) { echo '<img src="../../img/speaker-off.png">'; } ?></td>
	</tr>
	<?php endforeach; ?>
</table>

<?php if ($nbEtudiants == 0): ?>
		Aucun ancien étudiant
<?php endif; ?>