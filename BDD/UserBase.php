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

    public function getIdWithEmail($email){
        $results = $this->getColumnWithParameter('utilisateurs', ['email' => $email], ['id']);
        return !empty($results) ? $results[0]['id'] : null; // Récupérer l'id ou null si aucun résultat
    }

 

    public function addUser($informations){
        try{
            try{
                $utilisateur = [
                    'pseudo' => $informations['pseudo'],
                    'email' => $informations['email'],
                    'mot_de_passe' => $informations['mot_de_passe']
                ];
                $userId = $this->insertUser($utilisateur);
            }catch(Exception $e){
                echo 'erreur insertion user';
            }
            try{
                $profil = ['utilisateur_id' => $userId,
                    'nom' => $informations['nom'],
                    'prenom' => $informations['prenom'],
                    'date_naissance' => $informations['date_naissance'],
                    'sexe' => $informations['genre']
                ];
                $this->insertProfil($profil);
            }catch(Exception $e){
                echo 'Erreur insertion profil';
            }

            return !empty($this->getColumnWithParameter('utilisateurs', ['email' => $informations['email']]));
            
            
        }catch(Exception $e){
            echo "Cet utilisateur a déjà été inséré dans la BDD";
            return false;
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
        $result = $this->getColumnWithParameter("utilisateurs", ['email' => $email]);
        return !empty($result);
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

        

        $userId = $this->getIdWithEmail($email);
        $results = $this->getColumnWithParameter('verifications_email', ['utilisateur_id' => $userId], ['token']);
        $code = !empty($results) ? $results[0]['token'] : null;

        

        echo "code";
        echo $code;
        echo "code rentré";
        echo $codeVerification;


        return $code == $codeVerification;


        
    }

    /**
     * Génère un code de vérification et le stocke dans la base de données pour l'email spécifié.
     *
     * @param string $email L'email de l'utilisateur.
     * @return string Le code de vérification généré.
     * @throws Exception Si une erreur survient lors de la mise à jour de la base de données.
     */
    public function generateAndStoreVerificationCode($email) {
        try{
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789!@#$%^&*()-_=+[]{};:<.>?';
            $randomCode = '';

            for ($i = 0; $i < 10; $i++) {
                $randomCode .= $characters[random_int(0, strlen($characters) - 1)];
            }

            $userId = $this->getIdWithEmail($email); //Récupérer l'id de l'utilisateur
            
            

            // Vérifiez si un utilisateur a été trouvé
            if ($userId !== null) {
                $this->insertVerificationEmail(['utilisateur_id' => $userId, 'token' => $randomCode]);
                if(!empty($this->getColumnWithParameter('verifications_email', ['utilisateur_id' => $userId]))){
                    $this->deleteVerificationEmail($userId);
                }
            } else {
                echo 'Utilisateur non valide';
            }
            
        }catch(Exception $e){
            echo 'Impossible dajouter code vérification';
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
        $userId = $this->getIdWithEmail($email);
        return empty($this->getColumnWithParameter('verifications_email', ['utilisateur_id' => $userId]));
    }

    public function confirmVerificationCode($email) {
        $userId = $this->getIdWithEmail($email);
        $this->deleteVerificationEmail($userId);
    }


/*---------------------------------Requêtes de supression---------------------------------*/


    /**
     * Supprime un utilisateur et toutes ses données associées.
     *
     * @param int $id L'identifiant de l'utilisateur à supprimer.
     * @return bool Retourne true si la suppression est réussie, false sinon.
     */
    public function deleteUser($id) {
        $this->db->beginTransaction();
        try {
            $tables = [
                'verifications_email',
                'recuperation_mot_de_passe',
                'sessions',
                'preferences',
                'utilisateurs_roles',
                'profils'
            ];

            foreach ($tables as $table) {
                $this->deleteRelatedData($table, $id);
            }

            $query = "DELETE FROM utilisateurs WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            
            return false;
        }
    }

    /**
     * Supprime le profil d'un utilisateur.
     *
     * @param int $userId L'identifiant de l'utilisateur dont le profil doit être supprimé.
     * @return bool Retourne true si la suppression est réussie, false sinon.
     */
    public function deleteProfile($userId) {
        return $this->deleteRelatedData('profils', $userId);
    }

    /**
     * Supprime un rôle.
     *
     * @param int $id L'identifiant du rôle à supprimer.
     * @return bool Retourne true si la suppression est réussie, false sinon.
     */
    public function deleteRole($nom) {
        return $this->executeDelete("DELETE FROM roles WHERE nom = :nom", [':nom' => $nom]);
    }

    /**
     * Supprime une association utilisateur-rôle.
     *
     * @param int $userId L'identifiant de l'utilisateur.
     * @param int $roleId L'identifiant du rôle.
     * @return bool Retourne true si la suppression est réussie, false sinon.
     */
    public function deleteUserRole($userId, $roleId) {
        return $this->executeDelete(
            "DELETE FROM utilisateurs_roles WHERE utilisateur_id = :userId AND role_id = :roleId",
            [':userId' => $userId, ':roleId' => $roleId]
        );
    }

    /**
     * Supprime les préférences d'un utilisateur.
     *
     * @param int $userId L'identifiant de l'utilisateur dont les préférences doivent être supprimées.
     * @return bool Retourne true si la suppression est réussie, false sinon.
     */
    public function deletePreferences($userId) {
        return $this->deleteRelatedData('preferences', $userId);
    }

    public function deleteVerificationEmail($userId) {
        return $this->deleteRelatedData('verifications_email', $userId);
    }

    public function deleteRecuperationMDP($userId){
        return $this->deleteRelatedData('recuperation_mot_de_passe', $userId);
    }

    /**
     * Supprime les vérifications d'email expirées et les données associées.
     *
     * @return int Le nombre d'enregistrements supprimés.
     * @throws Exception Si une erreur survient pendant la suppression.
     */
    public function deleteExpiredVerifications() {
        $this->db->beginTransaction();
        try {
            $tables = ['sessions', 'preferences', 'profils', 'utilisateurs_roles'];
            foreach ($tables as $table) {
                $this->deleteExpiredRelatedData($table);
            }

            $deletedCount = $this->deleteExpiredEmailVerifications();

            $this->db->commit();
            return $deletedCount;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw new Exception("Erreur lors de la suppression des vérifications d'email expirées et des données associées : " . $e->getMessage());
        }
    }

    /**
     * Supprime la récupération d'un mdp
     *
     * @return int Le nombre d'enregistrements supprimés.
     * @throws Exception Si une erreur survient pendant la suppression.
     */
    public function deleteExpiredPasswordRecuperation() {
        $this->db->beginTransaction();
        try {
            $tables = ['sessions', 'preferences', 'profils', 'utilisateurs_roles'];
            foreach ($tables as $table) {
                $this->deleteExpiredRelatedData($table);
            }

            $deletedCount = $this->deleteExpiredEmailVerifications();

            $this->db->commit();
            return $deletedCount;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw new Exception("Erreur lors de la suppression des vérifications d'email expirées et des données associées : " . $e->getMessage());
        }
    }

    /**
     * Supprime les données liées à un utilisateur dans une table spécifique.
     *
     * @param string $table Le nom de la table.
     * @param int $userId L'identifiant de l'utilisateur.
     * @return bool Retourne true si la suppression est réussie, false sinon.
     */
    private function deleteRelatedData($table, $userId) {
        return $this->executeDelete("DELETE FROM $table WHERE utilisateur_id = :userId", [':userId' => $userId]);
    }

    /**
     * Exécute une requête de suppression.
     *
     * @param string $query La requête SQL de suppression.
     * @param array $params Les paramètres de la requête.
     * @return bool Retourne true si la suppression est réussie, false sinon.
     */
    private function executeDelete($query, $params) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            }
            $stmt->execute();
            $this->db->commit();    
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Supprime les données expirées liées aux vérifications d'email dans une table spécifique.
     *
     * @param string $table Le nom de la table.
     */
    private function deleteExpiredRelatedData($table) {
        $query = "DELETE $table FROM $table
                  JOIN verifications_email ON $table.utilisateur_id = verifications_email.utilisateur_id
                  WHERE verifications_email.date_expiration < NOW()";
        $this->db->prepare($query)->execute();
    }

    /**
     * Supprime les vérifications d'email expirées.
     *
     * @return int Le nombre de vérifications supprimées.
     */
    private function deleteExpiredEmailVerifications() {
        $query = "DELETE FROM verifications_email WHERE date_expiration < NOW()";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->rowCount();
    }



/*---------------------------------Requêtes d'insertions---------------------------------*/

    
    public function insertUser($userData){
    $query = "INSERT INTO utilisateurs (pseudo, email, mot_de_passe) VALUES(:pseudo, :email, :mot_de_passe)";
        try{
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute($userData);
            if ($success) {
                return $this->db->lastInsertId(); // Retourne l'ID du nouvel utilisateur
            } else {
                return false;
            }
        }catch(PDOException $e){
            error_log("Erreur d'insertion : " . $e->getMessage());
            throw new Exception("Erreur lors de l'insertion de l'utilisateur : " . $e->getMessage());
        }
    }

    public function insertProfil($userData){
        $query = "INSERT INTO profils (utilisateur_id, nom, prenom, date_naissance, sexe) VALUES (:utilisateur_id, :nom, :prenom, :date_naissance, :sexe)";
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
        if (empty($verificationData['utilisateur_id']) || empty($verificationData['token'])) {
            throw new Exception("L'identifiant de l'utilisateur, le token et la date d'expiration doivent être fournis.");
        }

        // Requête d'insertion
        $query = "INSERT INTO verifications_email (utilisateur_id, token) VALUES (:utilisateur_id, :token)";
        
        try {
            $stmt = $this->db->prepare($query);
            if ($stmt === false) {
                throw new Exception("Erreur lors de la préparation de la requête");
            }

            $result = $stmt->execute([
                ':utilisateur_id' => $verificationData['utilisateur_id'],
                ':token' => $verificationData['token']
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


/*-------------------------Getter--------------------*/


    /**
     * Récupère un utilisateur par son email.
     *
     * @param string $email L'email de l'utilisateur.
     * @return array|null Les données de l'utilisateur ou null si aucun utilisateur trouvé.
     */
    public function getUserByEmail($email) {
        return $this->getColumnWithParameter('utilisateurs', ['email' => $email])[0] ?? null;
    }

    /**
     * Récupère des données d'une table en fonction des paramètres spécifiés.
     *
     * @param string $table Le nom de la table.
     * @param array $parameters Un tableau associatif des paramètres de recherche.
     * @param array $columns Les colonnes à récupérer (par défaut, toutes les colonnes).
     * @return array La liste des données récupérées.
     * @throws PDOException Si une erreur de base de données se produit.
     */
    public function getColumnWithParameter($table, $parameters, $columns = ['*'])
    {
        try {
            // Construire la partie SELECT de la requête
            $selectColumns = implode(', ', $columns);

            // Construire la partie WHERE de la requête
            $whereClauses = [];
            $values = [];
            foreach ($parameters as $column => $value) {
                $whereClauses[] = "$column = :$column";
                $values[":$column"] = $value;
            }
            $whereClause = implode(' AND ', $whereClauses);

            // Construire la requête complète
            $query = "SELECT $selectColumns FROM $table";
            if (!empty($whereClause)) {
                $query .= " WHERE $whereClause";
            }

            // Préparer et exécuter la requête
            $stmt = $this->db->prepare($query);
            $stmt->execute($values);

            // Récupérer les résultats
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log l'erreur et la relancer
            error_log("Erreur lors de la récupération des données : " . $e->getMessage());
            throw $e;
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
