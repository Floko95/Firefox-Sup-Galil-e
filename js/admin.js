function afficheEtudiants(role){
	if (document.getElementById('middle'+role).style.display == 'block'){
		document.getElementById('middle'+role).style.display = 'none';
	}
	else {
		document.getElementById('middle'+role).style.display = 'block';
		if (!rolesDejaOuvert[role]){
			rolesDejaOuvert[role] = true;
			$('#middle'+role).html('<img src="../../img/ajax-loader.gif">');
			switch (role) {
				case 'role2':
					$('#middle'+role).load("inc/role-etudiant.php"); break;
				case 'role3':
					$('#middle'+role).load("inc/role-ancienetudiant.php"); break;
				case 'role4':
					$('#middle'+role).load("inc/role-enattente.php"); break;
				case 'role5':
					$('#middle'+role).load("inc/role-banni.php"); break;
				default:
					i = 0;
					trouve = false;
					while(!trouve){
						i++;
						if (role == ('role'+i)){
							trouve = true;
						}
					}
					$('#middle'+role).load("inc/role-autres.php?idRoles="+i); break; // a changer
			}
		}
	}
}

var roles = document.getElementsByClassName("roleTitle");
var rolesDejaOuvert = [];
for (i = 0; i < roles.length; i++) {
	rolesDejaOuvert[roles[i].id] = false;
}

for (i = 0; i < roles.length; i++) {
	
    $('#'+roles[i].id).click(function(){
													afficheEtudiants(this.id);
													/*if (!(rolesDejaOuvert[i])){
														rolesDejaOuvert[i] = true;
														console.log('#middle'+this.id);
														$('#middle'+this.id).html('a'); //<img src="http://www.mediaforma.com/sdz/jquery/ajax-loader.gif">
														//....load
													}*/
												}); // roles[i].addEventListener("click", 
}
$('#contenuTitle').click(function(){
	if (document.getElementById('creerRoleMiddle').style.display == 'block'){
		document.getElementById('creerRoleMiddle').style.display = 'none';
	}
	else {
		document.getElementById('creerRoleMiddle').style.display = 'block';
	}
});


function cocherTout(role)
{
   var cases = document.getElementsByTagName('input');   // on recupere tous les INPUT
   for(var i=0; i<cases.length; i++)     // on les parcourt
      if(cases[i].type == 'checkbox')     // si on a une checkbox...
         cases[i].checked = true;     // ... on la coche
}



// AJAX :
$(function() {
        $('.roleTitle').click(function() {
          $('#roleMiddle1').load('maj1.html', function() {
            alert('La première zone a été mise à jour');
          });
        });

        $('#majDeuxieme').click(function() {
          $('#deuxieme').load('maj2.html', function() {
            alert('La deuxième zone a été mise à jour');
          });
        });
      });