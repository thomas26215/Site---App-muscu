Groupe 11


**Documentation Technique du Projet [Rezisten]**


1. [Introduction](#introduction)
2. [Aperçu du Projet](#aperçu-du-projet)
3. [Bases de Données](#bases-de-données)
   - 3.1. [Structure des Tables SQL](#31-structure-des-tables-sql)
      - 3.1.1. [Fichier create.sql](#311-fichier-createsql)
   - 3.2. [Relations entre les Tables](#32-relations-entre-les-tables)
   - 3.3. [Cas d'utilisations des tables](#33cas-dutilisations-des-tables)
4. [Fonctions de classe](#fonctions-de-classes)
    - 4.1. [Bases de données]()
5. [Fonctionnalités](#fonctionnalités)
6. [Scénarios d'Utilisation](#scénarios-dutilisation)
7. [Conclusion](#conclusion)


## Introduction
Ce document présente les spécifications techniques et fonctionnelles du projet [Rezisten]. Il décrit la structure des bases de données, les principales fonctionnalités et le déroulement des interactions utilisateur.


## Aperçu du Projet
Description à fournir .


## Bases de Données

### 3.1 Structure des tables SQL

#### 3.1.1. **Fichier `create.sql**


- **TABLE** `Utilisateurs` :
	- ``id`` **SERIAL PRIMARY KEY** : Identifiant de l'utllisateur
	- `pseudo` **VARCHAR(20) NOT NULL** : Pseudo de l'utilisateur
	- ``datenaiss`` **DATE NOT NULL** : Date de naissance de l'utilisateur
	- ``mail`` **VARCHAR(255) NOT NULL** : Adresse Mail de l'utilisateur
	- ``mot_de_passe`` **TEXT NOT NULL** : Mot de passe haché
	- ``role`` **CHAR(1) NOT NULL** : Indique si l'utilisateur est un joueurn un créateur ou un  (*j*, *c*, ou *a*)
---


- Table des demandes de créateurs
CREATE TABLE DEMANDES(
`    id_utilisateur ` INTEGER PRIMARY KEY,         -- Référence un utilisateur de la 
`table UTILISATEURS

`    doc` VARCHAR(50) NOT NULL,                  -- Nom d'un fichier image qui contient la                                                                                      carte d'identité de la personne

  FOREIGN KEY (``id_utilisateur``) REFERENCES UTILISATEURS(``id``)

);



- Table des lieux associés aux histoires
CREATE TABLE LIEUX (
    ``id`` SERIAL PRIMARY KEY,   -- Identifiant unique auto-incrémenté pour chaque lieu
    	``nom`` VARCHAR,
    ``type_lieu`` VARCHAR,
    ``description`` VARCHAR,
    ``commune`` VARCHAR,
    ``coordonnee`` VARCHAR
);


- Table des chapitres
CREATE TABLE CHAPITRES (
    ``numchap`` INT PRIMARY KEY,                  -- Numéro unique pour chaque chapitre
    ``titre`` VARCHAR(50) NOT NULL                -- Titre du chapitre
);

  
- Table des histoires associées aux chapitres
CREATE TABLE HISTOIRES (
    ``id`` **SERIAL PRIMARY KEY**,                     -- Identifiant unique au sein d'un chapitre
    ``titre`` **VARCHAR(50) NOT NULL**,              -- Titre de l'histoire
    ``numchap`` **INTEGER NOT NULL**,                -- Numéro du chapitre associé à cette histoire
    ``createur`` **INTEGER**,                         -- Référence à l'identifiant du créateur dans la table                                                              CREATEURS
    ``id_lieu`` **INTEGER**,                         -- Référence à l'identifiant du lieu dans la table LIEUX
    ``background`` **varchar(50) NOT NULL**,         -- Nom de l'image d'arrière plan de l'histoire                                                                            stockée sur le serveur web
    ``visible`` **boolean**,                             -- vrai si le chapitre a été validé, faux sinon
    **FOREIGN KEY** (``id_lieu``) **REFERENCES LIEUX**(``id``),  -- Clé étrangère référencée à la table LIEUX
    **FOREIGN KEY** (``numchap``) **REFERENCES CHAPITRES**(``numchap``),  -- Clé étrangère  référencée à la table CHAPITRES
    **FOREIGN KEY** (``createur``) **REFERENCES UTILISATEURS**(``id``)   -- Clé étrangère référencée à la table CREATEURS
);


- Table des personnages du jeu
CREATE TABLE PERSONNAGES(
    ``id`` **SERIAL PRIMARY KEY**,          -- Identifiant du personnage pour faciliter l'unicité des n-uplets
    ``prenom`` **varchar(50) NOT NULL**,    -- Prenom du personnage
    ``createur`` **INTEGER**,
    ``img`` **varchar(50) NOT NULL**,        -- Nom de l'image (le modèle) du personnage stockée ailleurs
    **FOREIGN KEY** (``createur``) **REFERENCES UTILISATEURS**(``id``)
);

  
- Table des dialogues associés aux histoires
CREATE TABLE DIALOGUES (
    ``id`` **INTEGER**,                       -- Identifiant unique pour chaque dialogue
    ``id_histoire`` **INTEGER**,                     -- Référence à l'identifiant de l'histoire associée
    ``interlocuteur`` **INTEGER**,                    -- Référence au personnage qui dit ce dialogue
    ``contenu`` **VARCHAR**(400),                    -- Contenu du dialogue
    ``bonus``**BOOLEAN NOT NULL,**                 -- Dialogue étant un bonus lié à la question spécifique
    ``doublage`` **VARCHAR(10),**                   -- Nom fichier audio : clé primaire
    **PRIMARY KEY**(``id_histoire, id``),     -- Clé primaire composite pour garantir l'unicité par histoire et dialogue
    **FOREIGN KEY** (``id_histoire``) **REFERENCES HISTOIRES**(``id``),  -- Clé étrangère référencée à la table HISTOIRES
    **FOREIGN KEY** (``interlocuteur``) **REFERENCES PERSONNAGES**(``id``)

);

  
- Table des apparitions des personnages, permet de savoir dans quelle histoire se trouve quels personnages
CREATE TABLE APPARITIONS(
    ``id_histoire`` **SERIAL**,
    ``id_perso`` **SERIAL**,
    **FOREIGN KEY** (``id_histoire``) **REFERENCES HISTOIRES**(``id``),
    **FOREIGN KEY** (``id_perso``) **REFERENCES PERSONNAGES**(``id``),
    **PRIMARY KEY**(``id_histoire , id_perso``)
);

  
- Table des questions associées aux histoires
CREATE TABLE QUESTIONS (
    ``id_histoire`` **INTEGER**,                    -- Référence à l'identifiant de l'histoire associée
    ``question`` **VARCHAR(200) NOT NULL**,         -- Question liée à l'histoire
    ``reponse`` **VARCHAR(50) NOT NULL**,           -- Réponse à la question
    ``type`` **CHAR CHECK (type IN('g','s')),**     -- Type de question ('g' pour générale, 's' pour spécifique)
    **PRIMARY KEY**(``id_histoire,type``),
    **FOREIGN KEY** (``id_histoire``) **REFERENCES HISTOIRES**(``id``)  -- Clé étrangère référencée à la table HISTOIRE
);

  
- Table suivant la progression des utilisateurs dans les histoires
CREATE TABLE PROGRESSION (
    ``id_utilisateur`` *INTEGER*,                    -- Nom d'utilisateur lié à la progression
    ``id_hist`` **INTEGER**,                         -- Référence à l'identifiant de l'histoire associée
    ``statut`` **BOOLEAN DEFAULT false**,  -- Statut de progression si true : histoire finie, sinon histoire non tentée
    **FOREIGN KEY** (``id_utilisateur``) **REFERENCES UTILISATEURS**(``id``),  -- Clé étrangère référencée à la table UTILISATEURS
    **FOREIGN KEY** (``id_hist``) **REFERENCES HISTOIRES**(``id``),     -- Clé étrangère référencée à la table HISTOIRES
    **PRIMARY KEY** (``id_utilisateur, id_hist``)          -- Clé primaire composite pour garantir l'unicité par utilisateur et histoire
);

  
CREATE TABLE verifications_email (
    ``id`` **SERIAL PRIMARY KEY**,
    ``utilisateur_id`` **INTEGER NOT NULL**,
    ``token`` **VARCHAR(255) NOT NULL UNIQUE**,
    ``date_expiration`` **TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL '30 minutes'),**
    **CONSTRAINT** ``fk_utilisateur_email`` **FOREIGN KEY** (``utilisateur_id``) **REFERENCES utilisateurs**(``id``)

);

  
CREATE TABLE recuperation_mot_de_passe (
    ``id`` **SERIAL PRIMARY KEY**,
    ``utilisateur_id`` **INTEGER NOT NULL**,
    ``token`` **VARCHAR(255) NOT NULL UNIQUE**,
    ``date_expiration`` **TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL '30 minutes'),**
    **CONSTRAINT** ``fk_utilisateur_recuperation`` **FOREIGN KEY** (``utilisateur_id``) **REFERENCES utilisateurs**(``id``)

);

## **3.2.** Relations entre les Tables

1. Relation one-to-many entre `UTILISATEURS` et `DEMANDES` via `id_utilisateur`:
    - Un utilisateur peut avoir une seule demande de créateur.
    - Pertinence : Permet aux utilisateurs de demander le statut de créateur.
    
2. Relation one-to-many entre `CHAPITRES` et `HISTOIRES` via `numchap`:
    - Un chapitre peut contenir plusieurs histoires.
    - Pertinence : Organise les histoires en chapitres.
    
3. Relation one-to-many entre `LIEUX` et `HISTOIRES` via `id_lieu`:
    - Un lieu peut être associé à plusieurs histoires.
    - Pertinence : Permet de situer les histoires dans des lieux spécifiques.
    
4. Relation one-to-many entre `UTILISATEURS` et `HISTOIRES` via `createur`:
    - Un utilisateur (créateur) peut créer plusieurs histoires.
    - Pertinence : Associe les histoires à leurs créateurs.
    
5. Relation one-to-many entre `UTILISATEURS` et `PERSONNAGES` via `createur`:
    - Un utilisateur peut créer plusieurs personnages.
    - Pertinence : Permet aux utilisateurs de créer et gérer leurs propres personnages.
    
6. Relation one-to-many entre `HISTOIRES` et `DIALOGUES` via `id_histoire`:    
    - Une histoire peut avoir plusieurs dialogues.
    - Pertinence : Associe les dialogues à leurs histoires respectives.
    
7. Relation one-to-many entre `PERSONNAGES` et `DIALOGUES` via `interlocuteur`:    
    - Un personnage peut avoir plusieurs dialogues.
    - Pertinence : Lie les dialogues aux personnages qui les prononcent.
    
8. Relation many-to-many entre `HISTOIRES` et `PERSONNAGES` via la table `APPARITIONS`:    
    - Une histoire peut avoir plusieurs personnages et un personnage peut apparaître dans plusieurs histoires.
    - Pertinence : Gère les apparitions des personnages dans différentes histoires.
    
9. Relation one-to-many entre `HISTOIRES` et `QUESTIONS` via `id_histoire`:    
    - Une histoire peut avoir plusieurs questions associées.
    - Pertinence : Permet d'ajouter des questions spécifiques à chaque histoire.
    
10. Relation many-to-many entre `UTILISATEURS` et `HISTOIRES` via la table `PROGRESSION`:    
    - Un utilisateur peut progresser dans plusieurs histoires et une histoire peut être suivie par plusieurs utilisateurs.
    - Pertinence : Suit la progression des utilisateurs dans les différentes histoires.
    
11. Relation one-to-many entre `UTILISATEURS` et `verifications_email` via `utilisateur_id`:    
    - Un utilisateur peut avoir plusieurs tokens de vérification d'email.
    - Pertinence : Gère le processus de vérification d'email pour les utilisateurs.
    
12. Relation one-to-many entre `UTILISATEURS` et `recuperation_mot_de_passe` via `utilisateur_id`:    
    - Un utilisateur peut avoir plusieurs demandes de récupération de mot de passe.
    - Pertinence : Gère le processus de récupération de mot de passe pour les utilisateurs.

## **3.3.** Cas d'utilisations des tables

# Fonctions de classes
## **4.1.** Bases de données
### **4.1.1.** Gestion des utilisateurs
#### **4.1.1.1.** Récupération d'informations
##### **4.1.1.1.1. `getIdWithEmail`**



# Fonctionnalités

## **5.1** Page Login

### **5.1.1.** Connexion Compte
- **Fonctionnalité** : permet aux utilisateurs se connecter à leur compte existant
- **Interface utilisateur** :
   - Champs de saisie pour l'adresse e-mail de l'utilisateur
   - Champs de saisie pour le mot de passe (masqué)
   - Bouton "Se Se connecter"
   - [TODO] Lien "Mot de passe oublié ?
   - Lien "Créer un compte"
- **Gestion erreurs** :
   - Si l'utilisateur se trompe d'e-mail ou de mot de passe
      - Message d'erreur en rouge : "Email ou mot de passe incorrect"
      - Les champs d'email et de mot de passe sont encadrés en rouge
   - **Sécurité** :
      - Hachage de mot de passe côté serveur
      - Protection contre les attaques XSS
- **Redirection** :
   - Si l'utilisateur rentre les bons identifiants :
   - Redirection vers le dashboard

### **5.1.2** Création Compte 
- **Fonctionnalité** : Permet aux nouveaux utilisateurs de créer un compte.
- **Interface utilisateur** :
   - Champs de saisie pour : nom, prénom, date de naissance , nom d'utilisateur, adresse e-mail, mot de passe, confirmation du mot de passe
   - Case à cocher pour accepter les conditions d'utilisation
   - Bouton "Créer un compte"
- **Vérification d'unicité** :
   - Si l'adresse mail est déjà utilisée, message d'erreur : "Cette adresse email est déjà utilisée ! Essayer de vous connecter avec celle-ci" + Encadré en rouge
- **Processus de création** :
   - Envoie d'un email de confirmation avec un lien et un code de vérification. 
   - Compte crée mais inactif jusqu'à la vérification par code de vérification
- **Redirection** :
   - Après la création réussie : Page de connexion .
# Conclusion 
Ce document fournit vue d'ensemble détaillée aspects techniques fonctionnels projet [Rezisten]. Pour toute question suggestion veuillez nous contacter à [Rezisten.contact@gmail.com].
