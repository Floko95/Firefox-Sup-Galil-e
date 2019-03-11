<!DOCTYPE html>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="../css/main.css">
	<link rel="stylesheet" type="text/css" href="../css/forum.css">
</head>
<body>
 
<?php require_once ('navigation.html');?>
<?php require_once 'inc/serveur.php' ;?>
<?php 

//RECUPERATION MESSAGE POSTE--------------------------------------------------
	if(isset($_POST['ecriture']) and isset($_POST['a_recup']) and trim($_POST['ecriture']!=''))
	{
		$req = $bdd->prepare('INSERT INTO `messages`(`id`, `idTopics`, `message`, `dateEnvoi`) VALUES (1,:indtopic,:mess,:date)');// a changer
			$req->execute(array(
			// en attendant que le systeme de connexion remarche, on met le 1 en brut
			//'id' => intval($_SESSION['id']),
			'indtopic' => intval($_POST['a_recup']),
			'mess' => $_POST['ecriture'],
			'date' => date('Y-m-d H:i:s')
			));
	}
//------------------------------------------------------------------------------
//RECUPERATION ID TOPIC-------------------------------------------------------------------------

	if (isset($_POST['a_recup'])and trim($_POST['a_recup']!=''))
	{
		$id =intval($_POST['a_recup']);// la requete sql a besoin d'un entier or le $post a_recup est un string

		$req = $bdd->prepare('SELECT prenom,nom,message,dateEnvoi FROM messages NATURAL JOIN etudiants WHERE  idTopics =:id ORDER BY dateEnvoi');
			$req->bindValue(':id',$id);
			$req->execute();
			$messages = $req->fetchAll();
	}
	else
	{
		header("Topics.php");
	}
//---------------------------------------------------------
	

 ?>
  <div class="container top-page">
        <div class="row">
            <div class="col">
                <!--Messages are here !-->
                <!--A message-->
				<?php foreach($messages as $message):
				?>
                <div class="row message">
                    <!--Information about the message-->
                    <div  class="col-md-2 user-info ">
                        <div class="row user-name">
                            <div class="col"><p><?php echo $message['prenom'].' '.$message['nom']?></p></div>
                        </div>
                        <div class="row message-date">
                            <div class="col-md">Posté le : </div>
                            <div class="col-md"><?php echo $message['dateEnvoi']?></div>
                        </div>
                    </div>

                    <!--Content of the message-->
                    <div class="col-md-9 message-content">
                            <?php echo $message['message'];?>
                    </div>
                </div>
				<?php endforeach;?>
				 <div class="row new-message">
                    <div class="col">
						<form method="post" action="Forum.php">
							<textarea name="ecriture" placeholder="Nouveau message" cols="80" rows="7"></textarea>
							<input type="hidden" name="a_recup" value="<?php echo $_POST['a_recup'];?>"/>
							<input type="submit" value="Poster"/>
						</form>
                    </div>
                </div>

                <!--Navigation par page-->
                <div class="row">
                    <div class="offset-md-1 col-md-2">
                        <form action="">
                            <label for="nb_page">Nombre de messages par page</label>
                            <select name="nb_page" size="1">
                                <option>20
                                <option>50
                                <option>100
                            </select><br>
                        </form>
                    </div>
                    <div class="offset-md-2 col-mad-1">
                        prev<br>
                        <button><</button>
                    </div>
                    <div class="offset-md-1 col-mad-1">
                        next<br>
                        <button>></button>
                    </div>
                    
                    
                    </div>
            </div>
        </div>
  </div>








  <footer>
    <?php require_once ('footer.html') ?>
  </footer>
</body>

</html>