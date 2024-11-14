PRAGMA foreign_keys = ON;

CREATE TABLE seance(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_utilisateur INTEGER NOT NULL,
    description 
)

/*

PRAGMA foreign_keys = ON;

-- Table pour gérer les utilisateurs
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table pour stocker les équipements
CREATE TABLE equipment (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT
);

-- Table pour stocker les types d'exercices
CREATE TABLE exercise_types (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_name VARCHAR(255) NOT NULL UNIQUE
);

-- Table pour stocker les exercices
CREATE TABLE exercises (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_utilisateur INTEGER NOT NULL,
    name VARCHAR(255) NOT NULL,
    description VARCHAR(1000) NOT NULL,
    type VARCHAR(50) NOT NULL, -- Type d'exercice (musculation, cardio, etc.)
    equipment_id INTEGER, -- Équipement nécessaire
    combat_sport VARCHAR(50), -- Sport de combat si applicable
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE SET NULL
);

-- Table pour stocker les séances
CREATE TABLE seance (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_utilisateur INTEGER NOT NULL,
    description TEXT NOT NULL,
    pause_between_exercises INTEGER DEFAULT 0, -- Pause entre les exercices en secondes
    recovery_time INTEGER DEFAULT 0, -- Temps de récupération en secondes
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES users(id) ON DELETE CASCADE
);

-- Table pour associer les séances aux exercices
CREATE TABLE seance_exercices (
    id_seance INTEGER NOT NULL,
    id_exercice INTEGER NOT NULL,
    repetitions INTEGER DEFAULT NULL, -- Nombre de répétitions si applicable
    sets INTEGER DEFAULT NULL, -- Nombre de séries si applicable
    time_work INTEGER DEFAULT NULL, -- Temps de travail en secondes si applicable
    time_rest INTEGER DEFAULT NULL, -- Temps de repos en secondes si applicable
    alternated BOOLEAN DEFAULT false, -- Si l'exercice est alterné ou non
    FOREIGN KEY (id_seance) REFERENCES seance(id) ON DELETE CASCADE,
    FOREIGN KEY (id_exercice) REFERENCES exercises(id) ON DELETE CASCADE,
    PRIMARY KEY (id_seance, id_exercice)
);

-- Table pour gérer les exercices alternatifs
CREATE TABLE alternative_exercises (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_exercise INTEGER NOT NULL,
    alternative_id INTEGER NOT NULL,
    FOREIGN KEY (id_exercise) REFERENCES exercises(id) ON DELETE CASCADE,
    FOREIGN KEY (alternative_id) REFERENCES exercises(id) ON DELETE CASCADE,
    UNIQUE (id_exercise, alternative_id)
);

*/