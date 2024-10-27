<?php

require_once 'UserBase.php';
$base = new UserBase();

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    if (isset($_POST["submit"])){
        // Utiliser $_POST['code'] au lieu de $_POST['nom']
        if ($base->verifyAccountWithCode($_POST['code'], $_POST['email']) == true) {
            $base->confirmVerificationCode($_POST['email']);
            header("Location: main.php");
            exit();
        }
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
                <span>Entrez le code de vérification qui vous a été envoyé à l'adresse mail suivante : </span>
                <div class="infield">
                    <input type="email" placeholder="Email" name="email" required />
                    <label></label>
                </div>
                <div class="infield" style="margin-top: 0px;">
                    <input type="text" placeholder="Code" name="code" required />
                    <label></label>
                </div>
                <button type="submit" name="submit">Envoyer le lien</button>
                <a href="main.php" class="forgot">Retour à la connexion</a>
            </form>
        </div>
    </div>

    <!-- JS code -->
    <script>
        // Vous pouvez ajouter des scripts ici si nécessaire
    </script>

</body>
</html>
