PRAGMA foreign_keys = ON;


-- Table utilisateurs
CREATE TABLE utilisateurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    pseudo VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion DATETIME
);

-- Table profils
CREATE TABLE profils (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    utilisateur_id INTEGER NOT NULL UNIQUE,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    date_naissance DATE,
    sexe VARCHAR(10),
    biographie TEXT,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- Table roles
CREATE TABLE roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(20) NOT NULL UNIQUE,
    description TEXT
);
    
-- Table utilisateurs_roles (table de liaison)
CREATE TABLE utilisateurs_roles (
    utilisateur_id INTEGER NOT NULL,
    role_id INTEGER NOT NULL,
    PRIMARY KEY (utilisateur_id, role_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Table preferences
CREATE TABLE preferences (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    utilisateur_id INTEGER NOT NULL UNIQUE,
    theme VARCHAR(20),
    notifications_email BOOLEAN DEFAULT TRUE,
    langue VARCHAR(10),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);



-- Table recuperation_mot_de_passe
CREATE TABLE recuperation_mot_de_passe (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    utilisateur_id INTEGER NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    date_expiration DATETIME NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- Table verifications_email
CREATE TABLE verifications_email (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    utilisateur_id INTEGER NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    date_expiration DATETIME NOT NULL DEFAULT (datetime('now', '+30 minutes')),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);