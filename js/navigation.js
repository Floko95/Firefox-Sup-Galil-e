$('#profil').click(function(){
	if (document.getElementById("connexion").style.display == "block"){
		document.getElementById("connexion").style.display = "none";
	}
	else{
		document.getElementById("connexion").style.display = "block";
	}
});


$('#hamburger').click(function(){
	if ($( "nav ul ul" ).css( "display" ) == "none"){
		$( "#hamburger" ).css( "background-image", "url(../img/nav-hamburger-active.png)" );
		$( "nav ul ul" ).css( "display", "block" );
	}
	else{
		$( "#hamburger" ).css( "background-image", "url(../img/nav-hamburger.png)" );
		$( "nav ul ul" ).css( "display", "none" );
	}
});