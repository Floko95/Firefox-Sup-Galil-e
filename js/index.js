var actualites = document.getElementsByClassName("actualite");

for (i = 0; i < actualites.length; i++) {
    $( '#'+actualites[i].id ).click(function(){ 
		if (document.getElementById('contenu'+this.id).style.display == 'block'){
			document.getElementById('contenu'+this.id).style.display = 'none';
		} else {
			document.getElementById('contenu'+this.id).style.display = 'block';
		}
	});
}