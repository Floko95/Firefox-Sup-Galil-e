<?php require_once 'inc/serveur.php'; ?>
<?php 
session_cache_limiter('private, must-revalidate');
require_once 'inc/check_ban.php'; 
?>


<?php 
//RECUPERATION MESSAGE POSTE--------------------------------------------------
	if(isset($_POST['ecriture']) and isset($_POST['a_recup']) and trim($_POST['ecriture']!=''))
	{
		if($_SESSION['etat'] == 2)
		{
			$req = $bdd->prepare('INSERT INTO `messages`(`id`, `idTopics`, `message`, `dateEnvoi`) VALUES (:id,:indtopic,:mess,:date)');
			$req->execute(array(
			
			'id' => intval($_SESSION['id']),
			'indtopic' => intval($_POST['a_recup']),
			'mess' => $_POST['ecriture'],
			'date' => date('Y-m-d H:i:s')
			));
			$req = $bdd->prepare('UPDATE Topics SET dateDerniereModif = :d Where idTopics= :id');
			$req->execute(array('id' => intval($_POST['a_recup']),
								'd' => date('Y-m-d H:i:s')));
		}
		else if ($_SESSION['etat'] == 3)
		{
			$_SESSION['flash']['alerte'] = 'Votre compte vient tout juste d\'etre mute par un administrateur.Vous ne pouvez plus envoyer de message sur le forum.';
		}
		else
		{
			$_SESSION['flash']['alerte'] = 'Une erreur s\'est produite au niveau de votre compte.Veuillez contacter les administrateurs.';
		}
	}
//------------------------------------------------------------------------------
?>




<!DOCTYPE html>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="../css/main.css">
		<link rel="stylesheet" type="text/css" href="../css/forum.css">
		<link rel="stylesheet" type="text/css" href="../css/alerte.css" />
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	</head>
	<body>
	
		<?php require_once ('navigation.php');?>

		<div class="container top-page">
			<?php require_once 'inc/erreurs.php'; ?>
			<div class="row">
				<div class="col">
					<div id="ajax_messages">
						<!-- les messages sont insérés ici par ajax-->
					</div>
					
					<div class="row new-message">
						<div class="col">
						
							<form method="post" action="Forum.php">
								<?php if($_SESSION['etat'] == 2):?><textarea name="ecriture" placeholder="Nouveau message" cols="80" rows="7"></textarea>	<?php endif;?>
								<input id = "a_recup" type="hidden" name="a_recup" value="<?php echo $_POST['a_recup'];?>"/> <?php //LAISSER CECI,LE JS EN A BESOIN?>
								<?php if($_SESSION['etat'] == 2):?><input type="submit" value="Poster"/>	<?php endif;?>
							</form>
						
						</div>
					</div>
				
					<!--Navigation par page-->
					<div class="row">
						<div class="offset-md-1 col-md-2">
							<form action="">
								<label for="nb_page">Nombre de messages par page</label>
								<select id = "nb_page" name="nb_page" size="1">
									<option>20
									<option>50
									<option>100
								</select><br>
							</form>
						</div>
						<div class="offset-md-2 col-mad-1">
							prev<br>
							<button id="prev"><</button>
						</div>
						<div class="offset-md-1 col-mad-1">
							next<br>
							<button id="next">></button>
						</div>
						
						
					</div>
				</div>
			</div>
	  </div>

	  <script type="text/javascript" src="../js/jquery.js"></script>
	  <script type="text/javascript" src="../js/forum.js"></script>
	  <script type="text/javascript" src="../js/alerte.js"></script>
	
	</body>
</html>