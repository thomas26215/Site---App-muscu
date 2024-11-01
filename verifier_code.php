<?php
require_once 'UserBase.php';
$base = new UserBase();

$errorMessage = ""; // Variable pour stocker le message d'erreur

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    // Utilisation de $_POST['code'] pour le code de vérification

    //TODO: Faire le try catch correctement + Regarder comment on crée de nouvelles exceptions pour faciliter gestion

    try{
        $isVerified = $base->verifyAccountWithCode($_POST['code'], $_POST['email']);
    }catch(Exception $e){

    }
    

    
    
    if ($isVerified) {
        echo "coucou"; // Action si le compte est vérifié
        $base->confirmVerificationCode($_POST['email']);
    } else {
        $errorMessage = "Email ou code de vérification invalide. Réessayez !";
    }
}



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Un code de vérification vous a été envoyé</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container" id="container">
        <div class="form-container reset-password-container">
            <form action="#" method="POST">
                <h1 style="padding-top: 60px;">Code de vérification envoyé</h1>

                <!-- Message conditionnel -->
                <?php
                    if ($errorMessage) {
                        echo '<p style="color: red; font-weight: bold;">' . $errorMessage . '</p>';
                    } elseif (isset($_GET['error']) && $_GET['error'] == 'verification_required') {
                        echo '<p style="color: red; font-weight: bold;">Vous devez d\'abord vérifier votre adresse mail. Un code de vérification vous a été réenvoyé à l\'adresse mail suivante :</p>';
                    } else {
                        echo '<p>Entrez le code de vérification qui vous a été envoyé à l\'adresse mail suivante :</p>';
                    }
                ?>

                <div class="infield">
                    <input 
                     type="email" placeholder="Email" name="email" required />
                    <label></label>
                </div>
                <div class="infield" style="margin-top: 0px;">
                    <input type="text" placeholder="Code" name="code" required />
                    <label></label>
                </div>
                <a href="ask_new_code.php" class="forgot" type="askNewCode" value="askNewCode" style="margin-top: 0;margin-left: -50px;">Demander un nouveau code de connexion</a>
                <button type="submit" name="submit">Envoyer le lien</button>
                <a href="main.php" class="forgot">Retour à la connexion</a>
                
            </form>
        </div>
    </div>

</body>
</html>
