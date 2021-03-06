<?php
session_start();
require_once '../../inc/serveur.php';
require_once '../../inc/fonctions.php';
# Requête pour savoir si l'étudiant possède le droit i
$reqDroit = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants NATURAL JOIN attributionDroitsAuxRoles WHERE id = ? AND idDroits = ?');
if (isset($_GET['idRoles'])) {
	$i = intval($_GET['idRoles']);
	$req = $bdd->prepare('SELECT * FROM ETUDIANTS NATURAL JOIN attributionRolesAuxEtudiants WHERE idRoles = ? AND etat >= 2');
	$req->execute(array($i));
	$etudiants = $req->fetchAll();
	$nbEtudiants = count($etudiants);
	$req = $bdd->prepare('SELECT COUNT(*) FROM ETUDIANTS E WHERE E.id = ? 
		AND NOT EXISTS (SELECT * FROM attributionRolesAuxEtudiants A2 NATURAL JOIN attributionDroitsAuxRoles B2 WHERE A2.idRoles = ? 
		AND NOT EXISTS (SELECT * FROM attributionRolesAuxEtudiants A3 NATURAL JOIN attributionDroitsAuxRoles B3 WHERE A3.id = E.id))
		AND EXISTS (SELECT * FROM attributionRolesAuxEtudiants A4 NATURAL JOIN attributionDroitsAuxRoles B4 WHERE B4.idDroits = 2 AND A4.id = E.id)');
	$req->execute(array($_SESSION['id'], $i));
	$data = $req->fetch();
?>
	
	<form action="roles.php" method="post">
		<table>
			<?php foreach ($etudiants as $etudiant): ?>
			<tr>
				<td><?php echo strtoupper(text($etudiant['nom'])) ?></td>
				<td><?php echo text($etudiant['prenom']) ?></td>
				<td><?php echo text($etudiant['formation']) ?></td>
				<td><?php echo text($etudiant['promotion']) ?></td>
				<?php if ($data[0] == 1): ?><td><input type="checkbox" name=<?php echo 'retirer'.$etudiant['id']; ?> /></td><?php endif; ?>
			</tr>
			<?php endforeach; ?>
		</table>
	<?php if ($nbEtudiants == 0 && $i != 1): ?>
		Ce rôle n'est attribué à personne
		<?php
			$reqDroit->execute(array($_SESSION['id'], 2));
			$data = $reqDroit->fetch();
			if ($data[0] > 0):
		?>
			<button type="submit" name=<?php echo 'suppressionRole'.$i; ?> value="Valider">Supprimer ce rôle</button>
		<?php endif; ?>
	<?php else: ?>
		<?php
			$reqDroit->execute(array($_SESSION['id'], 4));
			$data = $reqDroit->fetch();
			if ($data[0] > 0):
		?>
			<button type="submit" name=<?php echo 'retirerRole'.$i; ?> value="Valider">Retirer le rôle aux étudiants sélectionnés</button>
		<?php endif; ?>
	<?php endif; ?>
	</form>
	
<?php } ?>