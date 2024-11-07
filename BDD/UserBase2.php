<?php

class UserBase {
    private $db;
    private $dataSourceName;

    /**
     * Initialise la connexion à la base de données.
     *
     * @throws Exception Si une erreur survient lors de la connexion à la base de données.
     */
    public function __construct() {
        $this->dataSourceName = 'sqlite:' . __DIR__ . '/utilisateur.db';
        try {
            $this->db = new PDO($this->dataSourceName);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    /**
     * Récupère des enregistrements dans une table en fonction de conditions.
     *
     * @param string $table Le nom de la table.
     * @param array $conditions Un tableau associatif des conditions (colonne => valeur).
     * @return array Un tableau d'enregistrements trouvés.
     * @throws Exception Si une erreur survient lors de l'exécution de la requête.
     */
    public function getRecordsByConditions($table, $conditions) {
        $whereClause = [];
        $params = [];

        foreach ($conditions as $key => $value) {
            $whereClause[] = "{$key} = :{$key}";
            $params[":{$key}"] = $value;
        }

        $query = "SELECT * FROM {$table} WHERE " . implode(' AND ', $whereClause);

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur dans getRecordsByConditions : " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération des données.");
        }
    }


    /**
     *
     * Vérifie si le mail existe
     * 
     * @param string $email le mail de l'utilisateur
     * @return bool True si le mail existe dans la base de données
     *
     */

    public function checkIfMailExist($email) {
        if (!$this->db instanceof PDO) {
            throw new Exception("La connexion à la base de données n'est pas établie.");
        }

        $query = "SELECT COUNT(*) FROM utilisateur WHERE LOWER(email) = LOWER(:email)";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([':email' => trim($email)]);
            $count = $stmt->fetchColumn();
            
            return $count > 0;
        } catch(PDOException $e) {
            error_log("Erreur de vérification d'email : " . $e->getMessage());
            throw new Exception("Erreur lors de la vérification de l'email : " . $e->getMessage());
        }
    }

    /**
     * Insère un nouvel utilisateur dans la base de données.
     *
     * @param array $userData Les données de l'utilisateur.
     * @return bool True si l'insertion réussit.
     * @throws Exception Si une erreur survient lors de l'insertion.
     */
    /*public function insertUser($userData) {
        $query = "INSERT INTO utilisateur (nom, prenom, age, genre, email, mot_de_passe, date_naissance) 
                  VALUES (:nom, :prenom, :age, :genre, :email, :mot_de_passe, :date_naissance)";
        try {
            $stmt = $this->db->prepare($query);
            return $stmt->execute($userData);
        } catch (PDOException $e) {
            error_log("Erreur d'insertion : " . $e->getMessage());
            throw new Exception("Erreur lors de l'insertion de l'utilisateur : " . $e->getMessage());
        }
    }*/

    /**
     * Récupère un utilisateur par son email.
     *
     * @param string $email L'email de l'utilisateur.
     * @return array|null Les données de l'utilisateur ou null si aucun utilisateur trouvé.
     */
    public function getUserByEmail($email) {
        return $this->getRecordsByConditions('utilisateur', ['email' => $email])[0] ?? null;
    }

    /**
     * Vérifie si le code de vérification de l'utilisateur correspond.
     *
     * @param string $codeVerification Le code de vérification à vérifier.
     * @param string $email L'email de l'utilisateur.
     * @return bool True si le code correspond, sinon false.
     * @throws Exception Si une erreur survient lors de la vérification.
     */
    public function verifyAccountWithCode($codeVerification, $email) {
        $query = "SELECT code_verification FROM utilisateur WHERE email = :email";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute([':email' => $email]);
            $code = $stmt->fetchColumn();
            return $code == $codeVerification;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification du code : " . $e->getMessage());
            throw new Exception("Erreur lors de la vérification du code de l'utilisateur.");
        }
    }

    /**
     * Génère un code de vérification et le stocke dans la base de données pour l'email spécifié.
     *
     * @param string $email L'email de l'utilisateur.
     * @return string Le code de vérification généré.
     * @throws Exception Si une erreur survient lors de la mise à jour de la base de données.
     */
    public function generateAndStoreVerificationCode($email) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789!@#$%^&*()-_=+[]{};:<.>?';
        $randomCode = '';

        for ($i = 0; $i < 10; $i++) {
            $randomCode .= $characters[random_int(0, strlen($characters) - 1)];
        }

        $query = "UPDATE utilisateur SET code_verification = :code_verification WHERE email = :email";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':code_verification', $randomCode, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            return $randomCode;
        } catch (PDOException $e) {
            error_log("Erreur lors de la génération du code de vérification : " . $e->getMessage());
            throw new Exception("Erreur lors de la génération du code de vérification.");
        }
    }

    /**
     * Vérifie si l'email de l'utilisateur est validé (aucun code de vérification stocké).
     *
     * @param string $email L'email de l'utilisateur.
     * @return bool True si l'email est validé (pas de code), sinon false.
     * @throws Exception Si une erreur survient lors de la vérification.
     */
    public function isEmailVerified($email) {
        $query = "SELECT code_verification FROM utilisateur WHERE email = :email";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            return empty($stmt->fetchColumn());
        } catch (PDOException $e) {
            error_log("Erreur dans isEmailVerified : " . $e->getMessage());
            throw new Exception("Erreur lors de la vérification de l'email.");
        }
    }

    public function confirmVerificationCode($email) {
    $query = "UPDATE utilisateur SET code_verification = '' WHERE email = :email";

    try {
        $stmt = $this->db->prepare($query);
        $stmt->execute([':email' => $email]);
        return $stmt->rowCount() > 0; // Retourne vrai si la mise à jour a réussi
    } catch (PDOException $e) {
        error_log("Erreur lors de la confirmation du code de vérification : " . $e->getMessage());
        throw new Exception("Erreur lors de la confirmation du code de vérification.");
    }
}






/*---------------------------------Requêtes d'insertions---------------------------------*/

    
    public function insertUser($userData){
        $query = "INSERT INTO utilisateurs (pseudo, email, mot_de_passe) VALUES(:pseudo, :email, :mot_de_passe)";
        try{
            $stmt = $this->db->prepare($query);
            return $stmt->execute($userData);
        }catch(PDOException $e){
            error_log("Erreur d'insertion : " . $e->getMessage());
            throw new Exception("Erreur lors de l'insertion de l'utilisateur : " . $e->getMessage());
        }
    }

    public function insertProfil($userData){
        $query = "INSERT INTO profils (utilisateur_id, nom, prenom, date_naissance) VALUES (:utilisateur_id, :nom, :prenom, :date_naissance)";
        try{
            $stmt = $this->db->prepare($query);
            return $stmt->execute($userData);
        }catch(PDOException $e){
            error_log("Erreur d'insertion : " . $e->getMessage());
            throw new Exception("Erreur lors de l'insertion du profil : " . $e->getMessage());
        }
    }

    public function insertRole($roleData){
        if (empty($roleData['nom'])) {
            return false;
        }

        $query = "INSERT INTO roles (nom, description) VALUES (:nom, :description)";
        try {
            $stmt = $this->db->prepare($query);
            if ($stmt === false) {
                throw new Exception("Erreur lors de la préparation de la requête");
            }
            $result = $stmt->execute($roleData);
            if ($result === false) {
                throw new Exception("Erreur lors de l'exécution de la requête");
            }
            return true;
        } catch(PDOException $e) {
            // Gestion spécifique pour les doublons
            if ($e->getCode() == '23000' || $e->getCode() == '1062') { // Codes pour "Duplicate entry"
                throw new Exception("Erreur lors de l'insertion du rôle : Le rôle existe déjà");
            }
            throw new Exception("Erreur lors de l'insertion du rôle : " . $e->getMessage());
        }
    }

    public function insertUtilisateursRoles($roleData) {
        // Vérification des paramètres
        if (empty($roleData['utilisateur_id']) || empty($roleData['role_id'])) {
            throw new Exception("L'identifiant de l'utilisateur et le rôle doivent être fournis.");
        }

        // Requête d'insertion
        $query = "INSERT INTO utilisateurs_roles (utilisateur_id, role_id) VALUES (:utilisateur_id, :role_id)";
        
        try {
            $stmt = $this->db->prepare($query);
            if ($stmt === false) {
                throw new Exception("Erreur lors de la préparation de la requête");
            }

            $result = $stmt->execute([
                ':utilisateur_id' => $roleData['utilisateur_id'],
                ':role_id' => $roleData['role_id']
            ]);

            if ($result === false) {
                throw new Exception("Erreur lors de l'exécution de la requête");
            }

            return true; // Retourne vrai si l'insertion a réussi

        } catch (PDOException $e) {
            // Gestion spécifique pour les doublons
            if ($e->getCode() == '23000' || $e->getCode() == '1062') { // Codes pour "Duplicate entry"
                throw new Exception("Erreur lors de l'attribution du rôle à l'utilisateur : Cette relation existe déjà.");
            }
            throw new Exception("Erreur lors de l'attribution du rôle à l'utilisateur : " . $e->getMessage());
        }
    }

    public function insertUserPreferences($preferencesData) {
        // Vérification des paramètres
        if (empty($preferencesData['utilisateur_id'])) {
            throw new Exception("L'identifiant de l'utilisateur doit être fourni.");
        }

        // Requête d'insertion
        $query = "INSERT INTO preferences (utilisateur_id, theme, notifications_email, langue) VALUES (:utilisateur_id, :theme, :notifications_email, :langue)";
        
        try {
            $stmt = $this->db->prepare($query);
            if ($stmt === false) {
                throw new Exception("Erreur lors de la préparation de la requête");
            }

            $result = $stmt->execute([
                ':utilisateur_id' => $preferencesData['utilisateur_id'],
                ':theme' => $preferencesData['theme'] ?? null,
                ':notifications_email' => $preferencesData['notifications_email'] ?? true,
                ':langue' => $preferencesData['langue'] ?? null
            ]);

            return true; // Retourne vrai si l'insertion a réussi

        } catch (PDOException $e) {
            // Gestion spécifique pour les doublons
            if ($e->getCode() == '23000') { // Code pour "Duplicate entry"
                throw new Exception("Erreur lors de l'insertion des préférences : Les préférences existent déjà pour cet utilisateur.");
            }
            throw new Exception("Erreur lors de l'insertion des préférences : " . $e->getMessage());
        }
    }

    public function insertSession($sessionData) {
        // Vérification des paramètres
        if (empty($sessionData['utilisateur_id']) || empty($sessionData['token']) || empty($sessionData['date_expiration'])) {
            throw new Exception("L'identifiant de l'utilisateur, le token et la date d'expiration doivent être fournis.");
        }

        // Requête d'insertion
        $query = "INSERT INTO sessions (utilisateur_id, token, date_expiration) VALUES (:utilisateur_id, :token, :date_expiration)";
        
        try {
            $stmt = $this->db->prepare($query);
            if ($stmt === false) {
                throw new Exception("Erreur lors de la préparation de la requête");
            }

            $result = $stmt->execute([
                ':utilisateur_id' => $sessionData['utilisateur_id'],
                ':token' => $sessionData['token'],
                ':date_expiration' => $sessionData['date_expiration']
            ]);

            if ($result === false) {
                throw new Exception("Erreur lors de l'exécution de la requête");
            }

            return true; // Retourne vrai si l'insertion a réussi

        } catch (PDOException $e) {
            // Gestion spécifique pour les doublons
            if ($e->getCode() == '23000') { // Code pour "Duplicate entry"
                throw new Exception("Erreur lors de l'insertion de la session : Le token existe déjà.");
            }
            throw new Exception("Erreur lors de l'insertion de la session : " . $e->getMessage());
        }
    }

    public function insertRecuperationMotDePasse($recuperationData) {
        // Vérification des paramètres
        if (empty($recuperationData['utilisateur_id']) || empty($recuperationData['token']) || empty($recuperationData['date_expiration'])) {
            throw new Exception("L'identifiant de l'utilisateur, le token et la date d'expiration doivent être fournis.");
        }

        // Requête d'insertion
        $query = "INSERT INTO recuperation_mot_de_passe (utilisateur_id, token, date_expiration) VALUES (:utilisateur_id, :token, :date_expiration)";
        
        try {
            $stmt = $this->db->prepare($query);
            if ($stmt === false) {
                throw new Exception("Erreur lors de la préparation de la requête");
            }

            $result = $stmt->execute([
                ':utilisateur_id' => $recuperationData['utilisateur_id'],
                ':token' => $recuperationData['token'],
                ':date_expiration' => $recuperationData['date_expiration']
            ]);

            if ($result === false) {
                throw new Exception("Erreur lors de l'exécution de la requête");
            }

            return true; // Retourne vrai si l'insertion a réussi

        } catch (PDOException $e) {
            // Gestion spécifique pour les doublons
            if ($e->getCode() == '23000') { // Code pour "Duplicate entry"
                throw new Exception("Erreur lors de l'insertion de la récupération de mot de passe : Le token existe déjà.");
            }
            throw new Exception("Erreur lors de l'insertion de la récupération de mot de passe : " . $e->getMessage());
        }
    }

    public function insertVerificationEmail($verificationData) {
        // Vérification des paramètres
        if (empty($verificationData['utilisateur_id']) || empty($verificationData['token']) || empty($verificationData['date_expiration'])) {
            throw new Exception("L'identifiant de l'utilisateur, le token et la date d'expiration doivent être fournis.");
        }

        // Requête d'insertion
        $query = "INSERT INTO verifications_email (utilisateur_id, token, date_expiration) VALUES (:utilisateur_id, :token, :date_expiration)";
        
        try {
            $stmt = $this->db->prepare($query);
            if ($stmt === false) {
                throw new Exception("Erreur lors de la préparation de la requête");
            }

            $result = $stmt->execute([
                ':utilisateur_id' => $verificationData['utilisateur_id'],
                ':token' => $verificationData['token'],
                ':date_expiration' => $verificationData['date_expiration']
            ]);

            if ($result === false) {
                throw new Exception("Erreur lors de l'exécution de la requête");
            }

            return true; // Retourne vrai si l'insertion a réussi

        } catch (PDOException $e) {
            // Gestion spécifique pour les doublons
            if ($e->getCode() == '23000') { // Code pour "Duplicate entry"
                throw new Exception("Erreur lors de l'insertion de la vérification d'email : Le token existe déjà.");
            }
            throw new Exception("Erreur lors de l'insertion de la vérification d'email : " . $e->getMessage());
        }
    }










        




/*
-- Table roles
CREATE TABLE roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(20) NOT NULL UNIQUE,
    description TEXT
);
*/

}

?>
