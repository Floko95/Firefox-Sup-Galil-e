<?php
require_once '../../inc/serveur.php';

if (!empty($_GET['image']) && $_GET['image'] != 'Aucune') {
	$image = $_GET['image'];

	$req = $bdd->prepare('SELECT COUNT(*) FROM IMAGES WHERE image = ?');
	$req->execute(array($image));
	$data = $req->fetch();
	if ($data[0] == 1) {
		echo '<br><img src="../../img/imports/'.$image.'" style="max-height: 300px; max-width: 450px" >';
	} else {
		echo 'Image introuvable';
	}
}
?>