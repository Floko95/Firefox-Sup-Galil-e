DROP TABLE IF EXISTS attributionDroitsAuxRoles;
CREATE TABLE attributionDroitsAuxRoles (
idRoles int unsigned NOT NULL,
idDroits int unsigned NOT NULL,
CONSTRAINT PK_aDAR PRIMARY KEY (idRoles, idDroits),
CONSTRAINT FK_aDAR_idRoles FOREIGN KEY (idRoles) REFERENCES roles (idRoles) ON DELETE CASCADE,
CONSTRAINT FK_aDAR_idDroits FOREIGN KEY (idDroits) REFERENCES droits (idDroits)  ON DELETE CASCADE
);


DROP TABLE IF EXISTS attributionRolesAuxEtudiants;
CREATE TABLE attributionRolesAuxEtudiants (
id int unsigned NOT NULL,
idRoles int unsigned NOT NULL,
CONSTRAINT PK_aRAE PRIMARY KEY (id, idRoles),
CONSTRAINT FK_aRAE_id FOREIGN KEY (id) REFERENCES etudiants (id) ON DELETE CASCADE,
CONSTRAINT FK_aRAE_idRoles FOREIGN KEY (idRoles) REFERENCES roles (idRoles) ON DELETE CASCADE
);


DROP TABLE IF EXISTS IDEES;
CREATE TABLE IDEES (
idIdees int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
id int unsigned NOT NULL,
ideeTitre varchar(50) NOT NULL,
idee varchar(1000) NOT NULL,
dateIdee timestamp,
CONSTRAINT FK_idees_id FOREIGN KEY (id) REFERENCES etudiants (id) ON DELETE CASCADE
);


DROP TABLE IF EXISTS MINIJEU;
CREATE TABLE MINIJEU (
id int unsigned NOT NULL PRIMARY KEY,
score int unsigned NOT NULL,
dateScore timestamp,
CONSTRAINT FK_minijeu_id FOREIGN KEY (id) REFERENCES etudiants (id) ON DELETE CASCADE
);


DROP TABLE IF EXISTS etudiants;
CREATE TABLE etudiants (
id int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
mailUniv varchar(70) NOT NULL,
mailPerso varchar(70),
nom varchar(30) NOT NULL,
prenom varchar(30) NOT NULL,
numero int unsigned NOT NULL,
mdp varchar(255) NOT NULL,
formation varchar(4) DEFAULT NULL,
promotion year(4) DEFAULT NULL,
dateInscription timestamp NOT NULL,
etat int(1) DEFAULT 0,
code varchar(255),
typeCode int(1) NOT NULL,
dateMail timestamp
);


DROP TABLE IF EXISTS droits;
CREATE TABLE droits (
idDroits int unsigned NOT NULL PRIMARY KEY,
droit varchar(35) NOT NULL UNIQUE,
descriptionDroit varchar(255)
);


DROP TABLE IF EXISTS roles;
CREATE TABLE roles (
idRoles int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
role varchar(30) NOT NULL UNIQUE,
descriptionRole varchar(255),
supprimable int (1) DEFAULT 0
);


DROP TABLE IF EXISTS topics;
CREATE TABLE topics (
  idTopics int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  id int(10) UNSIGNED NOT NULL,
  topic varchar(255) NOT NULL,
  dateCreation timestamp NOT NULL,
  general tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 si topic réservé au génral, 0 si réservé à la filière du créateur',
  PRIMARY KEY (idTopics),
  KEY FK_topics_id (id) 
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS tags;
CREATE TABLE IF NOT EXISTS tags (
  idTags int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  idTopics int(10) UNSIGNED NOT NULL,
  tag varchar(20) NOT NULL,
  PRIMARY KEY (idTags),
  KEY FK_tags_idTags (idTopics)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS TOURNOI;
CREATE TABLE TOURNOI (
filiere varchar(20) PRIMARY KEY,
score int DEFAULT 0 NOT NULL,
visible int(1) DEFAULT 0 NOT NULL
);


DROP TABLE IF EXISTS IMAGES;
CREATE TABLE IMAGES (
idImages int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
image varchar(35) NOT NULL
);



INSERT INTO DROITS (idDroits, droit, descriptionDroit) VALUES
	(1, 'Créer un rôle', 'Permet de créer un rôle et de lui attribuer des droits'),
	(2, 'Supprimer un rôle', 'Permet de supprimer un rôle précédemment créé'),
	(3, 'Attribuer un rôle', 'Permet d\'attribuer un rôle à un étudiant, il faut posséder tous les droits de ce rôle'),
	(4, 'Retirer un rôle', 'Permet de retirer un rôle à un étudiant, il faut posséder tous les droits de ce rôle'),
	(5, 'Valider une inscription', ''),
	(6, 'Refuser une inscription', ''),
	(7, 'Modifier le profil d\'un étudiant', 'Permet de modifier les informations entrées lors de l\'inscription d\un étudiant'),
	(8, 'Bannir un étudiant', 'L\'étudiant ne pourra plus accéder à son compte'),
	(9, 'Supprimer un compte', 'Permet de supprimer le compte d\'un étudiant banni'),
	(10, 'Accéder à tout le forum', 'L\'étudiant pourra voir toutes les sections du forum, et pas seulement celles de sa formation'),
	(11, 'Rendre muet un étudiant', 'L\'étudiant ne pourra plus discuter sur le forum, mais pourra toujours voir les messages'),
	(12, 'Supprimer un topic du forum', ''),
	(13, 'Supprimer un message du forum', ''),
	(14, 'Gérer les actualités', 'Permet de créer et supprimer des actualités'),
	(15, 'Gérer la boutique', 'Permet de créer et supprimer des articles dans la boutique'),
	(16, 'Gérer le tournoi', 'Permet de modifier les scores des évènements inter-filières');
	(17, 'Ajouter une image', 'Permet d\'ajouter une image pour l\'utiliser dans la boutique ou les actualités');
	(18, 'Gérer la boîte à idées', 'Permet de supprimer les idées postées dans la boîte à idées');
	(19, 'Gérer le mini-jeu', 'Permet de supprimer des records et de réinitialiser tous les records');
	

INSERT INTO ROLES (idRoles, role, descriptionRole) VALUES 
	(1, 'Administrateur', 'Possède tous les droits'),
	(2, 'Etudiant', 'Simple étudiant'),
	(3, 'Ancien étudiant', 'Etudiant ayant obtenu son diplôme'),
	(4, 'Ancien CP2I', 'Etudiant qui a terminé sa CP2I'),
	(5, 'En attente', 'Etudiant ayant confirmé son adresse mail, son inscription doit maintenant être validée par un étudiant possédant les droits requis'),
	(6, 'Banni', 'Etudiant ou ancien étudiant dont le compte a été banni');
	
	
INSERT INTO attributionDroitsAuxRoles (idRoles, idDroits) VALUES
	(1, 1),
	(1, 2),
	(1, 3),
	(1, 4),
	(1, 5),
	(1, 6),
	(1, 7),
	(1, 8),
	(1, 9),
	(1, 10),
	(1, 11),
	(1, 12),
	(1, 13),
	(1, 14),
	(1, 15),
	(1, 16),
	(1, 17),
	(1, 18),
	(1, 19);
	
	
INSERT INTO TOURNOI (filiere) VALUES 
	('CP2I'),
	('Energétique'),
	('Informatique'),
	('Mathématiques'),
	('Télécommunications'),
	('Instrumentation');
