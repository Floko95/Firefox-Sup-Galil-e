DROP TABLE IF EXISTS attributionDroitsAuxRoles;
DROP TABLE IF EXISTS attributionRolesAuxEtudiants;
DROP TABLE IF EXISTS ACTUALITES;
DROP TABLE IF EXISTS IDEES;
DROP TABLE IF EXISTS MINIJEU;
DROP TABLE IF EXISTS ETUDIANTS;
DROP TABLE IF EXISTS DROITS;
DROP TABLE IF EXISTS ROLES;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS topics;
DROP TABLE IF EXISTS ARTICLES;
DROP TABLE IF EXISTS CLUBS;
DROP TABLE IF EXISTS IMAGES;
DROP TABLE IF EXISTS TOURNOI;





CREATE TABLE ETUDIANTS (
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


CREATE TABLE DROITS (
	idDroits int unsigned NOT NULL PRIMARY KEY,
	droit varchar(35) NOT NULL UNIQUE,
	descriptionDroit varchar(255)
);


CREATE TABLE ROLES (
	idRoles int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	role varchar(30) NOT NULL UNIQUE,
	descriptionRole varchar(255),
	supprimable int (1) DEFAULT 0
);


CREATE TABLE attributionDroitsAuxRoles (
	idRoles int unsigned NOT NULL,
	idDroits int unsigned NOT NULL,
	CONSTRAINT PK_aDAR PRIMARY KEY (idRoles, idDroits),
	CONSTRAINT FK_aDAR_idRoles FOREIGN KEY (idRoles) REFERENCES ROLES (idRoles) ON DELETE CASCADE,
	CONSTRAINT FK_aDAR_idDroits FOREIGN KEY (idDroits) REFERENCES DROITS (idDroits)  ON DELETE CASCADE
);


CREATE TABLE attributionRolesAuxEtudiants (
	id int unsigned NOT NULL,
	idRoles int unsigned NOT NULL,
	CONSTRAINT PK_aRAE PRIMARY KEY (id, idRoles),
	CONSTRAINT FK_aRAE_id FOREIGN KEY (id) REFERENCES ETUDIANTS (id) ON DELETE CASCADE,
	CONSTRAINT FK_aRAE_idRoles FOREIGN KEY (idRoles) REFERENCES ROLES (idRoles) ON DELETE CASCADE
);


CREATE TABLE topics (
	idTopics int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	id int(10) unsigned NOT NULL,
	topic varchar(255) NOT NULL,
	dateCreation timestamp NOT NULL,
	filliere varchar(10) NOT NULL,
	dateDerniereModif timestamp NOT NULL
);


CREATE TABLE IF NOT EXISTS messages (
  idMessages int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  id int UNSIGNED NOT NULL,
  idTopics int UNSIGNED NOT NULL,
  message varchar(2000) NOT NULL,
  dateEnvoi timestamp NOT NULL,
  CONSTRAINT FK_messages_idTopics FOREIGN KEY (idTopics) REFERENCES topics (idTopics) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS tags (
	idTags int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	idTopics int(10) unsigned NOT NULL,
	tag varchar(20) NOT NULL,
	CONSTRAINT FK_tags_idTopics FOREIGN KEY (idTopics) REFERENCES topics (idTopics) ON DELETE CASCADE
);


CREATE TABLE IMAGES (
	idImages int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	image varchar(35) NOT NULL UNIQUE
);


DROP TABLE IF EXISTS ACTUALITES;
CREATE TABLE ACTUALITES (
	idActualites int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	id int unsigned NOT NULL,
	idImages int unsigned,
	createur varchar(100),
	actualite varchar(100) NOT NULL,
	descriptionActualite varchar(1000),
	CONSTRAINT FK_actualites_id FOREIGN KEY (id) REFERENCES ETUDIANTS (id) ON DELETE CASCADE,
	CONSTRAINT FK_actualites_idImages FOREIGN KEY (idImages) REFERENCES IMAGES (idImages) ON DELETE CASCADE
);


CREATE TABLE ARTICLES (
	idArticles int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	idImages int unsigned,
	article varchar(100) NOT NULL UNIQUE,
	descriptionArticle varchar(1000),
	prix float(5,2) NOT NULL,
	CONSTRAINT FK_articles_idImages FOREIGN KEY (idImages) REFERENCES IMAGES (idImages) ON DELETE CASCADE
);


CREATE TABLE CLUBS (
	idClubs int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	idImages int unsigned,
	club varchar(100) NOT NULL UNIQUE,
	descriptionClub varchar(1000),
	CONSTRAINT FK_clubs_idImages FOREIGN KEY (idImages) REFERENCES IMAGES (idImages) ON DELETE CASCADE
);


CREATE TABLE IDEES (
	idIdees int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
	id int unsigned NOT NULL,
	idee varchar(50) NOT NULL,
	ideeDescription varchar(1000) NOT NULL,
	dateIdee timestamp,
	CONSTRAINT FK_idees_id FOREIGN KEY (id) REFERENCES ETUDIANTS (id) ON DELETE CASCADE
);


CREATE TABLE TOURNOI (
	filiere varchar(20) PRIMARY KEY,
	score int DEFAULT 0 NOT NULL,
	visible int(1) DEFAULT 0 NOT NULL
);


CREATE TABLE MINIJEU (
	id int unsigned NOT NULL PRIMARY KEY,
	score int unsigned NOT NULL,
	dateScore timestamp,
	CONSTRAINT FK_minijeu_id FOREIGN KEY (id) REFERENCES ETUDIANTS (id) ON DELETE CASCADE
);





INSERT INTO DROITS (idDroits, droit, descriptionDroit) VALUES
	(1, 'Créer un rôle', 'Permet de créer un rôle et de lui attribuer des droits'),
	(2, 'Supprimer un rôle', 'Permet de supprimer un rôle précédemment créé'),
	(3, 'Attribuer un rôle', 'Permet d\'attribuer un rôle à un étudiant, il faut posséder tous les droits de ce rôle'),
	(4, 'Retirer un rôle', 'Permet de retirer un rôle à un étudiant, il faut posséder tous les droits de ce rôle'),
	(5, 'Valider une inscription', 'Permet de finaliser l\'inscription d\'un étudiant qui a validé son adresse mail'),
	(6, 'Refuser une inscription', 'Permet de refuser l\'inscription d\'un étudiant qui a validé son adresse mail'),
	(7, 'Modifier le profil d\'un étudiant', 'Permet de modifier les informations entrées lors de l\'inscription d\un étudiant'),
	(8, 'Bannir un étudiant', 'L\'étudiant ne pourra plus accéder à son compte'),
	(9, 'Supprimer un compte', 'Permet de supprimer le compte d\'un étudiant banni'),
	(10, 'Accéder à tout le forum', 'L\'étudiant pourra voir toutes les sections du forum, et pas seulement celles de sa formation'),
	(11, 'Rendre muet un étudiant', 'L\'étudiant ne pourra plus discuter sur le forum, mais pourra toujours voir les messages'),
	(12, 'Supprimer un topic du forum', 'Permet de supprimer un topic et tous les messages qui lui sont associés'),
	(13, 'Supprimer un message du forum', 'Permet de supprimer un message d\'un topic'),
	(14, 'Gérer les actualités', 'Permet de créer et supprimer des actualités'),
	(15, 'Gérer la boutique', 'Permet de créer et supprimer des articles dans la boutique'),
	(16, 'Gérer le tournoi', 'Permet de modifier les scores des évènements inter-filières'),
	(17, 'Ajouter une image', 'Permet d\'ajouter une image pour l\'utiliser dans la boutique ou les actualités'),
	(18, 'Gérer la boîte à idées', 'Permet de supprimer les idées postées dans la boîte à idées'),
	(19, 'Gérer le mini-jeu', 'Permet de supprimer des records et de réinitialiser tous les records'),
	(20, 'Gérer les clubs', 'Permet de créer et supprimer des clubs');
	

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
	(1, 19),
	(1, 20);
	
	
INSERT INTO TOURNOI (filiere) VALUES 
	('CP2I'),
	('Energétique'),
	('Informatique'),
	('Mathématiques'),
	('Télécommunications'),
	('Instrumentation');