 <?php require_once 'inc/serveur.php' ;?>
 <?php session_start(); ?>

<?php 


//RECUPERATION ID TOPIC-------------------------------------------------------------------------

	if (isset($_GET['id'])and trim($_GET['id']!=''))
	{
		$id =intval($_GET['id']);// la requete sql a besoin d'un entier or le $post a_recup est un string

		$req = $bdd->prepare('SELECT prenom,nom,message,dateEnvoi FROM messages NATURAL JOIN etudiants WHERE  idTopics =:id ORDER BY dateEnvoi');
			$req->bindValue(':id',$id);
			$req->execute();
			$messages = $req->fetchAll();
			$totm=count($messages);
	}
	else
	{
		header("Topics.php");
	}
//---------------------------------------------------------
//----------------------RECUPERATION_COOKIE-------------------------
if (isset($_COOKIE['nbMessages'])) {
	//var_dump($_COOKIE['nbMessages']);

}
else {
	//Notre cookie n'est pas déclaré


	$temps = 365*24*3600;//cookie d'un an
	setcookie ("nbMessages", "20", time() + $temps);
	}


 //---------------------------------------------------------------------------
 $page = intval($_GET['page']);

 $nbm = intval($_GET['nbm']);
	$i=1;
 ?>







<!--Messages are here !-->
                <!--A message-->
				<?php foreach($messages as $message):
					if ($i > ($nbm*$page) - $nbm and $i <= ($nbm*$page)):
				?>
                <div class="row message">
                    <!--Information about the message-->
                    <div  class="col-md-2 user-info ">
                        <div class="row user-name">
                            <div class="col"><?php echo $message['prenom'].' '.$message['nom']?></div>
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
				<?php 
				
				endif;
				$i++;
				endforeach;?>
				<script> init_totm(<?php echo $totm;?>);</script>
				 