<?php

class UserBase {
    private $db;
    private $dataSourceName;

    public function __construct() {
        $this->dataSourceName = 'sqlite:' . __DIR__ . '/utilisateur.db';
        try {
            $this->db = new PDO($this->dataSourceName);
            // Activer le mode d'erreur pour PDO
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    /**
     * Récupère des enregistrements d'une table spécifique selon des conditions données.
     *
     * Cette méthode permet de rechercher des enregistrements dans n'importe quelle table
     * de la base de données en spécifiant des conditions de filtrage flexibles.
     *
     * @param string $table     Le nom de la table dans laquelle effectuer la recherche.
     * @param array  $conditions Un tableau associatif des conditions de recherche, 
     *                           où les clés sont les noms des colonnes et les valeurs 
     *                           sont les valeurs recherchées.
     *
     * @return array Un tableau d'enregistrements correspondant aux conditions spécifiées.
     *               Chaque enregistrement est représenté par un tableau associatif.
     *               Retourne un tableau vide si aucun enregistrement n'est trouvé.
     *
     * @throws Exception Si une erreur survient lors de l'exécution de la requête.
     *
     * @example
     * // Recherche d'un utilisateur par ID
     * $user = $this->getByCondition('utilisateur', ['id' => 1]);
     *
     * // Recherche d'utilisateurs par nom et date de naissance
     * $users = $this->getByCondition('utilisateur', [
     *     'nom' => 'Dupont',
     *     'date_naissance' => '1990-01-01'
     * ]);
     */
    public function getByCondition($table, $conditions) {
        $whereClause = [];
        $params = [];

        foreach ($conditions as $key => $value) {
            $whereClause[] = "{$key} = :{$key}";
            $params[":{$key}"] = $value;
        }

        $whereString = implode(' AND ', $whereClause);
        $query = "SELECT * FROM {$table} WHERE {$whereString}";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            // Récupérer tous les résultats
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $results; // Retourne un tableau de résultats (vide si aucun résultat trouvé)
        } catch (PDOException $e) {
            // Gestion de l'erreur
            error_log("Erreur PDO dans getByCondition : " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la récupération des données.");
        }
    }


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

	public function getUserByEmail($email) {
	    return $this->getByCondition('utilisateur', ['email' => $email])[0] ?? null;
	}


}

?>