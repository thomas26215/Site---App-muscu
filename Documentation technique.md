# Documentation Technique

**Documentation Technique du Projet [Nom à définir]**

---

## Table des Matières
1. [Introduction](#introduction)
2. [Aperçu du Projet](#aperçu-du-projet)
3. [Bases de Données](#bases-de-données)
   - 3.1. [Structure des Tables SQL](#31-structure-des-tables-sql)
      - 3.1.1. [Fichier utilisateur.db](#311-fichier-utilisateurdb)
      - 3.1.2. [Fichier exercises.db](#312fichierexercisesdb)
      - 3.1.3. [Fichier seances.db](#313fichier-seancesdb)
   - 3.2. [Relations entre les Tables](#32-relations-entre-les-tables)
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

### 3.1 Structure des tables SQL

#### 3.1.1. **Fichier `utilisateur.db**

Voici la version mise à jour selon vos instructions :

- **TABLE `utilisateurs`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de l'utilisateur.
    - `pseudo` **VARCHAR(50) NOT NULL** : Le pseudo de la personne.
    - `email` **VARCHAR(100) NOT NULL UNIQUE** : L'email de la personne. Deux personnes ne peuvent pas avoir le même email.
    - `mot_de_passe` **VARCHAR(255) NOT NULL** : Mot de passe hashé.
    - `date_inscription` **DATETIME DEFAULT CURRENT_TIMESTAMP** : La date d'inscription de la personne, automatiquement la date d'aujourd'hui.
    - `derniere_connexion` **DATETIME** : La date de la dernière connexion de l'utilisateur.

- **TABLE `profils`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique du profil.
    - `utilisateur_id` **INTEGER NOT NULL UNIQUE** : Identifiant de l'utilisateur associé.
    - `nom` **VARCHAR(50) NOT NULL** : Nom de l'utilisateur.
    - `prenom` **VARCHAR(50) NOT NULL** : Prénom de l'utilisateur.
    - `date_naissance` **DATE** : Date de naissance de l'utilisateur.
    - `sexe` **VARCHAR(10)** : Sexe de l'utilisateur.
    - `biographie` **TEXT** : Biographie de l'utilisateur.

- **TABLE `roles`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique du rôle.
    - `nom` **VARCHAR(20) NOT NULL UNIQUE** : Nom du rôle.
    - `description` **TEXT** : Description du rôle.

- **TABLE `utilisateurs_roles`** :
    - `utilisateur_id` **INTEGER NOT NULL** : Identifiant de l'utilisateur.
    - `role_id` **INTEGER NOT NULL** : Identifiant du rôle.
    - Clé primaire composite sur (utilisateur_id, role_id).

- **TABLE `preferences`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de la préférence.
    - `utilisateur_id` **INTEGER NOT NULL UNIQUE** : Identifiant de l'utilisateur associé.
    - `theme` **VARCHAR(20)** : Thème préféré de l'utilisateur.
    - `notifications_email` **BOOLEAN DEFAULT TRUE** : Préférence pour les notifications par email.
    - `langue` **VARCHAR(10)** : Langue préférée de l'utilisateur.

- **TABLE `recuperation_mot_de_passe`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de la demande de récupération.
    - `utilisateur_id` **INTEGER NOT NULL** : Identifiant de l'utilisateur associé.
    - `token` **VARCHAR(255) NOT NULL UNIQUE** : Token de récupération.
    - `date_expiration` **DATETIME NOT NULL** : Date d'expiration du token.

- **TABLE `verifications_email`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de la vérification.
    - `utilisateur_id` **INTEGER NOT NULL** : Identifiant de l'utilisateur associé.
    - `token` **VARCHAR(255) NOT NULL UNIQUE** : Token de vérification.
    - `date_expiration` **DATETIME NOT NULL DEFAULT (datetime('now', '+30 minutes'))** : Date d'expiration du token.


#### 3.1.2. **Fichier `exercises.db`**

- **TABLE `exercises`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de l'exercice.
    - `id_utilisateur` **INTEGER NOT NULL** : Identifiant de l'utilisateur qui a créé l'exercice.
    - `name` **VARCHAR(255) NOT NULL** : Nom de l'exercice.
    - `description` **VARCHAR(1000) NOT NULL** : Description détaillée de l'exercice.
    - `type` **VARCHAR(50) NOT NULL** : Type d'exercice.
    - `created_at` **DATETIME DEFAULT CURRENT_TIMESTAMP** : Date de création de l'exercice.

- **TABLE `visibility_exercises`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de la visibilité.
    - `id_exercise` **INTEGER NOT NULL UNIQUE** : Identifiant de l'exercice associé.
    - `visibility` **BOOLEAN NOT NULL DEFAULT FALSE** : Visibilité de l'exercice.

- **TABLE `tags`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique du tag.
    - `tag` **VARCHAR(255) NOT NULL UNIQUE** : Nom du tag.

- **TABLE `exercise_tags`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de l'association.
    - `id_exercise` **INTEGER NOT NULL** : Identifiant de l'exercice.
    - `id_tag` **INTEGER NOT NULL** : Identifiant du tag.
    - Contrainte d'unicité sur **`(id_exercise, id_tag)`**.

- **TABLE `exercise_community`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de l'interaction communautaire.
    - `id_exercise` **INTEGER NOT NULL UNIQUE** : Identifiant de l'exercice associé.
    - `likes` **INTEGER NOT NULL DEFAULT 0** : Nombre de likes pour l'exercice.

- **TABLE `comments`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique du commentaire.
    - `id_exercise` **INTEGER NOT NULL** : Identifiant de l'exercice commenté.
    - `id_user` **INTEGER NOT NULL** : Identifiant de l'utilisateur qui a commenté.
    - `comment` **TEXT NOT NULL** : Contenu du commentaire.
    - `created_at` **DATETIME DEFAULT CURRENT_TIMESTAMP** : Date de création du commentaire.

- **TABLE `ratings`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de l'évaluation.
    - `id_exercise` **INTEGER NOT NULL** : Identifiant de l'exercice évalué.
    - `id_user` **INTEGER NOT NULL** : Identifiant de l'utilisateur qui a évalué.
    - `rating` **INTEGER CHECK(rating >= 1 AND rating <= 5)** : Note attribuée (de 1 à 5).
    - `created_at` **DATETIME DEFAULT CURRENT_TIMESTAMP** : Date de l'évaluation.

- **TABLE `notifications`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de la notification.
    - `user_id` **INTEGER NOT NULL** : Identifiant de l'utilisateur concerné.
    - `message` **TEXT NOT NULL** : Contenu de la notification.
    - `is_read` **BOOLEAN DEFAULT FALSE** : Statut de lecture de la notification.
    - `created_at` **DATETIME DEFAULT CURRENT_TIMESTAMP** : Date de création de la notification.

- **TABLE `exercise_stats`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique des statistiques.
    - `id_exercise` **INTEGER NOT NULL UNIQUE** : Identifiant de l'exercice associé.
    - `views` **INTEGER DEFAULT 0** : Nombre de vues de l'exercice.
    - `interactions` **INTEGER DEFAULT 0** : Nombre d'interactions avec l'exercice.
    - `last_interaction` **DATETIME DEFAULT CURRENT_TIMESTAMP** : Date de la dernière interaction.

#### **3.1.3** Fichier seances.db


- **TABLE `seance`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de la séance.
    - `id_utilisateur` **INTEGER NOT NULL** : Identifiant de l'utilisateur associé à la séance.
    - `description` **TEXT** : Description de la séance.

- **TABLE `equipment`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de l'équipement.
    - `name` **VARCHAR(255) NOT NULL UNIQUE** : Nom de l'équipement.
    - `description` **TEXT** : Description de l'équipement.

- **TABLE `exercise_types`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique du type d'exercice.
    - `type_name` **VARCHAR(255) NOT NULL UNIQUE** : Nom du type d'exercice.

- **TABLE `exercises`** :
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de l'exercice.
    - `id_utilisateur` **INTEGER NOT NULL** : Identifiant de l'utilisateur qui a créé l'exercice.
    - `name` **VARCHAR(255) NOT NULL** : Nom de l'exercice.
    - `description` **VARCHAR(1000) NOT NULL** : Description détaillée de l'exercice.
    - `type` **VARCHAR(50) NOT NULL** : Type d'exercice (musculation, cardio, etc.).
    - `equipment_id` **INTEGER** : Identifiant de l'équipement nécessaire.
    - `combat_sport` **VARCHAR(50)** : Sport de combat si applicable.
    - `created_at` **DATETIME DEFAULT CURRENT_TIMESTAMP** : Date de création de l'exercice.

### **3.2** Relations entre les Tables

1. Relation one-to-one entre `utilisateurs` et `profils` via `utilisateur_id`:
    - Chaque utilisateur a un seul profil et chaque profil appartient à un seul utilisateur.
    - Pertinence : Permet de stocker des informations détaillées sur l'utilisateur séparément de ses informations de connexion, améliorant ainsi la structure et la sécurité des données.
2. Relation many-to-many entre `utilisateurs` et `roles` via la table de liaison `utilisateurs_roles`:
    - Un utilisateur peut avoir plusieurs rôles et un rôle peut être attribué à plusieurs utilisateurs.
    - Pertinence : Offre une gestion flexible des permissions et des accès dans l'application.
3. Relation one-to-one entre `utilisateurs` et `preferences` via `utilisateur_id`:
    - Chaque utilisateur a un seul ensemble de préférences.
    - Pertinence : Permet une personnalisation de l'expérience utilisateur sans surcharger la table principale des utilisateurs.
4. Relation one-to-many entre `utilisateurs` et `recuperation_mot_de_passe` via `utilisateur_id`:
    - Un utilisateur peut avoir plusieurs demandes de récupération de mot de passe au fil du temps.
    - Pertinence : Assure la sécurité en permettant la gestion des demandes de réinitialisation de mot de passe.
5. Relation one-to-many entre `utilisateurs` et `verifications_email` via `utilisateur_id`:
    - Un utilisateur peut avoir plusieurs tokens de vérification d'email (par exemple, en cas de changement d'email).
    - Pertinence : Facilite le processus de vérification d'email et améliore la sécurité du compte.
6. Relation one-to-many entre `utilisateurs` et `exercises` via `id_utilisateur`:
    - Un utilisateur peut créer plusieurs exercices.
    - Pertinence : Permet aux utilisateurs de contribuer au contenu de l'application en créant leurs propres exercices.    
7. Relation one-to-one entre `exercises` et `visibility_exercises` via `id_exercise`:
    - Chaque exercice a un seul paramètre de visibilité.
    - Pertinence : Offre un contrôle granulaire sur la visibilité de chaque exercice, permettant aux utilisateurs de décider s'ils veulent partager leurs créations.
8. Relation many-to-many entre `exercises` et `tags` via la table de liaison `exercise_tags`:
    - Un exercice peut avoir plusieurs tags et un tag peut être associé à plusieurs exercices.
    - Pertinence : Facilite la catégorisation et la recherche des exercices.    
9. Relation one-to-one entre `exercises` et `exercise_community` via `id_exercise`:
    - Chaque exercice a une seule entrée dans la table des interactions communautaires.
    - Pertinence : Permet de suivre l'engagement de la communauté pour chaque exercice.
10. Relation one-to-many entre `exercises` et `comments` via `id_exercise`:
    - Un exercice peut avoir plusieurs commentaires.
    - Pertinence : Favorise l'interaction entre les utilisateurs et le partage de retours sur les exercices.
11. Relation one-to-many entre `exercises` et `ratings` via `id_exercise`:
    - Un exercice peut avoir plusieurs évaluations.
    - Pertinence : Permet aux utilisateurs de noter les exercices, fournissant ainsi un indicateur de qualité.
12. Relation one-to-many entre `utilisateurs` et `notifications` via `user_id`:
    - Un utilisateur peut recevoir plusieurs notifications.
    - Pertinence : Facilite la communication avec les utilisateurs et les tient informés des activités pertinentes.
13. Relation one-to-one entre `exercises` et `exercise_stats` via `id_exercise`:
    - Chaque exercice a une seule entrée de statistiques.
    - Pertinence : Permet de suivre l'utilisation et la popularité de chaque exercice.
14. Relation one-to-many entre `utilisateurs` et `seance` via `id_utilisateur`:
    - Un utilisateur peut créer plusieurs séances d'entraînement.
    - Pertinence : Permet aux utilisateurs de planifier et d'organiser leurs entraînements.
15. Relation one-to-many entre `equipment` et `exercises` via `equipment_id`:
    - Un équipement peut être utilisé dans plusieurs exercices.
    - Pertinence : Facilite la recherche d'exercices basés sur l'équipement disponible pour l'utilisateur. 

## Fonctionnalités

### **4.1** Page Login

#### **4.1.1.** Connexion Compte

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
   - [TODO] Après 3 tentatives échouées
      - Message : "Trop de tentatives. Il faut réessayer dans 5 minutes"
      - Bouton de connexion désactivé pendant 5 minutes
   - **Sécurité** :
      - [TODO] Utilisation de HTTPS pour la transmission de données
      - Hachage de mot de passe côté serveur
      - Protection contre les attaques XSS
      - [TODO]Protection contres les attaques par force brute 
- **Redirection** :
   - Si l'utilisatteur rentre les bons identifiants :
      - **Dans le cas où il a déjà rentré son code de vérification concernant la création de compte** => Redirection vers le dashboard
      - **Dans le cas où il n'a pas déjà rentré son code de vérificaiton concernant la création de compte** => Redirection vers la vérification du compte

### ***4.1.2*** Création Compte 
- **Fonctionnalité** : Permet aux nouveaux utilisateurs de créer un compte.
- **Interface utilisateur** :
   - Champs de saisie pour : nom d'utilisateur, adresse e-mail, mot de passe, confirmation du mot de passe
   - [TODO] Case à cocher pour accepter les conditions d'utilisation
   - Bouton "Créer un compte"
- **Vérification d'unicité** :
   - Si l'adresse mail est déjà utilisée, message d'erreur : "Cette adresse email est déjà utilisée ! Essayer de vous connecter avec celle-ci" + Encadré en rouge
- **Processus de création** :
   - Envoie d'un email de confirmation avec un lien et un code de vérification. Si ce code n'est pas rentré dans les 30 minutes, le compte de crée par l'utilisateur est automatiquement supprimé
   - Compte crée mais inactif jusqu'à la vérification par code de vérification
- **Redirection** :
   - Après la création réussie : Page de confirmations avec les instructions pour vérifier l'email

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
