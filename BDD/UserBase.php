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
 * Récupère l'identifiant d'un utilisateur à partir de son email.
 *
 * @param string $email L'email de l'utilisateur.
 * @return int|null L'ID de l'utilisateur si trouvé, sinon null.
 */
public function getIdWithEmail($email) {
    $results = $this->getColumnWithParameter('utilisateurs', ['email' => $email], ['id']);
    return !empty($results) ? $results[0]['id'] : null; // Récupérer l'id ou null si aucun résultat
}

/**
 * Ajoute un nouvel utilisateur et son profil dans la base de données.
 *
 * @param array $informations Les informations de l'utilisateur à ajouter.
 * @return bool True si l'utilisateur a été ajouté avec succès, sinon false.
 * @throws Exception Si une erreur survient lors de l'insertion.
 */
public function addUser($informations) {
    try {
        // Préparation des données utilisateur
        $utilisateur = [
            'pseudo' => $informations['pseudo'],
            'email' => $informations['email'],
            'mot_de_passe' => $informations['mot_de_passe']
        ];
        
        // Insertion de l'utilisateur
        $userId = $this->insertUser($utilisateur);

        // Préparation des données du profil
        $profil = [
            'utilisateur_id' => $userId,
            'nom' => $informations['nom'],
            'prenom' => $informations['prenom'],
            'date_naissance' => $informations['date_naissance'],
            'sexe' => $informations['genre']
        ];
        
        // Insertion du profil
        $this->insertProfil($profil);

        // Vérification de l'insertion réussie
        return !empty($this->getColumnWithParameter('utilisateurs', ['email' => $informations['email']]));
        
    } catch (Exception $e) {
        echo "Cet utilisateur a déjà été inséré dans la BDD";
        return false;
    }
}

/**
 * Vérifie si un email existe dans la base de données.
 *
 * @param string $email L'email à vérifier.
 * @return bool True si l'email existe, sinon false.
 */
public function checkIfMailExist($email) {
    $result = $this->getColumnWithParameter("utilisateurs", ['email' => $email]);
    return !empty($result);
}

/**
 * Vérifie si le code de vérification correspond à celui stocké pour l'utilisateur.
 *
 * @param string $codeVerification Le code de vérification à vérifier.
 * @param string $email L'email de l'utilisateur.
 * @return bool True si le code correspond, sinon false.
 * @throws Exception Si une erreur survient lors de la vérification.
 */
public function verifyAccountWithCode($codeVerification, $email) {
    // Récupération de l'identifiant utilisateur par email
    $userId = $this->getIdWithEmail($email);
    
    // Récupération du code de vérification stocké
    $results = $this->getColumnWithParameter('verifications_email', ['utilisateur_id' => $userId], ['token']);
    $code = !empty($results) ? $results[0]['token'] : null;

    // Comparaison des codes
    return $code == $codeVerification;
}

/**
 * Génère un code de vérification aléatoire et le stocke dans la base de données pour l'email spécifié.
 *
 * @param string $email L'email de l'utilisateur.
 * @return void
 * @throws Exception Si une erreur survient lors de la mise à jour de la base de données.
 */
public function generateAndStoreVerificationCode($email) {
    try {
        // Génération du code aléatoire
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789!@#$%^&*()-_=+[]{};:<.>?';
        $randomCode = '';

        for ($i = 0; $i < 10; $i++) {
            $randomCode .= $characters[random_int(0, strlen($characters) - 1)];
        }

        // Récupération de l'identifiant utilisateur
        $userId = $this->getIdWithEmail($email);

        // Vérification si un utilisateur a été trouvé
        if ($userId !== null) {
            if (!empty($this->getColumnWithParameter('verifications_email', ['utilisateur_id' => $userId]))) {
                // Suppression d'une ancienne vérification si elle existe
                $this->deleteVerificationEmail($userId);
            }
            // Insertion du nouveau code de vérification
            $this->insertVerificationEmail(['utilisateur_id' => $userId, 'token' => $randomCode]);
            
        } else {
            echo 'Utilisateur non valide';
        }
        
    } catch (Exception $e) {
        echo 'Impossible d\'ajouter le code de vérification';
    }
}

/**
 * Vérifie si l'email d'un utilisateur est validé (aucun code de vérification stocké).
 *
 * @param string $email L'email à vérifier.
 * @return bool True si l'email est validé (pas de code), sinon false.
 * @throws Exception Si une erreur survient lors de la vérification.
 */
public function isEmailVerified($email) {
    // Récupération de l'identifiant utilisateur par email
    $userId = $this->getIdWithEmail($email);
    
    // Vérification s'il existe un code de vérification associé à cet utilisateur
    return empty($this->getColumnWithParameter('verifications_email', ['utilisateur_id' => $userId]));
}

/**
 * Confirme le code de vérification en supprimant le code associé à l'utilisateur.
 *
 * @param string $email L'email dont le code doit être confirmé.
 * @return void
 */
public function confirmVerificationCode($email) {
    // Récupération de l'identifiant utilisateur par email
    $userId = $this->getIdWithEmail($email);
    
    // Suppression du code de vérification associé à cet utilisateur
    if ($userId !== null) {
        $this->deleteVerificationEmail($userId);
    }
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

    
    /**
     * Insère un nouvel utilisateur dans la base de données.
     *
     * @param array $userData Les données de l'utilisateur à insérer.
     * @return int|false L'ID du nouvel utilisateur en cas de succès, false en cas d'échec.
     * @throws Exception Si une erreur se produit lors de l'insertion.
     */
    public function insertUser($userData) {
        $success = $this->insertRelatedData('utilisateurs', $userData);
        if ($success) {
            return $this->db->lastInsertId(); // Retourne l'ID du nouvel utilisateur
        } else {
            throw new Exception("Erreur lors de l'insertion de l'utilisateur.");
        }
    }

    /**
     * Insère un profil dans la base de données.
     *
     * @param array $userData Les données du profil à insérer.
     * @throws Exception Si une erreur se produit lors de l'insertion.
     */
    public function insertProfil($userData) {
        $success = $this->insertRelatedData('profils', $userData);
        if (!$success) {
            throw new Exception("Erreur lors de l'insertion du profil.");
        }
    }

    /**
     * Insère un rôle dans la base de données.
     *
     * @param array $roleData Les données du rôle à insérer.
     * @return bool Retourne true si l'insertion réussit, false sinon.
     * @throws Exception Si le nom du rôle est vide ou si une erreur se produit lors de l'insertion.
     */
    public function insertRole($roleData) {
        if (empty($roleData['nom'])) {
            throw new Exception("Le nom du rôle ne peut pas être vide.");
        }
        
        $success = $this->insertRelatedData('roles', $roleData);
        if (!$success) {
            throw new Exception("Erreur lors de l'insertion du rôle.");
        }
        
        return true;
    }

    /**
     * Insère une relation entre un utilisateur et un rôle dans la base de données.
     *
     * @param array $roleData Les données de la relation utilisateur-rôle à insérer.
     * @throws Exception Si les identifiants requis ne sont pas fournis ou si une erreur se produit lors de l'insertion.
     */
    public function insertUtilisateursRoles($roleData) {
        if (empty($roleData['utilisateur_id']) || empty($roleData['role_id'])) {
            throw new Exception("L'identifiant de l'utilisateur et le rôle doivent être fournis.");
        }

        $success = $this->insertRelatedData('utilisateurs_roles', $roleData);
        if (!$success) {
            throw new Exception("Erreur lors de l'insertion des utilisateurs et rôles.");
        }
    }

    /**
     * Insère les préférences d'un utilisateur dans la base de données.
     *
     * @param array $preferencesData Les données des préférences à insérer.
     * @throws Exception Si l'identifiant d'utilisateur n'est pas fourni ou si une erreur se produit lors de l'insertion.
     */
    public function insertUserPreferences($preferencesData) {
        if (empty($preferencesData['utilisateur_id'])) {
            throw new Exception("L'identifiant de l'utilisateur doit être fourni.");
        }

        $success = $this->insertRelatedData('preferences', $preferencesData);
        if (!$success) {
            throw new Exception("Erreur lors de l'insertion des préférences.");
        }
    }

    /**
     * Insère une session dans la base de données.
     *
     * @param array $sessionData Les données de la session à insérer.
     * @throws Exception Si des informations requises ne sont pas fournies ou si une erreur se produit lors de l'insertion.
     */
    public function insertSession($sessionData) {
        if (empty($sessionData['utilisateur_id']) || empty($sessionData['token']) || empty($sessionData['date_expiration'])) {
            throw new Exception("L'identifiant de l'utilisateur, le token et la date d'expiration doivent être fournis.");
        }

        $success = $this->insertRelatedData('sessions', $sessionData);
        if (!$success) {
            throw new Exception("Erreur lors de l'insertion de la session.");
        }
    }

    /**
     * Insère une demande de récupération du mot de passe dans la base de données.
     *
     * @param array $recuperationData Les données pour la récupération du mot de passe à insérer.
     * @throws Exception Si des informations requises ne sont pas fournies ou si une erreur se produit lors de l'insertion.
     */
    public function insertRecuperationMotDePasse($recuperationData) {
        if (empty($recuperationData['utilisateur_id']) || empty($recuperationData['token']) || empty($recuperationData['date_expiration'])) {
            throw new Exception("L'identifiant de l'utilisateur, le token et la date d'expiration doivent être fournis.");
        }

        $success = $this->insertRelatedData('recuperation_mot_de_passe', $recuperationData);
        if (!$success) {
            throw new Exception("Erreur lors de l'insertion pour la récupération du mot de passe.");
        }
    }

    /**
     * Insère une vérification d'email dans la base de données.
     *
     * @param array $verificationData Les données pour la vérification d'email à insérer.
     * @throws Exception Si des informations requises ne sont pas fournies ou si une erreur se produit lors de l'insertion.
     */
    public function insertVerificationEmail($verificationData) {
        if (empty($verificationData['utilisateur_id']) || empty($verificationData['token'])) {
            throw new Exception("L'identifiant de l'utilisateur et le token doivent être fournis.");
        }

        // Requête d'insertion
        $success = $this->insertRelatedData('verifications_email', $verificationData);
        if (!$success) {
            throw new Exception("Erreur lors de l'insertion pour la vérification d'email.");
        }
    }

    /**
     * Insère des données liées dans la table spécifiée.
     *
     * @param string $table Le nom de la table où les données seront insérées.
     * @param array $datas Les données à insérer sous forme clé-valeur.
     * @return bool Retourne true si l'insertion réussit, false sinon.
     * @throws Exception Si une erreur se produit lors de la préparation ou exécution de la requête SQL.
     */
    private function insertRelatedData($table, $datas) {
        // Construire les colonnes et les placeholders
        $columns = implode(", ", array_keys($datas));
        $placeholders = implode(", ", array_map(function ($key) {
            return ":$key";
        }, array_keys($datas)));

        return $this->executeInsert("INSERT INTO $table ($columns) VALUES ($placeholders)", $datas);
    }

    /**
     * Exécute une requête d'insertion dans la base de données.
     *
     * @param string $query La requête SQL d'insertion à exécuter.
     * @param array $params Les paramètres à associer à la requête SQL.
     * @return bool Retourne true si l'insertion réussit, false sinon.
     * @throws Exception Si une erreur se produit lors de la préparation ou exécution de la requête SQL, y compris les doublons.
     */
    private function executeInsert($query, $params) {
        try {
            // Préparation
            $stmt = $this->db->prepare($query);
            if ($stmt === false) {
                throw new Exception("Erreur lors de la préparation de la requête");
            }

            // Exécution avec liaison des paramètres
            if (!$stmt->execute($params)) {
                throw new Exception("Erreur lors de l'exécution de la requête");
            }

            return true; // Retourne vrai si l'insertion a réussi

        } catch (PDOException $e) {
            // Gestion spécifique pour les doublons
            if ($e->getCode() == '23000') { // Code pour "Duplicate entry"
                throw new Exception("Erreur : entrée en double détectée.");
            }
            throw new Exception("Erreur lors de l'exécution : " . $e->getMessage());
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


}

?>
