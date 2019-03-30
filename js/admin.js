// AJAX pour l'affichage des étudiants qui possèdent un rôle
function afficheEtudiants(role){
	if (document.getElementById('middle'+role).style.display == 'block'){
		document.getElementById('middle'+role).style.display = 'none';
	}
	else {
		document.getElementById('middle'+role).style.display = 'block';
		if (!rolesDejaOuvert[role]){
			rolesDejaOuvert[role] = true;
			$('#middle'+role).html('<div class="loader"></div>');
			switch (role) {
				case 'role2':
					$('#middle'+role).load("inc/role-etudiant.php"); break;
				case 'role3':
					$('#middle'+role).load("inc/role-ancienetudiant.php"); break;
				case 'role4':
					$('#middle'+role).load("inc/role-anciencp2i.php"); break;
				case 'role5':
					$('#middle'+role).load("inc/role-enattente.php"); break;
				case 'role6':
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
    $('#'+roles[i].id).click(function(){ afficheEtudiants(this.id); });
}

// Affichage du contenu pour créer un rôle, un article, un club, une actualité...
$('#contenuTitle').click(function(){
	if (document.getElementById('creerRoleMiddle').style.display == 'block'){
		document.getElementById('creerRoleMiddle').style.display = 'none';
	}
	else {
		document.getElementById('creerRoleMiddle').style.display = 'block';
	}
});

// AJAX lorsqu'on choisit une image pour créer une actualité, un article ou un club
$('#selectImage').click(function(){ 
	$('#apercuImage').load('inc/afficher-image.php?image=' + $('#selectImage').val());
});