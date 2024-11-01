<?php
require_once 'UserBase.php';
require_once 'Utilitaire/mail.php';
$base = new UserBase();

$errorMessage = ""; // Variable pour stocker le message d'erreur
$existEmail = true;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
/*if($base->checkIfMailExist()){
        echo "string";
    }*/

    try{
        if(($base->checkIfMailExist($_POST['email']))){
            header("Location: verifier_code.php");
            $base->generateAndStoreVerificationCode($_POST['email']);
            sendCodeMail($_POST['email'], "Personne");
            $existEmail = true;
        }else{
            $existEmail = false;
        }
        
    }catch(Exception $e){
        echo $e;
    }

    


}



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ask new code</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container" id="container">
        <div class="form-container reset-password-container">
            <form action="#" method="POST">
                <h1 style="padding-top: 60px;">Demander un nouveau code de vérification</h1>

                <?php

                if($existEmail){
                    echo "<p>Indiquez votre adresse mail pour recevoir un nouveau code de vérification</p>";
                }else{
                    echo "<p style='color:red; font-weight: bold';>L'adresse mail que vous venez de rentrer n'est pas connue dans notre base de données. Réessayez avec une autre adresse mail :</p>";
                }

                ?>
                <div class="infield">
                    <input type="email" placeholder="Email" name="email" required style="margin-top: -10px;"/>
                    <label></label>
                </div>
                
                <button type="submit" name="submit">Envoyer un nouveau code</button>
                <a href="main.php" class="forgot">Retour à la connexion</a>
                
            </form>
        </div>
    </div>

</body>
</html>
