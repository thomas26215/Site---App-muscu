# README - Développement Web de l'Application de Suivi de Musculation

## Table des matières
- [Introduction](#introduction)
- [Technologies utilisées](#technologies-utilisées)
- [Fonctionnalités](#fonctionnalités)
- [Installation](#installation)
- [Utilisation](#utilisation)
- [Contributions](#contributions)
- [Idées](#idees)
- [Droits d'auteur](#droits-dauteur)
- [Contact](#contact)

## Introduction
Ce projet vise à développer la version web d'une application mobile de suivi de musculation. L'application permet aux utilisateurs de suivre leurs séances d'entraînement, de partager des routines et d'interagir avec d'autres utilisateurs, notamment des enseignants et des élèves.
**[IDEE]** J'ai également en idée la création d'une série d'applications. C'est à dire que je crée une application destinée à la préparations physique des skieurs en été, une autre destinée à la préparation physique pour les sports de combat, une pour la préparation physique au tennis, une pour la préparation physique pour des sports d'athlétisme... Chaque application aurait la même base : cette base de bleu et blanc mais chaque appli aurait sa petite touche personnelle au niveau de la couleur (certains liens survolés pas la souris, l'affichage des statistiques...)
N'hésitez pas à lire le fichier **Documentation technique.md** pour obtenir plus d'informations spécifiques sur le projet

## Technologies utilisées
- **HTML** : Structure du contenu web.
- **CSS** : Style et mise en page.
- **JavaScript** : Interactivité et logique côté client.
- **PHP** : Traitement côté serveur et gestion des bases de données.
- **MySQL** : Système de gestion de base de données pour stocker les informations des utilisateurs et des séances.

## Fonctionnalités
Les utilisateurs peuvent :
- **Créer des programmes de musculation** : Concevoir et personnaliser leurs propres programmes d'entraînement avec différents types d'exercices.
- **Pratiquer plusieurs types de sport** : Inclure des activités telles que la musculation et les sports de combat.
- **Choisir le type d'exercice** : Sélectionner des paramètres spécifiques comme le poids, le temps, les exercices dégressifs, ou alternés (ex. : 1 min de squats suivi de 1 min de gainage, répété 3 fois sans pause).
- **Accéder à un système d'apprentissage** : Intégrer des cours sur des sujets comme le MMA, incluant des explications détaillées et une liste d'exercices pratiques.
- **Suivre ses progrès** : Accéder à un système de suivi basé sur des statistiques, permettant aux utilisateurs d'évaluer leur performance au fil du temps. En fonction des performances réalisées précédemment, l'algorithme pourra conseiller d'augmenter ou de réduire les charges/temps sur certains exercices.
- **Gestion du matériel** : Au début de chaque séance, l'application demandera à l'utilisateur s'il dispose de tout le matériel nécessaire. Si ce n'est pas le cas, elle proposera automatiquement une alternative avec les équipements dont l'utilisateur dispose.

## Installation
Pour installer le projet localement, suivez ces étapes :

1. Clonez le dépôt :
   ```bash
   git clone https://github.com/votre-utilisateur/votre-repo.git
   ```
2. Accédez au répertoire du projet :
   ```bash
   cd votre-repo
   ```
3. Configurez votre serveur local (par exemple, XAMPP ou WAMP) pour exécuter les fichiers PHP.
4. Importez la base de données depuis le fichier `database.sql` dans votre gestionnaire MySQL.

## Utilisation
1. Lancez votre serveur local.
2. Ouvrez votre navigateur et accédez à `http://localhost/votre-repo`.
3. Créez un compte ou connectez-vous pour commencer à utiliser l'application.

## Contributions
Les contributions sont les bienvenues ! Pour contribuer au projet :
1. Forkez le dépôt.
2. Créez une nouvelle branche (`git checkout -b feature/nouvelle-fonctionnalité`).
3. Commitez vos modifications (`git commit -m 'Ajout d'une nouvelle fonctionnalité'`).
4. Poussez la branche (`git push origin feature/nouvelle-fonctionnalité`).
5. Ouvrez une Pull Request.

## Idées
Voici les idées à intégrer
1. Ajoute des exercices évolutifs à intégrer dans la séance. Exemple : Pour faire les handstand pushup, il faut maîtriser le pushup, le pushup surélevé, le handstand ... Jusqu'au pushup. Il faudrait pouvoir intégrer ce type d'exercice évolutif dans une séance de muscu

## Droits d'auteur
Ce projet est protégé par les droits d'auteur. Tous les droits sont réservés à **[Thomas VENOUIL]**. Aucune partie de ce projet ne peut être reproduite, distribuée ou modifiée sans l'autorisation écrite préalable du titulaire des droits.

## Contact
Pour toute question ou suggestion, n'hésitez pas à me contacter :
- **Nom** : Venouil
- **Email** : t.venouil26@gmail.com
- **GitHub** : [thomas26215](https://github.com/thomas26215)

---

Merci d'avoir consulté ce README ! J'espère que vous apprécierez le développement et l'utilisation de cette application web dédiée au suivi de musculation.
