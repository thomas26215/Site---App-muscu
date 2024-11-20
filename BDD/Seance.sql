PRAGMA foreign_keys = ON;

CREATE TABLE seance (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_utilisateur INTEGER NOT NULL,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE seance_groupes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_seance INTEGER NOT NULL,
    nombre_repetitions INTEGER NOT NULL, -- Nombre de fois que ce groupe d'exercices sera répété
    ordre INTEGER NOT NULL, -- Ordre d'apparition du groupe dans la séance
    FOREIGN KEY (id_seance) REFERENCES seance(id)
);

CREATE TABLE seance_exercices (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_groupe INTEGER NOT NULL,
    id_exercice INTEGER NOT NULL,
    ordre_exercice INTEGER NOT NULL, -- Ordre de l'exercice dans le groupe
    FOREIGN KEY (id_groupe) REFERENCES seance_groupes(id),
    FOREIGN KEY (id_exercice) REFERENCES exercises(id)
);

CREATE TABLE exercises (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_utilisateur INTEGER NOT NULL,
    name VARCHAR(255) NOT NULL,
    description VARCHAR(1000) NOT NULL,
    type VARCHAR(50) NOT NULL, -- Type d'exercice (musculation, cardio, etc.)
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE exercices_repetition (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_exercice INTEGER NOT NULL,
    series INTEGER NOT NULL,
    repetitions INTEGER NOT NULL,
    temps_repos_entre_series INTEGER, -- en secondes
    FOREIGN KEY (id_exercice) REFERENCES exercises(id)
);

CREATE TABLE exercices_temps (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_exercice INTEGER NOT NULL,
    series INTEGER NOT NULL,
    duree INTEGER NOT NULL, -- en secondes
    temps_repos_entre_series INTEGER, -- en secondes
    FOREIGN KEY (id_exercice) REFERENCES exercises(id)
);

CREATE TABLE exercices_degression (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_exercice INTEGER NOT NULL,
    temps_repos_entre_series INTEGER, -- en secondes
    FOREIGN KEY (id_exercice) REFERENCES exercises(id)
);

CREATE TABLE details_degression (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_exercice_degression INTEGER NOT NULL,
    numero_serie INTEGER NOT NULL, -- 1, 2, 3, etc.
    poids DECIMAL(5,2) NOT NULL, -- Poids pour cette série (ex : 15.00 pour 15kg)
    FOREIGN KEY (id_exercice_degression) REFERENCES exercices_degression(id)
);

CREATE TABLE exercices_alternes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(255) NOT NULL, -- Un nom pour identifier cette séquence alternée
    nombre_series INTEGER NOT NULL,
    temps_repos_entre_series INTEGER -- en secondes
);

CREATE TABLE details_exercices_alternes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_exercice_alterne INTEGER NOT NULL,
    ordre_serie INTEGER NOT NULL, -- Ordre de la série dans le circuit alterné
    id_exercice INTEGER NOT NULL,
    repetitions INTEGER NOT NULL, -- Nombre de répétitions pour cet exercice dans cette série
    FOREIGN KEY (id_exercice_alterne) REFERENCES exercices_alternes(id),
    FOREIGN KEY (id_exercice) REFERENCES exercises(id)
);