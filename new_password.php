<?php   
require_once 'BDD/UserBase.php';
$base = new UserBase();
$bad_field = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    // Utilisation de $_POST['code'] pour le code de vérification

    //TODO: Faire le try catch correctement + Regarder comment on crée de nouvelles exceptions pour faciliter gestion

    if($base->insertNewPassword($_POST['email'], $_POST['code'], $_POST['password'])){
        header('Location: Login/main.php');
    }else{
        $bad_field = true;
    }
}

// Fonction pour générer le style du champ
function getFieldStyle($bad_field) {
    return $bad_field ? 'style="border: 2px solid red;"' : '';
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
    <link rel="stylesheet" href="Login/style.css">
</head>
<body>

    <div class="container" id="container">
        <div class="form-container reset-password-container">
            <form action="#" method="POST">
                <1 style="padding-top: 30px;">Code de vérification envoyé</h1>

                <p>Rentrez le code et votre nouveau mot de passe</p>


                <div class="infield" style="margin-top: 0px;">
                    <input type="text" placeholder="Email" name="email" required <?php echo getFieldStyle($bad_field); ?> />
                    <label></label>
                </div>

                <div class="infield" style="margin-top: 0px;">
                    <input type="text" placeholder="Code" name="code" required <?php echo getFieldStyle($bad_field); ?> />
                    <label></label>
                </div>
                    
                <div class="infield" style="margin-top: 0px;">
                    <input type="password" placeholder="NewPassword" name="password" required <?php echo getFieldStyle($bad_field); ?> />
                    <label></label>
                </div>

                <button type="submit" name="submit">Envoyer le lien</button>
                <a href="Login/main.php" class="forgot">Retour à la connexion</a>
                
            </form>
        </div>
    </div>

</body>
</html>
