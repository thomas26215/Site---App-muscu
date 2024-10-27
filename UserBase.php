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
     * Insère un nouvel utilisateur dans la base de données.
     *
     * @param array $userData Les données de l'utilisateur.
     * @return bool True si l'insertion réussit.
     * @throws Exception Si une erreur survient lors de l'insertion.
     */
    public function insertUser($userData) {
        $query = "INSERT INTO utilisateur (nom, prenom, age, genre, email, mot_de_passe, date_naissance) 
                  VALUES (:nom, :prenom, :age, :genre, :email, :mot_de_passe, :date_naissance)";
        try {
            $stmt = $this->db->prepare($query);
            return $stmt->execute($userData);
        } catch (PDOException $e) {
            error_log("Erreur d'insertion : " . $e->getMessage());
            throw new Exception("Erreur lors de l'insertion de l'utilisateur : " . $e->getMessage());
        }
    }

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

}

?>
