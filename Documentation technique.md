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


- **Table `seance`**
	- `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de la séance.
	- `id_utilisateur` **INTEGER NOT NULL** : Identifiant de l'utilisateur qui a créé la séance.
	- `nom` **VARCHAR(255) NOT NULL** : Nom de la séance.
	- `description` **TEXT** : Description détaillée de la séance.
	- `date_creation` **DATETIME DEFAULT CURRENT_TIMESTAMP** : Date de création de la séance.
- **Table `seance_groupes`**
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique du groupe.
    - `id_seance` **INTEGER NOT NULL** : Identifiant de la séance associée.
    - `nombre_repetitions` **INTEGER NOT NULL** : Nombre de fois que ce groupe d'exercices sera répété.
    - `ordre` **INTEGER NOT NULL** : Ordre d'apparition du groupe dans la séance.
- **Table `seance_exercices`**
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de l'association exercice-groupe.
    - `id_groupe` **INTEGER NOT NULL** : Identifiant du groupe auquel l'exercice appartient.
    - `id_exercice` **INTEGER NOT NULL** : Identifiant de l'exercice.
    - `ordre_exercice` **INTEGER NOT NULL** : Ordre de l'exercice dans le groupe.
- **Table `exercises`**
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de l'exercice.
    - `id_utilisateur` **INTEGER NOT NULL** : Identifiant de l'utilisateur qui a créé l'exercice.
    - `name` **VARCHAR(255) NOT NULL** : Nom de l'exercice.
    - `description` **VARCHAR(1000) NOT NULL** : Description détaillée de l'exercice.
    - `type` **VARCHAR(50) NOT NULL** : Type d'exercice (musculation, cardio, etc.).
    - `created_at` **DATETIME DEFAULT CURRENT_TIMESTAMP** : Date de création de l'exercice.
- **Table `exercices_repetition`**
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de l'enregistrement.
    - `id_exercice` **INTEGER NOT NULL** : Identifiant de l'exercice associé.
    - `series` **INTEGER NOT NULL** : Nombre de séries pour cet exercice.
    - `repetitions` **INTEGER NOT NULL** : Nombre de répétitions par série.
    - `temps_repos_entre_series` **INTEGER** : Temps de repos entre les séries (en secondes).
- **Table `exercices_temps`**
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de l'enregistrement.
    - `id_exercice` **INTEGER NOT NULL** : Identifiant de l'exercice associé.
    - `series` **INTEGER NOT NULL** : Nombre de séries pour cet exercice.
    - `duree` **INTEGER NOT NULL** : Durée d'une série (en secondes).
    - `temps_repos_entre_series` **INTEGER** : Temps de repos entre les séries (en secondes).
- **Table `exercices_degression`**
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique de l'enregistrement.
    - `id_exercice` **INTEGER NOT NULL** : Identifiant de l'exercice associé.
    - `temps_repos_entre_series` **INTEGER** : Temps de repos entre les séries (en secondes).
- **Table `details_degression`**
    - `id` **INTEGER PRIMARY KEY AUTOINCREMENT** : Identifiant unique des détails de dégression.
    - `id_exercice_degression` **INTEGER NOT NULL** : Identifiant de l'exercice avec dégressions associé.
    - `numero_serie` **INTEGER NOT NULL** : Numéro séquentiel pour la série (1, 2, etc.).
    - `poids` **DECIMAL(5,2) NOT NULL:** Poids utilisé pour cette série (ex: '15.00' pour '15kg').
- **Table `exercices_alternes`**
   - `id` ***INTEGER PRIMARY KEY AUTOINCREMENT***: Identifiant unique pour la séquence alternée
   - `nom` ***VARCHAR(255) NOT NULL***: Nom pour identifier cette séquence alternée
   - `nombre_series` ***INTEGER NOT NULL***: Nombre total de séries dans cette séquence
   - `temps_repos_entre_series` ***INTEGER***: Temps de repos entre les séries (en secondes)
- **Table `details_exercices_alternes`**
   - `id` **INTEGER PRIMARY KEY AUTOINCREMENT**: Identifiant unique pour cet enregistrement
   - `id_exercice_alterne` **INTEGER NOT NULL**: Identifiant du circuit alterné auquel cet exercice appartient
   - `ordre_serie` **INTEGER NOT NULL**: Ordre dans lequel cet exercice apparaît dans le circuit alterné
   - `id_exercice` **INTEGER NOT NULL**: Identifiant du véritable exercice
   - `repetitions` **INTEGER NOT NULL**: Nombre total de répétitions pour cet exercice dans cette série


### **3.2.** Relations entre les Tables

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
16. Relation one-to-many entre `utilisateurs` et `seance` via `id_utilisateur` :  
    - Un utilisateur peut créer plusieurs séances.  
    - Pertinence : Permet de gérer les séances associées à chaque utilisateur tout en maintenant un lien direct avec le créateur de la séance.
17. Relation one-to-many entre `seance` et `seance_groupes` via `id_seance` :  
    - Une séance peut contenir plusieurs groupes d'exercices.  
    - Pertinence : Structure les groupes d'exercices comme des sous-composantes d'une séance, permettant une organisation claire et une gestion flexible.
18. Relation one-to-many entre `seance_groupes` et `seance_exercices` via `id_groupe` :  
    - Un groupe peut inclure plusieurs exercices.  
    - Pertinence : Permet de lier des exercices à des groupes spécifiques tout en respectant leur ordre dans la structure de la séance.
19. Relation one-to-many entre `exercises` et `seance_exercices` via `id_exercice` :  
    - Un exercice peut être utilisé dans plusieurs groupes ou séances.  
    - Pertinence : Assure que chaque exercice inclus dans une séance est validé et correctement référencé.
20. Relation one-to-one entre `exercises` et `exercices_repetition` via `id_exercice` :  
    - Chaque exercice peut avoir des détails spécifiques pour les répétitions (séries, nombre de répétitions, temps de repos).  
    - Pertinence : Gère les paramètres détaillés pour les exercices basés sur des répétitions.
21. Relation one-to-one entre `exercises` et `exercices_temps` via `id_exercice` :  
    - Chaque exercice peut avoir des détails spécifiques pour les séances basées sur le temps (durée, séries, temps de repos).  
    - Pertinence : Supporte les exercices où la durée est le facteur principal.
22. Relation one-to-one entre `exercises` et `exercices_degression` via `id_exercice` :  
    - Chaque exercice peut inclure une dégression (repos progressif, poids décroissant, etc.).  
    - Pertinence : Permet de gérer les exercices avec une approche évolutive ou dégressive.
23. Relation one-to-many entre `exercices_degression` et `details_degression` via `id_exercice_degression` :  
    - Une dégression peut avoir plusieurs détails par série (numéro de série, poids utilisé, etc.).  
    - Pertinence : Offre un suivi précis des paramètres pour chaque série d'une dégression.
24. Relation one-to-many entre `exercices_alternes` et `details_exercices_alternes` via `id_exercice_alterne` :  
    - Une séquence alternée peut inclure plusieurs séries d'exercices.  
    - Pertinence : Structure et organise les exercices alternés dans un format répétable et clair.
25. Relation one-to-many entre `exercises` et `details_exercices_alternes` via `id_exercice` :  
    - Un exercice peut être inclus dans plusieurs séquences alternées.  
    - Pertinence : Permet de réutiliser les exercices existants dans des séquences variées, garantissant leur cohérence dans les différents types d'entraînement.  

---

Si vous avez d'autres ajustements ou points spécifiques, je suis à votre disposition. 😊

### **3.3.** Cas d'utilisations des tables

#### Ajouter une séance de différents exrcices complexes

- **Objectif** : Ajouter 3 exercices :
   1. Un exercices de types répétitions : Chaque exercice fait 5 répétitions de 10 kilos avec débord 30s de récup puis 1min de récup et une récup de 2mins
   2. Un exercice de type dégression : D'abord 10kg puis 5 kg puis 3kg avec 10 secondes entre chaque exercice
   3. Deux exercice alternatifs : On enchaîne développé couché et curl marteau avec une minutes de chaque sans pause le tout 3 fois avec une minute par exo
 
  - **Comment faire** :
     - Créer une séance pour le premier utilisateur : `INSERT INTO seance (id_utilisateur, descripition) values (5, 'Description de la séance
  - **Ajouter le premier exo** [TODO]

#### **3.3.3.**

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

### **4.1.2** Création Compte 
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

### **4.3** Gestion Exercices 
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
