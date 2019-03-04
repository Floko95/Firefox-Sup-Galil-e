<!DOCTYPE html>

<html>
<head>
	<!--<link rel="stylesheet" type="text/css" href="../css/design.css">-->
	<link rel="stylesheet" type="text/css" href="../css/main.css">
</head>
<body>

	<?php require_once ('navigation.html') ?>


	<div id="connexion"></div>

	<div style="top:0;z-index: -100;position: sticky;">
		<video width="100%"autoplay muted loop style="margin-top: -3%">
    		<source src="../img/composition_planete_tournante_compresse.mp4" type="video/mp4">
		</video> 
	</div>>


<script>
i = false;
function connexion(){
	if (i == false){
		document.getElementById("connexion").style.display = "block";
		i = true;
	}
	else{
		document.getElementById("connexion").style.display = "none";
		i = false;
	}
}

</script>


</body>
</html>