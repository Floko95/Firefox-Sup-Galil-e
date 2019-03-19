 <?php require_once 'inc/serveur.php' ;?>
 <?php session_start(); ?>
<?php require_once ('navigation.php');?>





<?php 
//RECUPERATION MESSAGE POSTE--------------------------------------------------
	if(isset($_POST['ecriture']) and isset($_POST['a_recup']) and trim($_POST['ecriture']!=''))
	{
		$req = $bdd->prepare('INSERT INTO `messages`(`id`, `idTopics`, `message`, `dateEnvoi`) VALUES (:id,:indtopic,:mess,:date)');
			$req->execute(array(
			
			'id' => intval($_SESSION['id']),
			'indtopic' => intval($_POST['a_recup']),
			'mess' => $_POST['ecriture'],
			'date' => date('Y-m-d H:i:s')
			));
	}
//------------------------------------------------------------------------------

?>




<!DOCTYPE html>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="../css/main.css">
	<link rel="stylesheet" type="text/css" href="../css/forum.css">
</head>
<body>

  <div class="container top-page">
        <div class="row">
            <div class="col">
				<div id="ajax_messages">
					<!-- les messages sont insérés ici par ajax-->
				</div>
				<div class="row new-message">
                    <div class="col">
						<form method="post" action="Forum.php">
							<textarea name="ecriture" placeholder="Nouveau message" cols="80" rows="7"></textarea>
							<input id = "a_recup" type="hidden" name="a_recup" value="<?php echo $_POST['a_recup'];?>"/>
							<input type="submit" value="Poster"/>
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








  <footer>
    <?php require_once ('footer.html') ?>
  </footer>
  <script type="text/javascript" src="../js/jquery.js"></script>
  <script type="text/javascript" src="../js/forum.js"></script>
</body>

</html>
