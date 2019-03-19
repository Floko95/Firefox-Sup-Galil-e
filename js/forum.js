var page_actuelle = 1;
$("#prev").css("display", "none");//page 1 , on cache le bouton précédent


$('#nb_page').change(function(){
	console.log($(this).val());
	affichage($('#a_recup').val(),page_actuelle,$(this).val());

});
$('#prev').click(function(){
	console.log(page_actuelle -1);
	
	if(page_actuelle>1)
	{
		page_actuelle-=1;
	}
	
	if(page_actuelle ==1)
	{
		$("#prev").css("display", "none");
	}
	affichage($('#a_recup').val(), page_actuelle,$('#nb_page').val());
});
$('#next').click(function(){
	console.log(page_actuelle +1);
	
	page_actuelle+=1;
	if(page_actuelle >1)
	{
		$("#prev").css("display", "block");
	}
	affichage($('#a_recup').val(), page_actuelle,$('#nb_page').val());
});
function affichage(idtopic, page,nbm){
	$('#ajax_messages').load("affichage_messages?id="+idtopic+"&page="+page+
	"&nbm="+nbm+".php", function() {
            console.log('L\'affichage des messages ont  été mise à jour');
          });
	
};

affichage($('#a_recup').val(), page_actuelle,$('#nb_page').val())//premier affichage quand on entre sur la page