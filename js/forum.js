var page_actuelle = 1;
var totm;

$("#prev").css("display", "none");//page 1 , on cache le bouton précédent

function init_totm(nb)
{
	totm = nb;
	console.log("totm = " +totm);
}

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
	
	if(page_actuelle< (totm / $('#nb_page').val()))
	{
		$("#next").css("display", "block");
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
	
	if(page_actuelle>= (totm / $('#nb_page').val()))
	{
		$("#next").css("display", "none");
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


+$('.arrow-bouton').click(function(){
	if($(this).parents().parents().parents().next('.messages').css('display') == 'none'){
		console.log('Display Forum\n');
		$(this).parents().parents().parents().next('.messages').show();
		$(this).html('<span class="fas fa-sort-up fa-2x">V</span>');
	}
	else{
		console.log('Hide Forum \n'+$(this).next('.messages'));
		$(this).parents().parents().parents().next('.messages').hide();
		$(this).html('<span class="fas fa-sort-down fa-2x">/\\</span>');
	}
});
