PRAGMA foreign_keys = ON;

-- Table pour stocker les exercices
CREATE TABLE exercises (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_utilisateur INTEGER NOT NULL,
    name VARCHAR(255) NOT NULL,
    description VARCHAR(1000) NOT NULL,
    type VARCHAR(50) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES users(id) ON DELETE CASCADE
);

-- Table pour gérer la visibilité des exercices
CREATE TABLE visibility_exercises (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_exercise INTEGER NOT NULL UNIQUE,
    visibility BOOLEAN NOT NULL DEFAULT false,
    FOREIGN KEY (id_exercise) REFERENCES exercises(id) ON DELETE CASCADE
);

-- Table pour gérer les tags des exercices
CREATE TABLE tags (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tag VARCHAR(255) NOT NULL UNIQUE
);

-- Table pour associer les exercices aux tags
CREATE TABLE exercise_tags (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_exercise INTEGER NOT NULL,
    id_tag INTEGER NOT NULL,
    FOREIGN KEY (id_exercise) REFERENCES exercises(id) ON DELETE CASCADE,
    FOREIGN KEY (id_tag) REFERENCES tags(id) ON DELETE CASCADE,
    UNIQUE (id_exercise, id_tag)
);

-- Table pour gérer les likes et interactions communautaires sur les exercices
CREATE TABLE exercise_community (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_exercise INTEGER NOT NULL UNIQUE,
    likes INTEGER NOT NULL DEFAULT 0,
    FOREIGN KEY (id_exercise) REFERENCES exercises(id) ON DELETE CASCADE
);

-- Table pour stocker les commentaires sur les exercices
CREATE TABLE comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_exercise INTEGER NOT NULL,
    id_user INTEGER NOT NULL,
    comment TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_exercise) REFERENCES exercises(id) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

-- Table pour stocker les évaluations des exercices
CREATE TABLE ratings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_exercise INTEGER NOT NULL,
    id_user INTEGER NOT NULL,
    rating INTEGER CHECK(rating >= 1 AND rating <= 5),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_exercise) REFERENCES exercises(id) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

-- Table pour gérer les notifications des utilisateurs
CREATE TABLE notifications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT false,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table pour stocker des statistiques sur l'utilisation des exercices
CREATE TABLE exercise_stats (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_exercise INTEGER NOT NULL UNIQUE,
    views INTEGER DEFAULT 0, -- Nombre de vues de l'exercice
    interactions INTEGER DEFAULT 0, -- Nombre d'interactions avec l'exercice
    last_interaction DATETIME DEFAULT CURRENT_TIMESTAMP, -- Dernière interaction avec l'exercice
    FOREIGN KEY (id_exercise) REFERENCES exercises(id) ON DELETE CASCADE
);