// Formulaire d'inscription
function changeState(form) {
	if(form.regagree.checked == true) {form.inscription.disabled = false }
	if(form.regagree.checked == false) {form.inscription.disabled = true }
}

$('#check').click(function(){changeState(this.form);});

$('#formation').click(function(){
	if ($("#formation").val() == "CP2I") {
		document.getElementById('rangAncienCP2I').style.display = 'none';
		$('#non').prop('checked', true);
	}
	else {
		document.getElementById('rangAncienCP2I').style.display = 'block';
	}
});



// Formulaire de modification de profil
$('#cp2iterminee').click(function(){
	if($('#cp2iterminee').is(':checked') == true) { 
		document.getElementById('nouvelleFormation').style.display = 'block';
	} else {
		document.getElementById('nouvelleFormation').style.display = 'none';
	}
});