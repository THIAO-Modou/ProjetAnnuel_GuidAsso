<?php
include_once __DIR__ . '/../config/BD.php';
include_once __DIR__ . '/../config/session.php';


$Retourmsg = ""; // Message de retour
$emailValide = false; // Indicateur d'email valide

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;

    if (!empty($email)) {
        try {
            // Vérification de la connexion
            if (!isset($pdo) || $pdo === null) {
                throw new Exception("La connexion à la base de données n'est pas initialisée.");
            }

            // Requête pour vérifier si l'email existe
            $sql = "SELECT * FROM GUIDASSO WHERE Mail = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $emailValide = true;

                // Générer un token unique et définir une expiration
                $token = bin2hex(random_bytes(32)); // Token sécurisé
                $expiration = new DateTime('+1 hour'); // 1 heure à partir de maintenant
                $expirationFormatted = $expiration->format('Y-m-d H:i:s');

                  // Mettre à jour la base de données
                $sqlUpdate = "UPDATE GUIDASSO SET reset_token = :token, token_expiration = :expiration WHERE Mail = :email";
                $stmtUpdate = $pdo->prepare($sqlUpdate);
                $stmtUpdate->execute([
                    ':token' => $token,
                    ':expiration' => $expirationFormatted,
                    ':email' => $email,
                ]);

                $Retourmsg = "Email trouvé. Un lien de réinitialisation sera envoyé.";
            } else {
                $Retourmsg = "Cet email n'existe pas dans notre base de données.";
            }
        } catch (PDOException $e) {
            $Retourmsg = "Erreur interne avec la base de données : " . $e->getMessage();
        } catch (Exception $e) {
            $Retourmsg = "Erreur interne : " . $e->getMessage();
        }
    } else {
        $Retourmsg = "Veuillez fournir un email valide.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>

    <link rel="icon" href="/../public/img/logo_page.png" type="image/png">

    <link rel="stylesheet" href="/../public/css/style_pageconnexion.css">
    <link rel="stylesheet" href="/../public/css/responsive_home.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script src="https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js"></script>
</head>
<body class="mdp-oublie">
    
    <h3>
        <div class="image-container">
            <img src="/../public/img\LogoRéseau1.png">
            <img src="/../public/img\LogoInformation.png">
            <img src="/../public/img\LogoOrientation1.png">
            <img src="/../public/img\LogoAccompagnementG1.png">
        </div>
        <br><br>
    </h3>

    <div id="main-content">

    <div id="email-oublie-container">
        <h3 class="dashboard-title">Mot de passe oublié</h3>
        <form id="forgotPasswordForm" method="POST" action="">
            <p>Entrez votre adresse email pour recevoir un lien de réinitialisation de mot de passe</p>
            <div class="mdp-oublie-form-group">
                <label for="email">Adresse email :</label>
                <input type="email" id="email" name="email" placeholder="exemple@email.com" required>
            </div>
             <!-- Message de retour PHP -->
            <p id="phpMessage" style="color: <?php echo $emailValide ? 'green' : 'red'; ?>;">
                <?php echo htmlspecialchars($Retourmsg); ?>
            </p>
            <button type="submit">Vérifier</button>
             <!-- Lien pour mot de passe oublié -->
            <p class="redirection-lien">
                <a href="pageconnexion.php">Retour vers la page de connexion</a>
            </p>

        </form>
    </div>
    </div>

    <section id="footer" class="footer">
        <div class="footer-content">
            <div class="footer-text">
                <p>Guid'Asso</p>
                <p>Patrice Mancino : 06 07 08 09 10</p>
                <p>Assistance client : vieasso86@guidasso86.fr</p>
            </div>  
            <div class="footer-image">
                <a href="/../views/questionnaire.php" class="btn-home" title="Retour à l'accueil">
                <img src="/../public/img/home.png" alt="Accueil" />
            </a> 
                <img src="/../images/CRAIG.png" alt="Logo">
            </div>
        </div>
    </section>

    <script>
        // Initialiser EmailJS
        emailjs.init("Tm-DDESe2F5bDCKvs");

        const phpValidation = "<?php echo $emailValide ? 'true' : 'false'; ?>"; // Validation PHP
        const email = "<?php echo addslashes(isset($email) ? $email : ''); ?>"; // Email validé

        if (phpValidation === "true") {
            // mail + email
            // Penser à changer le lien lors de l'instalation d'une base
            const resetLink = `https://guide-asso-m2.geniephy.net/views/reset-password.php?email=${encodeURIComponent(email)}&token=${encodeURIComponent("<?php echo $token; ?>")}`;
            const emailParams = {
                to_email: email,
                subject: "Guid'Asso - Réinitialiser votre mot de passe.",
                from_name: "Guid'Asso",
                message: `Vous avez demandé une réinitialisation de votre mot de passe. Cliquez sur le lien suivant pour le réinitialiser : ${resetLink}`,
            };
            // Envoie du mail via EmailJS
            emailjs.send("service_rln42mj", "template_i0dp3s5", emailParams)
            .then(() => {
                statusMessage.textContent = "Un email de réinitialisation a été envoyé à votre adresse.";
                statusMessage.style.color = "green";
            })
            .catch((error) => {
                console.error("Erreur lors de l'envoi de l'email :", error);
                statusMessage.textContent = "Erreur lors de l'envoi de l'email. Veuillez réessayer.";
                statusMessage.style.color = "red";
            });
        }
    </script>
    <script>

        // Script pour gerer le footer
        document.addEventListener('DOMContentLoaded', function () {
            const footer = document.getElementById('footer');
            let lastScrollY = window.scrollY;

            window.addEventListener('scroll', function () {
                const scrollY = window.scrollY;
                const windowHeight = window.innerHeight;
                const documentHeight = document.documentElement.scrollHeight;

                // Afficher le footer si on est en bas ou en haut de la page
                if (scrollY === 0 || scrollY + windowHeight >= documentHeight - 10) {
                    footer.classList.remove('hidden-footer');
                    footer.classList.add('visible-footer');
                } 
                // Masquer ou afficher selon le défilement
                else if (scrollY > lastScrollY) {
                    footer.classList.remove('visible-footer');
                    footer.classList.add('hidden-footer');
                } else {
                    footer.classList.remove('hidden-footer');
                    footer.classList.add('visible-footer');
                }

                lastScrollY = scrollY;
            });
        });

    </script>
</body>
</html>
