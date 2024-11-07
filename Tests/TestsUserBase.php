<?php

use PHPUnit\Framework\TestCase;
require_once '../BDD/UserBase2.php';

class TestsUserBase extends TestCase
{
    private $userBase;
    private $dbMock;

    protected function setUp(): void{
        $this->dbMock = $this->createMock(PDO::class);

        $this->userBase = new UserBase();
        $this->setPrivateProperty($this->userBase, 'db', $this->dbMock);
    }
    private function setPrivateProperty($object, $propertyName, $value){
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    public function testInsertUserSuccess(){
        $userData = [
            'pseudo' => 'testuser',
            'email' => 'test@example.com',
            'mot_de_passe' => 'password123'
        ];

        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->once())
                 ->method('execute')
                 ->with($userData)
                 ->willReturn(true);

        $this->dbMock->expects($this->once())
                     ->method('prepare')
                     ->with("INSERT INTO utilisateurs (pseudo, email, mot_de_passe) VALUES(:pseudo, :email, :mot_de_passe)")
                     ->willReturn($stmtMock);

        $result = $this->userBase->insertUser($userData);
        $this->assertTrue($result);
    }

    public function testInsertUserFailure(){
        $userData = [
            'pseudo' => 'testuser',
            'email' => 'test@example.com',
            'mot_de_passe' => 'password123'
        ];

        // Créer un mock pour PDOStatement qui simule une erreur
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->once())
                 ->method('execute')
                 ->willThrowException(new PDOException('Test exception'));

        // Configurer le mock PDO
        $this->dbMock->expects($this->once())
                     ->method('prepare')
                     ->willReturn($stmtMock);

        // Vérifier que la méthode lance bien une exception
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erreur lors de l\'insertion de l\'utilisateur');

        $this->userBase->insertUser($userData);
    }

    public function testInsertUserAndProfil(){
        // Données pour l'utilisateur
        $userData = [
            'pseudo' => 'jdupont',
            'email' => 'jean.dupont@example.com',
            'mot_de_passe' => 'motdepasse123'
        ];

        // Données pour le profil
        $profilData = [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'date_naissance' => '1990-01-01'
        ];

        // Mock pour l'insertion de l'utilisateur
        $userStmtMock = $this->createMock(PDOStatement::class);
        $userStmtMock->expects($this->once())
                     ->method('execute')
                     ->with($userData)
                     ->willReturn(true);

        // Mock pour l'insertion du profil
        $profilStmtMock = $this->createMock(PDOStatement::class);
        $profilStmtMock->expects($this->once())
                       ->method('execute')
                       ->willReturn(true);

        // Configurer le mock PDO
        $this->dbMock->expects($this->exactly(2))
                     ->method('prepare')
                     ->withConsecutive(
                         ["INSERT INTO utilisateurs (pseudo, email, mot_de_passe) VALUES(:pseudo, :email, :mot_de_passe)"],
                         ["INSERT INTO profils (utilisateur_id, nom, prenom, date_naissance) VALUES (:utilisateur_id, :nom, :prenom, :date_naissance)"]
                     )
                     ->willReturnOnConsecutiveCalls($userStmtMock, $profilStmtMock);

        // Appeler les méthodes et vérifier les résultats
        $userResult = $this->userBase->insertUser($userData);
        $this->assertTrue($userResult);

        $profilResult = $this->userBase->insertProfil(array_merge(['utilisateur_id' => 1], $profilData));
        $this->assertTrue($profilResult);
    }

    public function testInsertUserAndProfilFailure()
    {
        // Données pour l'utilisateur
        $userData = [
            'pseudo' => 'jdupont',
            'email' => 'jean.dupont@example.com',
            'mot_de_passe' => 'motdepasse123'
        ];

        // Données pour le profil
        $profilData = [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'date_naissance' => '1990-01-01'
        ];

        // Mock pour l'insertion de l'utilisateur (succès)
        $userStmtMock = $this->createMock(PDOStatement::class);
        $userStmtMock->expects($this->once())
                     ->method('execute')
                     ->with($userData)
                     ->willReturn(true);

        // Mock pour l'insertion du profil (échec)
        $profilStmtMock = $this->createMock(PDOStatement::class);
        $profilStmtMock->expects($this->once())
                       ->method('execute')
                       ->willThrowException(new PDOException('Erreur lors de l\'insertion du profil'));

        // Configurer le mock PDO
        $this->dbMock->expects($this->exactly(2))
                     ->method('prepare')
                     ->withConsecutive(
                         ["INSERT INTO utilisateurs (pseudo, email, mot_de_passe) VALUES(:pseudo, :email, :mot_de_passe)"],
                         ["INSERT INTO profils (utilisateur_id, nom, prenom, date_naissance) VALUES (:utilisateur_id, :nom, :prenom, :date_naissance)"]
                     )
                     ->willReturnOnConsecutiveCalls($userStmtMock, $profilStmtMock);

        // Appeler les méthodes et vérifier les résultats
        $userResult = $this->userBase->insertUser($userData);
        $this->assertTrue($userResult);

        // Vérifier que l'exception est lancée lors de l'ajout du profil
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erreur lors de l\'insertion du profil');

        $this->userBase->insertProfil(array_merge(['utilisateur_id' => 1], $profilData));
    }

    public function testInsertRoleSuccess() {
        // Données du rôle à insérer
        $roleData = [
            'nom' => 'Admin',
            'description' => 'Administrateur du système'
        ];

        // Configuration du mock pour simuler le comportement de PDO
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->once())
                 ->method('execute')
                 ->with($roleData)
                 ->willReturn(true);

        // Préparation du mock PDO pour retourner notre mock de statement
        $this->dbMock->expects($this->once())
                     ->method('prepare')
                     ->willReturn($stmtMock);

        // Appel de la méthode à tester
        $result = $this->userBase->insertRole($roleData);

        // Vérification que l'insertion a réussi
        $this->assertTrue($result);
    }

    public function testInsertRoleFailures() {
        // Vérification du cas où le nom du rôle est vide
        $roleDataEmptyName = [
            'nom' => '',
            'description' => 'Rôle sans nom'
        ];
        
        $result = $this->userBase->insertRole($roleDataEmptyName);
        $this->assertFalse($result, "La méthode devrait retourner false si le nom est vide.");

        // Configuration du mock pour simuler une erreur d'exécution
        $roleDataExecutionFailure = [
            'nom' => 'User',
            'description' => 'Utilisateur standard'
        ];
        
        $stmtMockExecutionFailure = $this->createMock(PDOStatement::class);
        $stmtMockExecutionFailure->method('execute')
                                 ->willReturn(false);

        // Préparation du mock PDO pour retourner notre mock de statement
        $this->dbMock->expects($this->once())
                     ->method('prepare')
                     ->willReturn($stmtMockExecutionFailure);

        // Vérification que l'exception est bien lancée
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Erreur lors de l'exécution de la requête");

        // Appel de la méthode à tester
        $this->userBase->insertRole($roleDataExecutionFailure);

        // Configuration du mock pour simuler une exception de doublon
        $roleDataDuplicateEntry = [
            'nom' => 'Admin',
            'description' => 'Administrateur du système'
        ];

        $stmtMockDuplicateEntry = $this->createMock(PDOStatement::class);
        
        $stmtMockDuplicateEntry->method('execute')
                               ->will($this->throwException(new PDOException("Duplicate entry", 23000)));

        // Préparation du mock PDO pour retourner notre mock de statement
        $this->dbMock->expects($this->once())
                     ->method('prepare')
                     ->willReturn($stmtMockDuplicateEntry);

        // Vérification que l'exception est bien lancée
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Erreur lors de l'insertion du rôle : Le rôle existe déjà");

        // Appel de la méthode à tester
        $this->userBase->insertRole($roleDataDuplicateEntry);
    }

    public function testInsertUtilisateursRolesSuccess() {
        // Données pour l'attribution de rôle
        $roleData = [
            'utilisateur_id' => 1,
            'role_id' => 2
        ];

        // Configuration du mock pour simuler le comportement de PDO
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->once())
                 ->method('execute')
                 ->with([
                     ':utilisateur_id' => 1,
                     ':role_id' => 2
                 ])
                 ->willReturn(true);

        // Préparation du mock PDO pour retourner notre mock de statement
        $this->dbMock->expects($this->once())
                     ->method('prepare')
                     ->willReturn($stmtMock);

        // Appel de la méthode à tester
        $result = $this->userBase->insertUtilisateursRoles($roleData);

        // Vérification que l'attribution a réussi
        $this->assertTrue($result);
    }

    public function testInsertUtilisateursRolesFailures() {
        // Vérification du cas où l'identifiant utilisateur est vide
        $roleDataEmptyUser = [
            'utilisateur_id' => '',
            'role_id' => 2
        ];

        // Vérification que l'exception est bien lancée pour utilisateur_id vide
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("L'identifiant de l'utilisateur et le rôle doivent être fournis.");
        
        // Appel de la méthode à tester
        $this->userBase->insertUtilisateursRoles($roleDataEmptyUser);

        // Vérification du cas où l'identifiant rôle est vide
        $roleDataEmptyRole = [
            'utilisateur_id' => 1,
            'role_id' => ''
        ];

        // Vérification que l'exception est bien lancée pour role_id vide
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("L'identifiant de l'utilisateur et le rôle doivent être fournis.");

        // Appel de la méthode à tester
        $this->userBase->insertUtilisateursRoles($roleDataEmptyRole);

        // Configuration du mock pour simuler une erreur d'exécution
        $roleDataExecutionFailure = [
            'utilisateur_id' => 1,
            'role_id' => 2
        ];

        $stmtMockExecutionFailure = $this->createMock(PDOStatement::class);
        
        $stmtMockExecutionFailure->method('execute')
                                 ->willReturn(false);

        // Préparation du mock PDO pour retourner notre mock de statement
        $this->dbMock->expects($this->once())
                     ->method('prepare')
                     ->willReturn($stmtMockExecutionFailure);

        // Vérification que l'exception est bien lancée lors d'une erreur d'exécution
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Erreur lors de l'attribution du rôle à l'utilisateur : Erreur lors de l'exécution de la requête");

        // Appel de la méthode à tester
        $this->userBase->insertUtilisateursRoles(roleDataExecutionFailure);

        // Configuration du mock pour simuler une exception de doublon
        $roleDataDuplicateEntry = [
            'utilisateur_id' => 1,
            'role_id' => 2
        ];

        $stmtMockDuplicateEntry = $this->createMock(PDOStatement::class);
        
        $stmtMockDuplicateEntry->method('execute')
                               ->will($this->throwException(new PDOException("Duplicate entry", 23000)));

        // Préparation du mock PDO pour retourner notre mock de statement
        $this->dbMock->expects($this->once())
                     ->method('prepare')
                     ->willReturn($stmtMockDuplicateEntry);

        // Vérification que l'exception est bien lancée lors d'une tentative d'insertion en doublon
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Erreur lors de l'attribution du rôle à l'utilisateur : Cette relation existe déjà.");

        // Appel de la méthode à tester
        $this->userBase->insertUtilisateursRoles($roleDataDuplicateEntry);
    }

    public function testInsertUserPreferencesSuccess() {
        // Données valides pour l'insertion des préférences
        $preferencesData = [
            'utilisateur_id' => 1,
            'theme' => 'dark',
            'notifications_email' => true,
            'langue' => 'fr'
        ];

        // Configuration du mock pour simuler le comportement de PDO
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->once())
                 ->method('execute')
                 ->with([
                     ':utilisateur_id' => 1,
                     ':theme' => 'dark',
                     ':notifications_email' => true,
                     ':langue' => 'fr'
                 ])
                 ->willReturn(true);

        // Préparation du mock PDO pour retourner notre mock de statement
        $this->dbMock->expects($this->once())
                     ->method('prepare')
                     ->willReturn($stmtMock);

        // Appel de la méthode à tester
        $result = $this->userBase->insertUserPreferences($preferencesData);

        // Vérification que l'insertion a réussi
        $this->assertTrue($result);
    }

    public function testInsertUserPreferencesFailures() {
        // Vérification du cas où l'identifiant utilisateur est vide
        $preferencesDataEmptyUser = [
            'utilisateur_id' => '',
            'theme' => 'dark',
            'notifications_email' => true,
            'langue' => 'fr'
        ];

        // Vérification que l'exception est bien lancée pour utilisateur_id vide
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("L'identifiant de l'utilisateur doit être fourni.");
        
        // Appel de la méthode à tester
        $this->userBase->insertUserPreferences($preferencesDataEmptyUser);

        // Configuration du mock pour simuler une erreur d'insertion (doublon)
        $preferencesDataDuplicate = [
            'utilisateur_id' => 1,
            'theme' => 'dark',
            'notifications_email' => true,
            'langue' => 'fr'
        ];

        // Simuler le comportement de PDO pour le doublon
        $stmtMockDuplicate = $this->createMock(PDOStatement::class);
        
        $stmtMockDuplicate->method('execute')
                          ->will($this->throwException(new PDOException("Duplicate entry", 23000)));

        // Préparation du mock PDO pour retourner notre mock de statement
        $this->dbMock->expects($this->once())
                     ->method('prepare')
                     ->willReturn($stmtMockDuplicate);

        // Vérification que l'exception est bien lancée lors d'une tentative d'insertion en doublon
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Erreur lors de l'insertion des préférences : Les préférences existent déjà pour cet utilisateur.");

        // Appel de la méthode à tester
        $this->userBase->insertUserPreferences($preferencesDataDuplicate);

        // Configuration du mock pour simuler une erreur d'exécution générale
        $preferencesDataExecutionFailure = [
            'utilisateur_id' => 2,
            'theme' => 'light',
            'notifications_email' => false,
            'langue' => 'en'
        ];

        // Simuler le comportement de PDO pour une erreur d'exécution
        $stmtMockExecutionFailure = $this->createMock(PDOStatement::class);
        
        $stmtMockExecutionFailure->method('execute')
                                 ->will($this->throwException(new PDOException("Erreur lors de l'exécution", 500)));

        // Préparation du mock PDO pour retourner notre mock de statement
        $this->dbMock->expects($this->once())
                     ->method('prepare')
                     ->willReturn($stmtMockExecutionFailure);

        // Vérification que l'exception est bien lancée lors d'une erreur d'exécution
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Erreur lors de l'insertion des préférences : Erreur lors de l'exécution");

        // Appel de la méthode à tester
        $this->userBase->insertUserPreferences($preferencesDataExecutionFailure);
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

    public function testInsertSessionSuccess() {
        // Données valides pour l'insertion de session
        $sessionData = [
            'utilisateur_id' => 1,
            'token' => 'abc123xyz',
            'date_expiration' => '2024-12-31 23:59:59'
        ];

        // Configuration du mock pour simuler le comportement de PDO
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->once())
                 ->method('execute')
                 ->with([
                     ':utilisateur_id' => 1,
                     ':token' => 'abc123xyz',
                     ':date_expiration' => '2024-12-31 23:59:59'
                 ])
                 ->willReturn(true);

        // Préparation du mock PDO pour retourner notre mock de statement
        $this->dbMock->expects($this->once())
                     ->method('prepare')
                     ->willReturn($stmtMock);

        // Appel de la méthode à tester
        $result = $this->userBase->insertSession($sessionData);

        // Vérification que l'insertion a réussi
        $this->assertTrue($result);
    }


    public function testInsertSessionFailures() {
        // Vérification du cas où l'identifiant utilisateur est vide
        $sessionDataEmptyUser = [
            'utilisateur_id' => '',
            'token' => 'abc123xyz',
            'date_expiration' => '2024-12-31 23:59:59'
        ];

        // Vérification que l'exception est bien lancée pour utilisateur_id vide
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("L'identifiant de l'utilisateur, le token et la date d'expiration doivent être fournis.");

        // Appel de la méthode à tester
        $this->userBase->insertSession($sessionDataEmptyUser);

        // Vérification du cas où le token est vide
        $sessionDataEmptyToken = [
            'utilisateur_id' => 1,
            'token' => '',
            'date_expiration' => '2024-12-31 23:59:59'
        ];

        // Vérification que l'exception est bien lancée pour token vide
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("L'identifiant de l'utilisateur, le token et la date d'expiration doivent être fournis.");

        // Appel de la méthode à tester
        $this->userBase->insertSession($sessionDataEmptyToken);

        // Vérification du cas où la date d'expiration est vide
        $sessionDataEmptyExpiration = [
            'utilisateur_id' => 1,
            'token' => 'abc123xyz',
            'date_expiration' => ''
        ];

        // Vérification que l'exception est bien lancée pour date d'expiration vide
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("L'identifiant de l'utilisateur, le token et la date d'expiration doivent être fournis.");

        // Appel de la méthode à tester
        $this->userBase->insertSession($sessionDataEmptyExpiration);

        // Configuration du mock pour simuler une erreur d'insertion (doublon)
        $sessionDataDuplicate = [
            'utilisateur_id' => 1,
            'token' => 'abc123xyz',
            'date_expiration' => '2024-12-31 23:59:59'
        ];

        // Simuler le comportement de PDO pour le doublon
        $stmtMockDuplicate = $this->createMock(PDOStatement::class);
        
        $stmtMockDuplicate->method('execute')
                          ->will($this->throwException(new PDOException("Duplicate entry", 23000)));

        // Préparation du mock PDO pour retourner notre mock de statement
        $this->dbMock->expects($this->once())
                     ->method('prepare')
                     ->willReturn($stmtMockDuplicate);

        // Vérification que l'exception est bien lancée lors d'une tentative d'insertion en doublon
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Erreur lors de l'insertion de la session : Le token existe déjà.");

        // Appel de la méthode à tester
        $this->userBase->insertSession($sessionDataDuplicate);

        // Configuration du mock pour simuler une erreur d'exécution générale
        $sessionDataExecutionFailure = [
            'utilisateur_id' => 2,
            'token' => 'xyz456abc',
            'date_expiration' => '2024-12-31 23:59:59'
        ];

        // Simuler le comportement de PDO pour une erreur d'exécution
        $stmtMockExecutionFailure = $this->createMock(PDOStatement::class);
        
        $stmtMockExecutionFailure->method('execute')
                                 ->will($this->throwException(new PDOException("Erreur lors de l'exécution", 500)));

        // Préparation du mock PDO pour retourner notre mock de statement
        $this->dbMock->expects($this->once())
                     ->method('prepare')
                     ->willReturn($stmtMockExecutionFailure);

        // Vérification que l'exception est bien lancée lors d'une erreur d'exécution
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Erreur lors de l'insertion de la session : Erreur lors de l'exécution");

         // Appel de la méthode à tester
         $this->userBase->insertSession($sessionDataExecutionFailure);
    }


    public function testInsertRecuperationMotDePasseSuccess() {
        // Données valides pour l'insertion de récupération de mot de passe
        $recuperationData = [
            'utilisateur_id' => 1,
            'token' => 'unique_token_123',
            'date_expiration' => '2024-12-31 23:59:59'
        ];

        // Configuration du mock pour simuler le comportement de PDO
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->once())
                 ->method('execute')
                 ->with([
                     ':utilisateur_id' => 1,
                     ':token' => 'unique_token_123',
                     ':date_expiration' => '2024-12-31 23:59:59'
                 ])
                 ->willReturn(true);

        // Préparation du mock PDO pour retourner notre mock de statement
        $this->dbMock->expects($this->once())
                     ->method('prepare')
                     ->willReturn($stmtMock);

        // Appel de la méthode à tester
        $result = $this->userBase->insertRecuperationMotDePasse($recuperationData);

        // Vérification que l'insertion a réussi
        $this->assertTrue($result);
    }

    public function testInsertRecuperationMotDePasseFailures() {
        // Vérification du cas où l'identifiant utilisateur est vide
        $recuperationDataEmptyUser = [
            'utilisateur_id' => '',
            'token' => 'unique_token_123',
            'date_expiration' => '2024-12-31 23:59:59'
        ];

        // Vérification que l'exception est bien lancée pour utilisateur_id vide
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("L'identifiant de l'utilisateur, le token et la date d'expiration doivent être fournis.");

        // Appel de la méthode à tester
        $this->userBase->insertRecuperationMotDePasse($recuperationDataEmptyUser);

        // Vérification du cas où le token est vide
        $recuperationDataEmptyToken = [
            'utilisateur_id' => 1,
            'token' => '',
            'date_expiration' => '2024-12-31 23:59:59'
        ];

        // Vérification que l'exception est bien lancée pour token vide
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("L'identifiant de l'utilisateur, le token et la date d'expiration doivent être fournis.");

        // Appel de la méthode à tester
        $this->userBase->insertRecuperationMotDePasse($recuperationDataEmptyToken);

        // Vérification du cas où la date d'expiration est vide
        $recuperationDataEmptyExpiration = [
            'utilisateur_id' => 1,
            'token' => 'unique_token_123',
            'date_expiration' => ''
        ];

        // Vérification que l'exception est bien lancée pour date d'expiration vide
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("L'identifiant de l'utilisateur, le token et la date d'expiration doivent être fournis.");

        // Appel de la méthode à tester
        $this->userBase->insertRecuperationMotDePasse($recuperationDataEmptyExpiration);

        // Configuration du mock pour simuler une erreur d'insertion (doublon)
        $recuperationDataDuplicate = [
            'utilisateur_id' => 1,
            'token' => 'unique_token_123',
            'date_expiration' => '2024-12-31 23:59:59'
        ];

        // Simuler le comportement de PDO pour le doublon
        $stmtMockDuplicate = $this->createMock(PDOStatement::class);
        
        $stmtMockDuplicate->method('execute')
                          ->will($this->throwException(new PDOException("Duplicate entry", 23000)));

        // Préparation du mock PDO pour retourner notre mock de statement
        $this->dbMock->expects($this->once())
                     ->method('prepare')
                     ->willReturn($stmtMockDuplicate);

        // Vérification que l'exception est bien lancée lors d'une tentative d'insertion en doublon
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Erreur lors de l'insertion de la récupération de mot de passe : Le token existe déjà.");

        // Appel de la méthode à tester
        $this->userBase->insertRecuperationMotDePasse($recuperationDataDuplicate);

         // Configuration du mock pour simuler une erreur d'exécution générale
         $recuperationDataExecutionFailure = [
             'utilisateur_id' => 2,
             'token' => 'xyz456abc',
             'date_expiration' => '2024-12-31 23:59:59'
         ];

         // Simuler le comportement de PDO pour une erreur d'exécution
         $stmtMockExecutionFailure = $this->createMock(PDOStatement::class);
         
         $stmtMockExecutionFailure->method('execute')
                                  ->will($this->throwException(new PDOException("Erreur lors de l'exécution", 500)));

         // Préparation du mock PDO pour retourner notre mock de statement
         $this->dbMock->expects($this->once())
                      ->method('prepare')
                      ->willReturn($stmtMockExecutionFailure);

         // Vérification que l'exception est bien lancée lors d'une erreur d'exécution
         $this->expectException(Exception::class);
         $this->expectExceptionMessage("Erreur lors de l'insertion de la récupération de mot de passe : Erreur lors de l'exécution");

         // Appel de la méthode à tester
         $this->userBase->insertRecuperationMotDePasse($recuperationDataExecutionFailure);
    }

    public function testInsertVerificationEmailSuccess() {
        // Données valides pour l'insertion de vérification d'email
        $verificationData = [
            'utilisateur_id' => 1,
            'token' => 'unique_verification_token',
            'date_expiration' => '2024-12-31 23:59:59'
        ];

        // Configuration du mock pour simuler le comportement de PDO
        $stmtMock = $this->createMock(PDOStatement::class);
        $stmtMock->expects($this->once())
                 ->method('execute')
                 ->with([
                     ':utilisateur_id' => 1,
                     ':token' => 'unique_verification_token',
                     ':date_expiration' => '2024-12-31 23:59:59'
                 ])
                 ->willReturn(true);

        // Préparation du mock PDO pour retourner notre mock de statement
        $this->dbMock->expects($this->once())
                     ->method('prepare')
                     ->willReturn($stmtMock);

        // Appel de la méthode à tester
        $result = $this->userBase->insertVerificationEmail($verificationData);

        // Vérification que l'insertion a réussi
        $this->assertTrue($result);
    }

    public function testInsertVerificationEmailFailures() {
        // Vérification du cas où l'identifiant utilisateur est vide
        $verificationDataEmptyUser = [
            'utilisateur_id' => '',
            'token' => 'unique_verification_token',
            'date_expiration' => '2024-12-31 23:59:59'
        ];

        // Vérification que l'exception est bien lancée pour utilisateur_id vide
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("L'identifiant de l'utilisateur, le token et la date d'expiration doivent être fournis.");

        // Appel de la méthode à tester
        $this->userBase->insertVerificationEmail($verificationDataEmptyUser);

        // Vérification du cas où le token est vide
        $verificationDataEmptyToken = [
            'utilisateur_id' => 1,
            'token' => '',
            'date_expiration' => '2024-12-31 23:59:59'
        ];

        // Vérification que l'exception est bien lancée pour token vide
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("L'identifiant de l'utilisateur, le token et la date d'expiration doivent être fournis.");

        // Appel de la méthode à tester
        $this->userBase->insertVerificationEmail($verificationDataEmptyToken);

        // Vérification du cas où la date d'expiration est vide
        $verificationDataEmptyExpiration = [
            'utilisateur_id' => 1,
            'token' => 'unique_verification_token',
            'date_expiration' => ''
        ];

        // Vérification que l'exception est bien lancée pour date d'expiration vide
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("L'identifiant de l'utilisateur, le token et la date d'expiration doivent être fournis.");

        // Appel de la méthode à tester
        $this->userBase->insertVerificationEmail($verificationDataEmptyExpiration);

        // Configuration du mock pour simuler une erreur d'insertion (doublon)
        $verificationDataDuplicate = [
            'utilisateur_id' => 1,
            'token' => 'unique_verification_token',
            'date_expiration' => '2024-12-31 23:59:59'
        ];

        // Simuler le comportement de PDO pour le doublon
        $stmtMockDuplicate = $this->createMock(PDOStatement::class);
        
        $stmtMockDuplicate->method('execute')
                          ->will($this->throwException(new PDOException("Duplicate entry", 23000)));

        // Préparation du mock PDO pour retourner notre mock de statement
        $this->dbMock->expects($this->once())
                     ->method('prepare')
                     ->willReturn($stmtMockDuplicate);

        // Vérification que l'exception est bien lancée lors d'une tentative d'insertion en doublon
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Erreur lors de l'insertion de la vérification d'email : Le token existe déjà.");

        // Appel de la méthode à tester
        $this->userBase->insertVerificationEmail($verificationDataDuplicate);

         // Configuration du mock pour simuler une erreur d'exécution générale
         $verificationDataExecutionFailure = [
             'utilisateur_id' => 2,
             'token' => 'xyz456abc',
             'date_expiration' => '2024-12-31 23:59:59'
         ];

         // Simuler le comportement de PDO pour une erreur d'exécution
         $stmtMockExecutionFailure = $this->createMock(PDOStatement::class);
         
         $stmtMockExecutionFailure->method('execute')
                                  ->will($this->throwException(new PDOException("Erreur lors de l'exécution", 500)));

         // Préparation du mock PDO pour retourner notre mock de statement
         $this->dbMock->expects($this->once())
                      ->method('prepare')
                      ->willReturn($stmtMockExecutionFailure);

         // Vérification que l'exception est bien lancée lors d'une erreur d'exécution
         $this->expectException(Exception::class);
         $this->expectExceptionMessage("Erreur lors de l'insertion de la vérification d'email : Erreur lors de l'exécution");

         // Appel de la méthode à tester
         $this->userBase->insertVerificationEmail($verificationDataExecutionFailure);
    }

    
}