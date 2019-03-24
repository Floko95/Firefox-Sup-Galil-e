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


var ids = ['#bonjour', '#contact', '#admin', '#forum', '#boutique', '#actualites', '#equipe', '#accueil', '', 'contact', 'admin', 'forum', 'boutique', 'actualites', 'equipe'];
var tailles = []; //taille des a
var i = 0;
var j = 0;

function initTailles() {
	for (var a=0; a <= 7; a++) {
		tailles[a] = $( ids[a] ).width();
		if (tailles[a] == null) {
			tailles[a] = 0;
		}
		else {
			j++;
		}
	}
}
initTailles();

function calculEcart() {
	var wWidth = window.innerWidth;
	var lWidth = document.getElementById("left").offsetWidth;
	var rWidth = document.getElementById("right").offsetWidth;
	return wWidth - lWidth - rWidth - 5;
}

function responsive() {
	
	var ecart = calculEcart();
	
	/* Rétrécissement */
	while (ecart < 50 && i <= 14) {
		if (i == 0) {
			/* Suppression du bonjour prénom */
			if ( $( '#bonjour' ).css( "display" ) == "block" ) {
				$( '#bonjour' ).css( "display", "none" );
			}
		}
		else if (i <= 7) {
			/* Suppression des span */
			if ( $( ids[i] + ' span' ).css( "display" ) == "block" ) {
				$( ids[i] + ' span' ).css( "display", "none" );
				$( ids[i] ).css( "background-position", "center" );
			}
		}
		else if (i == 8) {
			/* Réduction de l'espace inter-images */
			$( 'nav a ~ a' ).css( "padding", "0px 0px" );
			$( 'nav a ~ a' ).css( "margin-left", "0px" );
		}
		else if (i == 9) {
			/* Affichage du menu hamburger */
			$( '#contact' ).css( "display", "none" );
			$( '#hamburger' ).css( "display", "block" );
			$( '#h-contact li' ).css( "display", "block" );
		}
		else {
			/* On complète le menu hamburger */
			if ( $( '#' + ids[i] ).css( "display" ) == "block" ) {
				$( '#' + ids[i] ).css( "display", "none" );
				$( '#h-' + ids[i] + ' li' ).css( "display", "block" );
			}
		}
		ecart = calculEcart();
		i++;
	}
	
	/* Agrandissement */
	while (ecart >= 95 && i >= 1) { // 45px par li lorqu'on a qu'une image
		if (i > 10) {
			if ( $( '#' + ids[i-1] ).css( "display" ) == "none" ) {
				$( '#h-' + ids[i-1] + ' li' ).css( "display", "none" );
				$( '#' + ids[i-1] ).css( "display", "block" );
			}
		}
		else if (i == 10) {
			$( '#hamburger' ).css( "display", "none" );
			$( '#h-contact' ).css( "display", "none" );
			$( '#contact' ).css( "display", "block" );
		}
		else if (i == 9) {
			if (ecart >= (60 + j * 10)) {
				$( 'nav a ~ a' ).css( "padding", "4px 0px" );
				$( 'nav a ~ a' ).css( "margin-left", "15px" );
			}
			else {
				break;
			}
		}
		else if ( i < 9 && i > 1) {
			if (ecart >= (tailles[i-1] + 5)) {
				if (tailles[i-1] > 0) {
					$( ids[i-1] + ' span' ).css( "display", "block" );
					$( ids[i-1] ).css( "background-position", "left" );
				}
			}
			else {
				break;
			}
		}
		else {
			if (ecart >= (tailles[0] + 5)) {
				if (tailles[0] > 0) {
					$( '#bonjour' ).css( "display", "block" );
				}
			}
			else {
				break;
			}
		}
		ecart = calculEcart();
		i--;
	}

}
// On lie l'événement resize à la fonction
window.addEventListener('resize', responsive, false);