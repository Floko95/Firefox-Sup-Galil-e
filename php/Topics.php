<?php
session_start();
if (!(isset($_SESSION['id']))) {
	header ('Location: index.php');
	exit();
}
?>

<?php require_once 'inc/serveur.php' ;?>


<!DOCTYPE html>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="../css/main.css">
    <link rel="stylesheet" type="text/css" href="../css/forum.css">
</head>

<body>

<?php require_once ('navigation.php') ?>
<!-- Barre de navigation -->




<!-----------------------------------CREATION TOPIC---------------------------------------->
<?php if(isset($_POST['ecriture']) and isset($_POST['categorie']) and trim($_POST['ecriture']!='') and isset($_POST['title']) and trim($_POST['title']!=''))
{
	if($_POST['categorie'] == 'general')
	{
		$req = $bdd->prepare('INSERT INTO `topics`(`id`, `topic`, `dateCreation`, `general`) VALUES (:id,:top,:date,1)');
	}
	elseif ($_POST['categorie'] == 'filliere') 
	{
		$req = $bdd->prepare('INSERT INTO `topics`(`id`, `topic`, `dateCreation`, `general`) VALUES (:id,:top,:date,0)');
	}
	$req->execute(array(
			
			'id' => intval($_SESSION['id']),
			'top' =>$_POST['title'],
			'date' => date('Y-m-d H:i:s')
			));
		$id = $bdd->lastInsertId();
	//Creation premier message----------------------------
	$req = $bdd->prepare('INSERT INTO `messages`(`id`, `idTopics`, `message`, `dateEnvoi`) VALUES (:id,:indtopic,:mess,:date)');
			$req->execute(array(
			
			'id' => intval($_SESSION['id']),
			'indtopic' => $id,
			'mess' => $_POST['ecriture'],
			'date' => date('Y-m-d H:i:s')
			));
	
			
}
?>



<!-- ------------------------------------FORUM GENERAL------------------------- -->
<?php $req = $bdd->prepare('SELECT idTopics,topic,dateCreation,nom,prenom FROM Topics NATURAL JOIN ETUDIANTS WHERE general=1 ORDER BY dateCreation DESC');
	$req->execute();
	$topics = $req->fetchAll();?>

<div class ="top-page"></div>

<div class="row">
    <div class="offset-md-2 col-md-2 add-topic">
        <a href="creation_topics.php" class="btn btn-warning"><span class="glyphicon glyphicon-plus-sign"></span> Créer topic</a>
    </div>
	<div class="offset-md-5 col-md-2 add-topic">
        <a href="boite_a_idees.php" class="btn btn-warning"><span class="glyphicon glyphicon-plus-sign"></span> Boîte à idées</a>
    </div>
</div>


<div class="row title">
	<div class="offset-md-2 col-md-8">
		<div class="row block">
			<div class="col-md-1" id="general-bouton"><span class="fas fa-sort-up fa-2x"></span></div>
			<div class="col-md-10">Forum Général</div>
		</div>
	</div>
</div>

<div class="row" id="general-messages" >
	<div class="offset-md-2 col-md-8 block">
		<div class="row info-categorie">
			<div class=" col-md-8 general-categorie">Forum</div>
			<div class=" col-md-2 auteur-categorie">Auteur</div>
			<div class=" col-md-2 date-categorie">Date de dernière modifiaction</div>
		</div>
		<?php foreach($topics as $topic):
		$req = $bdd->prepare('SELECT * from tags where idTopics= ?');
		$req->execute(array($topic['idTopics']));
		$tags= $req->fetchAll();
		?>
		
		<div class="row topic">
			<div class=" col-md-6 topic-subject">
				<form method="post" action="Forum.php">
				<input type="hidden" value="
				<?php echo $topic['idTopics']; ?>" name="a_recup"/>
				<button type="submit" value="
				<?php echo $topic['topic']; ?>"><?php echo $topic['topic']; ?></button>
			
				</form>
			</div>
			<div class="col-md-2 tag">
				<?php
				$i=0;
				foreach($tags as $tag) {
					if($i<5)
						echo $tag['tag'].' ';
					$i++;
				}?>
				
			</div>
			<div class=" col-md-2 auteur-topic"><?php echo $topic['prenom'].' '. $topic['nom']; ?></div>
			<div class=" col-md-2 date-topic"><?php echo $topic['dateCreation'];?> </div>
			
			
		</div>
		<?php endforeach; ?>
	</div>
</div>
<!-- ------------------------------------FORUM FILIERE------------------------- -->









<?php $req = $bdd->prepare('SELECT idTopics,topic,dateCreation,nom,prenom FROM Topics NATURAL JOIN ETUDIANTS WHERE general=0 AND formation = ? ORDER BY dateCreation DESC');
	$req->execute(array($_SESSION['formation']));
	$topics = $req->fetchAll();
	//on affiche le forum de la fillière de la personne connectée , toutes fillières confondues : info,cp2i, etc
	?>
	
<div class="row title">
	<div class="offset-md-2 col-md-8">
		<div class="row block">
			<div class="col-md-1" id="informatique-bouton"><span class="fas fa-sort-up fa-2x"></span></div>
			<div class="col-md-10 ">Forum <?php echo $_SESSION['formation']; ?></div>
		</div>
	</div>
</div>


<div class="row" id="informatique-messages">
	<div class="offset-md-2 col-md-8 block">
		<div class="row info-categorie">
			<div class=" col-md-8 general-categorie">Forum</div>
			<div class=" col-md-2 auteur-categorie">Auteur</div>
			<div class=" col-md-2 date-categorie">Date de création</div>
		</div>
		

		<?php foreach($topics as $topic):
		$req = $bdd->prepare('SELECT * from tags where idTopics= ?');
		$req->execute(array($topic['idTopics']));
		$tags= $req->fetchAll();
		?>
		<div class="row topic">
			<div class=" col-md-6 topic-subject"><form method="post" action="Forum.php">
			<input type="hidden" value="
			<?php echo $topic['idTopics']; ?>" name="a_recup"/>
			<button type="submit" value="
			<?php echo $topic['topic']; ?>"><?php echo $topic['topic']; ?></</button>
			
			</form></div>
			<div class="col-md-2 tag">
			<?php
			$i=0;
			foreach($tags as $tag) {
				if($i<5)
					echo $tag['tag'].' ';
				$i++;
			}?>
				
			</div>
			<div class=" col-md-2 auteur-topic"><?php echo $topic['prenom'].' '.$topic['nom']; ?></div>
			<div class=" col-md-2 date-topic"><?php echo $topic['dateCreation'];?> </div>
		</div>
		<?php endforeach; ?>
	</div>
</div>

<!-- ------------------------------------FORUM CP2i------------------------- -->


<?php $reqrole = $bdd->prepare('SELECT COUNT(*) FROM attributionRolesAuxEtudiants  WHERE id = ? AND idRoles = 4');		//4 = role "ancien cp2i"
$reqrole->execute(array($_SESSION['id']));
$check_cp2i = $reqrole->fetch();
?>

<?php if($_SESSION['formation'] != 'CP2I' and ($check_cp2i[0] != 0) ): // si l'éleve n'est pas un cp2i MAIS qu'il est un ancien cp2i, alors on affiche le forum cp2i ?> 

<?php $req = $bdd->prepare('SELECT idTopics,topic,dateCreation,nom,prenom FROM Topics NATURAL JOIN ETUDIANTS WHERE general=0 AND formation = "CP2I" ORDER BY dateCreation DESC');
	$req->execute();
	$topics = $req->fetchAll();?>
	
<div class="row title">
	<div class="offset-md-2 col-md-8">
		<div class="row block">
			<div class="col-md-1" id="cp2i-bouton"><span class="fas fa-sort-up fa-2x"></span></div>
			<div class="col-md-10 ">Forum CP2I</div>
		</div>
	</div>
</div>


<div class="row" id="cp2i-messages">
	<div class="offset-md-2 col-md-8 block">
		<div class="row info-categorie">
			<div class=" col-md-8 general-categorie">Forum</div>
			<div class=" col-md-2 auteur-categorie">Auteur</div>
			<div class=" col-md-2 date-categorie">Date de création</div>
		</div>
		

		<?php foreach($topics as $topic):
		$req = $bdd->prepare('SELECT * from tags where idTopics= ?');
		$req->execute(array($topic['idTopics']));
		$tags= $req->fetchAll();
		?>
		<div class="row topic">
			<div class=" col-md-6 topic-subject"><form method="post" action="Forum.php">
			<input type="hidden" value="
			<?php echo $topic['idTopics']; ?>" name="a_recup"/>
			<button type="submit" value="
			<?php echo $topic['topic']; ?>"><?php echo $topic['topic']; ?></</button>
			
			</form></div>
			<div class="col-md-2 tag">
			<?php
			$i=0;
			foreach($tags as $tag) {
				if($i<5)
					echo $tag['tag'].' ';
				$i++;
			}?>
				
			</div>
			<div class=" col-md-2 auteur-topic"><?php echo $topic['prenom'].' '.$topic['nom']; ?></div>
			<div class=" col-md-2 date-topic"><?php echo $topic['dateCreation'];?> </div>
		</div>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>
<!-- ------------------------------------FORUM ADMIN------------------------- -->
<!----working on it ;) ----------------->


  <footer>
    <?php require_once ('footer.html') ?>
  </footer>
  <script type="text/javascript" src="../js/forum.js"></script>
</body>

</html>