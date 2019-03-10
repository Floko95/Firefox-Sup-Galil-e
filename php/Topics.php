<!DOCTYPE html>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="../css/main.css">
    <link rel="stylesheet" type="text/css" href="../css/forum.css">
</head>

<body>
<?php require_once ('navigation.html') ?>
<!-- Barre de navigation -->


<?php require_once 'inc/serveur.php' ?>
<!-- ------------------------------------FORUM GENERAL------------------------- -->
<?php $req = $bdd->prepare('SELECT idTopics,topic,dateCreation,nom,prenom FROM Topics NATURAL JOIN ETUDIANTS WHERE general=1 ORDER BY dateCreation DESC');
	$req->execute();
	$topics = $req->fetchAll();?>

<div class ="top-page"></div>
<div class="row">
    <div class="offset-md-2 col-md-8 add-topic">
        <a href="creation_topics.php" class="btn btn-warning"><span class="glyphicon glyphicon-plus-sign"></span> Créer topic</a>
    </div>
</div>
<div class="row">
	<div class="offset-md-2 col-md-8 block">
		<div class="row info-categorie">
			<div class=" col-md-8 general-categorie">Forum Général</div>
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
				<input type="submit" value="
				<?php echo $topic['topic']; ?>"/>
			
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
<?php $req = $bdd->prepare('SELECT idTopics,topic,dateCreation,nom,prenom FROM Topics NATURAL JOIN ETUDIANTS WHERE general=0 ORDER BY dateCreation DESC');
	$req->execute();
	$topics = $req->fetchAll();?>
	

<div class="row">
	<div class="offset-md-2 col-md-8 block">
		<div class="row info-categorie">
			<div class=" col-md-8 general-categorie">Forum Informatique</div>
			<div class=" col-md-2 auteur-categorie">Auteur</div>
			<div class=" col-md-2 date-categorie">Date de dernière modifiaction</div>
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
			<input type="submit" value="
			<?php echo $topic['topic']; ?>"/>
			
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
<br><br><br><br><br><br><br><br><br><br><br><br>
<br><br><br><br><br><br><br><br>
  <footer>
    <?php require_once ('footer.html') ?>
  </footer>
</body>

</html>