<?php
require_once 'UserBase.php';
require 'vendor/autoload.php'; // Inclure l'autoloader de Composer pour PHPMailer
require_once 'Utilitaire/mail.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$personne = new UserBase();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        // Filtrer les données et empêcher les failles XSS
        $nom = htmlspecialchars($_POST['nom'] ?? '');
        $prenom = htmlspecialchars($_POST['prenom'] ?? '');
        $age = filter_var($_POST['age'] ?? '', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 120]]);
        $genre = htmlspecialchars($_POST['genre'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';
        $dateNaissance = htmlspecialchars($_POST['dateNaissance'] ?? '');


        if ($_POST['submit'] == "sign up") {
            // Validation des données du formulaire
            if ($nom && $prenom && $age && $genre && $email && $password && $confirmPassword && $dateNaissance) {
                if ($password !== $confirmPassword) {
                    echo "Les mots de passe ne correspondent pas.";
                } else {
                    // Hachage du mot de passe
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // Préparation des données pour l'insertion
                    $userData = [
                        'nom' => $nom,
                        'prenom' => $prenom,
                        'age' => $age,
                        'genre' => $genre,
                        'email' => $email,
                        'mot_de_passe' => $hashedPassword,
                        'date_naissance' => $dateNaissance
                    ];

                    try {
                        // Vérification que l'email n'existe pas déjà
                        if ($personne->getUserByEmail($email)) {
                            echo "L'email est déjà utilisé.";
                        } else {
                            // Tentative d'insertion de l'utilisateur
                            if ($personne->insertUser($userData)) {
                                echo "Inscription réussie !";

                                $personne->generateAndStoreVerificationCode($email);

                                sendCodeMail($email, $prenom);

                                
                            } else {
                                echo "Erreur lors de l'inscription.";
                            }
                        }
                    } catch (Exception $e) {
                        echo "Erreur lors de l'inscription.";
                    }
                }
            } else {
                echo "Tous les champs sont requis.";
            }
        } elseif ($_POST['submit'] == "sign in") {
            if ($email && $password) {
                try {
                    // Récupération de l'utilisateur par email
                    $user = $personne->getUserByEmail($email);

                    if ($user && password_verify($password, $user['mot_de_passe']) && $personne->isEmailVerified($email) == true) {
                        session_start();
                        $_SESSION['user_id'] = $user['id'];
                        echo "Connexion réussie !";
                        header("Location: dashboard.php");
                        exit();
                    } else {
                        echo "Email ou mot de passe incorrect.";

                    }
                    if($personne->isEmailVerified($email) == false && password_verify($password, $user['mot_de_passe'])){
                        echo "Code de vérification non validé";
                        header("Location: verifier_code.php?error=verification_required");
                    }
                } catch (Exception $e) {
                    echo "Erreur lors de la connexion.";
                }
            } else {
                echo "Email et mot de passe sont requis.";
            }
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
    <title>Connexion || Inscription</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" id="signupForm" method="POST">
                <h1>Créer un compte</h1>
                <div class="step-indicator">
                    <span class="step active"></span>
                    <span class="step"></span>
                    <span class="step"></span>
                </div>
                <div class="step-content" id="step1">
                    <div class="infield">
                        <input type="text" placeholder="Nom" name="nom" required/>
                    </div>
                    <div class="infield">
                        <input type="text" placeholder="Prénom" name="prenom" required/>
                    </div>
                    <div class="infield">
                        <input type="number" placeholder="Âge" name="age" min="1" max="120" required/>
                    </div>
                    <button type="button" onclick="nextStep(1)">Suivant</button>
                </div>
                <div class="step-content" id="step2" style="display:none;">
                    <div class="infield">
                        <select name="genre" required>
                            <option value="">Sélectionnez le genre</option>
                            <option value="homme">Homme</option>
                            <option value="femme">Femme</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div class="infield">
                        <input type="date" placeholder="Date de naissance" name="dateNaissance" required/>
                    </div>
                    <button type="button" onclick="prevStep(2)">Précédent</button>
                    <button type="button" onclick="nextStep(2)">Suivant</button>
                </div>
                <div class="step-content" id="step3" style="display:none;">
                    <div class="infield">
                        <input type="email" placeholder="Email" name="email" required/>
                    </div>
                    <div class="infield">
                        <input type="password" placeholder="Mot de passe" name="password" required/>
                    </div>
                    <div class="infield">
                        <input type="password" placeholder="Confirmer le mot de passe" name="confirmPassword" required/>
                    </div>
                    <button type="button" onclick="prevStep(3)">Précédent</button>
                    <button type="submit" name="submit" value="sign up">S'inscrire</button>
                </div>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <h1>Se connecter</h1>
                <div class="social-container">
                    <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
                    <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <span>Ou utiliser votre email pour vous connecter</span>
                <div class="infield">
                    <input type="email" placeholder="Email" name="email"/>
                </div>
                <div class="infield">
                    <input type="password" placeholder="Mot de passe" name="password"/>
                </div>
                <a href="forget_password.php" class="forgot">Mot de passe oublié ?</a>
                <button type="submit" name="submit" value="sign in">Se connecter</button>
            </form>
        </div>
        <div class="overlay-container" id="overlayCon">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Bon retour !</h1>
                    <p>Pour vous connecter, rentrez vos identifiants</p>
                    <button>Se connecter</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Bienvenue !</h1>
                    <p>Entrez vos informations personnelles et venez vous entraîner !</p>
                    <button>S'enregistrer</button>
                </div>
            </div>
            <button id="overlayBtn"></button>
        </div>
    </div>

    <script>
        const container = document.getElementById('container');
        const overlayBtn = document.getElementById('overlayBtn');
        const signupForm = document.getElementById('signupForm');

        overlayBtn.addEventListener('click', () => {
            container.classList.toggle('right-panel-active');
            overlayBtn.classList.remove('btnScaled');
            window.requestAnimationFrame(() => {
                overlayBtn.classList.add('btnScaled');
            });
        });

        function validateStep(stepNumber) {
            const stepElement = document.getElementById('step' + stepNumber);
            const inputs = stepElement.querySelectorAll('input, select');
            for (let input of inputs) {
                if (input.hasAttribute('required') && !input.value) {
                    return false;
                }
            }
            return true;
        }

        function updateNextButton(stepNumber) {
            const nextButton = document.querySelector(`#step${stepNumber} button[onclick^="nextStep"]`);
            if (nextButton) {
                nextButton.disabled = !validateStep(stepNumber);
            }
        }

        function nextStep(currentStep) {
            if (validateStep(currentStep)) {
                document.getElementById('step' + currentStep).style.display = 'none';
                let nextStepElement = document.getElementById('step' + (currentStep + 1));
                nextStepElement.style.display = 'block';
                nextStepElement.style.animation = 'none';
                nextStepElement.offsetHeight; // Déclenche un reflow
                nextStepElement.style.animation = 'fadeInStep 0.5s ease-out';
                document.querySelectorAll('.step-indicator .step')[currentStep].classList.add('active');
                updateNextButton(currentStep + 1);
            }
        }

        function prevStep(currentStep) {
            document.getElementById('step' + currentStep).style.display = 'none';
            let prevStepElement = document.getElementById('step' + (currentStep - 1));
            prevStepElement.style.display = 'block';
            prevStepElement.style.animation = 'none';
            prevStepElement.offsetHeight; // Déclenche un reflow
            prevStepElement.style.animation = 'fadeInStep 0.5s ease-out';
            document.querySelectorAll('.step-indicator .step')[currentStep - 1].classList.remove('active');
            updateNextButton(currentStep - 1);
        }

        // Ajouter des écouteurs d'événements pour chaque étape
        for (let i = 1; i <= 3; i++) {
            const stepElement = document.getElementById('step' + i);
            const inputs = stepElement.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.addEventListener('input', () => updateNextButton(i));
            });
        }

        // Initialiser l'état du bouton "Suivant" pour la première étape
        updateNextButton(1);
    </script>
</body>
</html>