# Documentation Technique

**Documentation Technique du Projet [Nom à définir]**

---

## Table des Matières
1. [Introduction](#introduction)
2. [Aperçu du Projet](#aperçu-du-projet)
3. [Bases de Données](#bases-de-données)
   - 3.1. [Structure des Tables SQL](#structure-des-tables-sql)
   - 3.2. [Relations entre les Tables](#relations-entre-les-tables)
4. [Fonctionnalités](#fonctionnalités)
   - 4.1. [Page de Connexion](#page-de-connexion)
   - 4.2. [Création de Compte](#création-de-compte)
   - 4.3. [Gestion des Exercices](#gestion-des-exercices)
5. [Scénarios d'Utilisation](#scénarios-dutilisation)
6. [Conclusion](#conclusion)

---

## Introduction
Ce document présente les spécifications techniques et fonctionnelles du projet [Nom à définir]. Il décrit la structure des bases de données, les principales fonctionnalités et le déroulement des interactions utilisateur.

## Aperçu du Projet
Description à fournir

## Bases de Données

### 3.1. Structure des Tables SQL

#### 3.1.1. **Fichier `utilisateurs.db`**
- **TABLE `utilisateurs`** :
  - `id` **(INTEGER, clé primaire, auto-increment)** : Identifiant unique de l'utilisateur.
  - `pseudo` **(VARCHAR(50), NOT NULL)** : Le pseudo de la personne.
  - `email` **(VARCHAR(100), NOT NULL, UNIQUE)** : L'email de la personne. Deux personnes ne peuvent pas avoir le même email.
  - `mot_de_passe` **(VARCHAR(255), NOT NULL)** : Mot de passe hashé.
  - `date_inscription` **(DATETIME, DEFAULT CURRENT_TIMESTAMP)** : La date d'inscription de la personne, automatiquement la date d'aujourd'hui.
  - `derniere_connexion` **(DATETIME)** : La date de la dernière connexion de l'utilisateur.

- **TABLE `profils`** :
  - `id` **(INTEGER, clé primaire, auto-increment)** : Identifiant unique du profil.
  - `utilisateur_id` **(INTEGER, NOT NULL, UNIQUE)** : Identifiant de l'utilisateur associé.
  - `nom` **(VARCHAR(50), NOT NULL)** : Nom de l'utilisateur.
  - `prenom` **(VARCHAR(50), NOT NULL)** : Prénom de l'utilisateur.
  - `date_naissance` **(DATE)** : Date de naissance de l'utilisateur.
  - `sexe` **(VARCHAR(10))** : Sexe de l'utilisateur.
  - `biographie` **(TEXT)** : Biographie de l'utilisateur.

- **TABLE `roles`** :
  - `id` **(INTEGER, clé primaire, auto-increment)** : Identifiant unique du rôle.
  - `nom` **(VARCHAR(20), NOT NULL, UNIQUE)** : Nom du rôle.
  - `description` **(TEXT)** : Description du rôle.

- **TABLE `utilisateurs_roles`** :
  - `utilisateur_id` **(INTEGER, NOT NULL)** : Identifiant de l'utilisateur.
  - `role_id` **(INTEGER, NOT NULL)** : Identifiant du rôle.
  - Clé primaire composite sur (utilisateur_id, role_id).

- **TABLE `preferences`** :
  - `id` **(INTEGER, clé primaire, auto-increment)** : Identifiant unique de la préférence.
  - `utilisateur_id` **(INTEGER, NOT NULL, UNIQUE)** : Identifiant de l'utilisateur associé.
  - `theme` **(VARCHAR(20))** : Thème préféré de l'utilisateur.
  - `notifications_email` **(BOOLEAN, DEFAULT TRUE)** : Préférence pour les notifications par email.
  - `langue` **(VARCHAR(10))** : Langue préférée de l'utilisateur.

- **TABLE `recuperation_mot_de_passe`** :
  - `id` **(INTEGER, clé primaire, auto-increment)** : Identifiant unique de la demande de récupération.
  - `utilisateur_id` **(INTEGER, NOT NULL)** : Identifiant de l'utilisateur associé.
  - `token` **(VARCHAR(255), NOT NULL, UNIQUE)** : Token de récupération.
  - `date_expiration` **(DATETIME, NOT NULL)** : Date d'expiration du token.

- **TABLE `verifications_email`** :
  - `id` **(INTEGER, clé primaire, auto-increment)** : Identifiant unique de la vérification.
  - `utilisateur_id` **(INTEGER, NOT NULL)** : Identifiant de l'utilisateur associé.
  - `token` **(VARCHAR(255), NOT NULL, UNIQUE)** : Token de vérification.
  - `date_expiration` **(DATETIME, NOT NULL, DEFAULT (datetime('now', '+30 minutes')))** : Date d'expiration du token.

#### 3.1.2. **Fichier `exercises.db`**
- **TABLE `exercises`** :
  - `id` **(INTEGER, clé primaire, auto-increment)** : Identifiant unique de l'exercice.
  - `id_utilisateur` **(INTEGER, NOT NULL)** : Identifiant de l'utilisateur qui a créé l'exercice.
  - `name` **(VARCHAR(255), NOT NULL)** : Nom de l'exercice.
  - `description` **(VARCHAR(1000), NOT NULL)** : Description détaillée de l'exercice.
  - `type` **(VARCHAR(50), NOT NULL)** : Type d'exercice.
  - `created_at` **(DATETIME, DEFAULT CURRENT_TIMESTAMP)** : Date de création de l'exercice.

- **TABLE `visibility_exercises`** :
  - `id` **(INTEGER, clé primaire, auto-increment)** : Identifiant unique de la visibilité.
  - `id_exercise` **(INTEGER, NOT NULL, UNIQUE)** : Identifiant de l'exercice associé.
  - `visibility` **(BOOLEAN, NOT NULL, DEFAULT false)** : Visibilité de l'exercice.

- **TABLE `tags`** :
  - `id` **(INTEGER, clé primaire, auto-increment)** : Identifiant unique du tag.
  - `tag` **(VARCHAR(255), NOT NULL, UNIQUE)** : Nom du tag.

- **TABLE `exercise_tags`** :
  - `id` **(INTEGER, clé primaire, auto-increment)** : Identifiant unique de l'association.
  - `id_exercise` **(INTEGER, NOT NULL)** : Identifiant de l'exercice.
  - `id_tag` **(INTEGER, NOT NULL)** : Identifiant du tag.
  - Contrainte d'unicité sur (id_exercise, id_tag).

- **TABLE `exercise_community`** :
  - `id` **(INTEGER, clé primaire, auto-increment)** : Identifiant unique de l'interaction communautaire.
  - `id_exercise` **(INTEGER, NOT NULL, UNIQUE)** : Identifiant de l'exercice associé.
  - `likes` **(INTEGER, NOT NULL, DEFAULT 0)** : Nombre de likes pour l'exercice.

- **TABLE `comments`** :
  - `id` **(INTEGER, clé primaire, auto-increment)** : Identifiant unique du commentaire.
  - `id_exercise` **(INTEGER, NOT NULL)** : Identifiant de l'exercice commenté.
  - `id_user` **(INTEGER, NOT NULL)** : Identifiant de l'utilisateur qui a commenté.
  - `comment` **(TEXT, NOT NULL)** : Contenu du commentaire.
  - `created_at` **(DATETIME, DEFAULT CURRENT_TIMESTAMP)** : Date de création du commentaire.

- **TABLE `ratings`** :
  - `id` **(INTEGER, clé primaire, auto-increment)** : Identifiant unique de l'évaluation.
  - `id_exercise` **(INTEGER, NOT NULL)** : Identifiant de l'exercice évalué.
  - `id_user` **(INTEGER, NOT NULL)** : Identifiant de l'utilisateur qui a évalué.
  - `rating` **(INTEGER CHECK(rating >=1 AND rating <=5))**: Note attribuée (de 1 à 5).
  - created_at **DATETIME DEFAULT CURRENT_TIMESTAMP**: Date de l'évaluation.

- **TABLE notifications**:
  - id **INTEGER PRIMARY KEY AUTOINCREMENT**: Identifiant unique de la notification
  - user_id **INTEGER NON NUL**, identifiant utilisateur concerné
  - message **TEXT NON NUL**, contenu notification
  - is_read **BOOLEAN DEFAULT FALSE**, statut lecture notification
  - created_at **DATETIME DEFAULT CURRENT_TIMESTAMP**, date création notification

- **TABLE exercise_stats**
  - id INTEGER PRIMARY KEY AUTOINCREMENT: identifiant unique des statistiques
  - id_exercise INTEGER NON NUL UNIQUE: identifiant exercice associé
  - views INTEGER DEFAULT ZERO: nombre vues exercice
  - interactions INTEGER DEFAULT ZERO: nombre interactions exercice
  - last_interaction DATETIME DEFAULT CURRENT_TIMESTAMP: date dernière interaction

#### ***3.1.3*** Fichier seances.db
* TABLE seance:
* id INTEGER PRIMARY KEY AUTOINCREMENT: identifiant unique séance
* id_utilisateur INTEGER NON NUL: identifiant utilisateur associé séance
* description (TEXT): description séance

* TABLE equipment:
* id INTEGER PRIMARY KEY AUTOINCREMENT: identifiant unique équipement
* name VARCHAR (255) NON NUL UNIQUE: nom équipement
* description (TEXT): description équipement

* TABLE exercise_types:
* id INTEGER PRIMARY KEY AUTOINCREMENT: identifiant unique type exercice
* type_name VARCHAR (255) NON NUL UNIQUE: nom type exercice

* TABLE exercises:
* id INTEGER PRIMARY KEY AUTOINCREMENT: identifiant unique exercice
* id_utilisateur INTEGER NON NUL: identifiant utilisateur qui a créé exercice
* name VARCHAR (255) NON NUL: nom exercice
* description VARCHAR (1000) NON NUL: description détaillée exercice
* type VARCHAR (50) NON NUL: type exercice (musculation cardio etc.)
* equipment_id INTEGER: identifiant équipement nécessaire
* combat_sport VARCHAR (50): sport combat si applicable
* created_at DATETIME DEFAULT CURRENT_TIMESTAMP: date création exercice

### ***3.2*** Relations entre les Tables

* Relation one-to-one entre utilisateurs et profils via utilisateur_id
* Relation many-to-many entre utilisateurs et rôles via table liaison utilisateurs_roles
* Relation one-to-one entre utilisateurs et préférences via utilisateur_id
* Relation one-to-many entre utilisateurs et récupération_mot_de_passe via utilisateur_id
* Relation one-to-many entre utilisateurs et vérifications_email via utilisateur_id
* Relation one-to-many entre utilisateurs et exercices via id_utilisateur
* Relation one-to-one entre exercices et visibility_exercises via id_exercise
* Relation many-to-many entre exercices et tags via table liaison exercise_tags
* Relation one-to-one entre exercices et exercise_community via id_exercise
* Relation one-to-many entre exercices et commentaires via id_exercise 
* Relation one-to-many entre exercices et évaluations via id_exercise 
* Relation one-to-many entre utilisateurs et notifications via user_id 
* Relation one-to-one entre exercices et exercise_stats via id_exercise 
* Relation one-to-many entre utilisateurs et séances via id_utilisateur 
* Relation one-to-many entre équipement et exercices via equipment_id 

## Fonctionnalités

### ***4.1*** Page Connexion 
***Fonctionnalité***: permet aux utilisateurs se connecter à leur compte 
***Gestion erreurs***: affiche message erreur si nom utilisateur ou mot passe incorrect 
***Redirection***: redirection page d'accueil après connexion réussie 

### ***4.2*** Création Compte 
***Fonctionnalité***: permet nouveaux utilisateurs créer compte 
***Vérification unicité***: vérifie adresse email déjà utilisée affiche message erreur si c'est le cas 
***Redirection***: redirection page connexion après création réussie 

### ***4.3*** Gestion Exercices 
***Fonctionnalité***: permet utilisateurs enregistrer suivre exercices 
***Suivi performances***: enregistre résultats exercices analyse ultérieure 
***Recommandations***: propose ajustements basés performances précédentes 

## Scénarios Utilisation 

***Connexion utilisateur***
1.L'utilisateur saisit son nom d'utilisateur mot passe 
2.Si informations correctes il est redirigé vers page d'accueil 

***Création compte***
1.L'utilisateur remplit formulaire avec nom d'utilisateur email mot passe 
2.Si email existe déjà message erreur s'affiche sinon il est redirigé vers page connexion 

## Conclusion 
Ce document fournit vue d'ensemble détaillée aspects techniques fonctionnels projet [Nom Projet]. Pour toute question suggestion veuillez contacter [Votre Nom] à [Votre Email].
