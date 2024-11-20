<?php 
require_once 'BDD/UserBase.php';

$base = new UserBase();

// Afficher toutes les erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])){
    if($base->isEmailVerified($_POST['email'])){
        echo $_POST['email'];
        $base->askNewPassword($_POST['email']);
        header('Location: new_password.php');
    }
}



?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="Login/style.css">
</head>
<body>

    <div class="container" id="container">
        <div class="form-container reset-password-container">
            <form action="#" method="POST">
                <h1 style="padding-top: 60px;">Réinitialiser le mot de passe</h1>
                <span>Entrez votre adresse e-mail pour recevoir un lien de réinitialisation.</span>
                <div class="infield">
                    <input type="email" placeholder="Email" name="email" required />
                    <label></label>
                </div>
                <button type="submit" name="submit">Demander un nouveau mot de passe</button>
                <a href="Login/main.php" class="forgot">Retour à la connexion</a>
            </form>
        </div>
    </div>

    <!-- JS code -->
    <script>
        // Vous pouvez ajouter des scripts ici si nécessaire
    </script>

</body>
</html>
