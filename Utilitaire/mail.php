<?php

require 'vendor/autoload.php'; // Inclure l'autoloader de Composer pour PHPMailer
require_once 'UserBase.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($subject, $body, $AltBody, $email, $prenom){
    $mail = new PHPMailer();
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'venouilthomas123456@gmail.com'; // Votre adresse email
        $mail->Password = 'oyux ckfj pyqu xfpd'; // Votre mot de passe
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Destinataires
        $mail->setFrom('venouilthomas123456@gmail.com', 'PlanniSport');
        $mail->addAddress($email, $prenom);

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $AltBody;

        return $mail->send();
    } catch (Exception $e) {
        echo "Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}";
        return false;
    }
}

function sendCodeMail($email, $prenom){
    // Obtenez le code de vérification de la base de données
    $base = new UserBase();
    $userRecord = $base->getRecordsByConditions('utilisateur', ['email' => $email]);
    $codeVerification = $userRecord[0]['code_verification'] ?? 'Code non disponible';

    // Préparation du contenu de l'email
    $subject = "Bienvenue";
    $body = "<h1>Coucou !</h1><p>Tu t'es créé un compte avec succès.</p><br><br>Voici ton code de vérification : " . $codeVerification;
    $altBody = "Coucou ! Tu t'es créé un compte avec succès. Voici ton code de vérification : " . $codeVerification;

    // Appel de la fonction sendMail
    return sendMail($subject, $body, $altBody, $email, $prenom);
}

?>
