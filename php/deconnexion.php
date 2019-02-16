<?php require_once("inc/serveur.php"); ?>

<?php
session_start();
session_destroy();
header('location: accueil.php');
exit;
?>
