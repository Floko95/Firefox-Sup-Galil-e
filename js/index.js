function changeState(form) {
	if(form.regagree.checked == true) {form.inscription.disabled = false }
	if(form.regagree.checked == false) {form.inscription.disabled = true }
}

if (document.getElementById("check") != null){
	document.getElementById("check").addEventListener("click", function(){changeState(this.form);});
}