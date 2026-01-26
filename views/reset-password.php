<?php
include_once __DIR__ . '/../config/BD.php';
// Initialisation des messages
$success_message = "";
$error_message = "";
$token_error_message = "";
$tokenValid = false;

// 💡 Vérification du token via GET
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['email'], $_GET['token'])) {
    $email = trim($_GET['email']);
    $token = trim($_GET['token']);

    if (!empty($email) && !empty($token)) {
        try {
            // Vérification en base de données
            $stmt = $pdo->prepare("SELECT * FROM GUIDASSO WHERE MAIL = :email AND reset_token = :token");
            $stmt->execute([':email' => $email, ':token' => $token]);

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $expiration = new DateTime($user['token_expiration']);
                $now = new DateTime();

                if ($now > $expiration) {
                    // ça met l'émoji croix rouge tout seul héhé
                    $token_error_message = "❌ Le lien de réinitialisation a expiré.";
                } else {
                    $tokenValid = true; // Le token est valide
                }
            } else {
                $token_error_message = "❌ Lien de réinitialisation invalide.";
            }
        } catch (Exception $e) {
            $token_error_message = "⚠️ Erreur interne : " . $e->getMessage();
        }
    } else {
        $token_error_message = "❌ Lien invalide.";
    }
}

// 💡 Traitement du formulaire (changement de mot de passe)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'], $_POST['token'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $token = $_POST['token'];
    $mdp = trim($_POST['mdp']);
    $mdp2 = trim($_POST['confirm_mdp']);

    // Vérification du mot de passe
    if ($mdp !== $mdp2) {
        $error_message = "❌ Les mots de passe ne correspondent pas.";
    } elseif (strlen($mdp) < 8 || !preg_match('/[^a-zA-Z\d]/', $mdp)) {
        $error_message = "❌ Le mot de passe doit contenir au moins 8 caractères et un caractère spécial.";
    } else {
        try {
            // Vérifier si l'email et le token sont valides
            $stmt = $pdo->prepare("SELECT * FROM GUIDASSO WHERE MAIL = :email AND reset_token = :token");
            $stmt->execute([':email' => $email, ':token' => $token]);

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // Vérifier si le mot de passe est différent de l'ancien
                if (password_verify($mdp, $user['MOTDEPASSE'])) {
                    $error_message = "❌ Le nouveau mot de passe doit être différent de l'ancien.";
                } else {
                    // Mise à jour du mot de passe et suppression du token
                    $hashed_password = password_hash($mdp, PASSWORD_DEFAULT);
                    $stmtUpdate = $pdo->prepare(
                        "UPDATE GUIDASSO SET MOTDEPASSE = :password, reset_token = NULL, token_expiration = NULL WHERE MAIL = :email"
                    );
                    $stmtUpdate->execute([':password' => $hashed_password, ':email' => $email]);

                    $success_message = "✅ Mot de passe mis à jour avec succès.";
                    $tokenValid = false; // Désactiver le formulaire après mise à jour
                }
            } else {
                $token_error_message = "❌ Lien de réinitialisation invalide.";
            }
        } catch (Exception $e) {
            $token_error_message = "⚠️ Erreur interne : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Changement de mot de passe</title>
    <link rel="icon" href="/../public/img/logo_page.png" type="image/png">
    <link rel="stylesheet" href="/../public/css/style_pageconnexion.css">
    <link rel="stylesheet" href="/../public/css/responsive_home.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
</head>
<body class="reset-password">
    <h3>
        <div class="image-container">
            <img src="/../public/img\LogoRéseau1.png" alt="Logo Réseau">
            <img src="/../public/img\LogoInformation.png" alt="Logo Information">
            <img src="/../public/imgs\LogoOrientation1.png" alt="Logo Orientation">
            <img src="/../public/img\LogoAccompagnementG1.png" alt="Logo Accompagnement">
        </div>
        <br><br>
    </h3>

    <div id="email-oublie-container" style="width: 40%;">
        <h3 class="dashboard-title">Changer le mot de passe</h3>

        <!-- 📢 AFFICHAGE DES MESSAGES -->
        <?php if (!empty($success_message)): ?>
            <p style="color: green;"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>

        <?php if (!empty($token_error_message)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($token_error_message); ?></p>
        <?php endif; ?>

        <!-- 📌 Affichage du formulaire uniquement si le token est valide -->
        <?php if ($tokenValid || !empty($error_message)): ?>
            <form method="POST" action="">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div class="mdp-oublie-form-group">
                    <label for="mdp">Nouveau mot de passe :</label>
                    <input type="password" id="mdp" name="mdp" required>
                    <label class="show-password">
                        <input type="checkbox" onclick="togglePasswordVisibility('mdp')"> Afficher le mot de passe
                    </label>
                </div>

                <div class="mdp-oublie-form-group">
                    <label for="confirm_mdp">Confirmer le mot de passe :</label>
                    <input type="password" id="confirm_mdp" name="confirm_mdp" required>
                </div>

                <!-- Affichage des erreurs spécifiques aux mots de passe -->
                <?php if (!empty($error_message)): ?>
                    <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
                <?php endif; ?>

                <button type="submit">Changer le mot de passe</button>
            </form>
        <?php endif; ?>

        <p class="redirection-lien">
            <a href="/../views/pageconnexion.php">Retour vers la page de connexion</a>
        </p>
    </div>

    <section id="footer" class="footer">
        <div class="footer-content">
            <div class="footer-text">
                <p>Guid'Asso</p>
                <p>Patrice Mancino : 06 07 08 09 10</p>
                <p>Assistance client : vieasso86@guidasso86.fr</p>
            </div>
            <div class="footer-image">
                <img src="/../public/img/vienneA.png" alt="Logo">
            </div>
        </div>
    </section>

    <script>
        // Fonction pour afficher ou masquer le mot de passe
        function togglePasswordVisibility(id) {
            const input = document.getElementById(id);
            input.type = input.type === "password" ? "text" : "password";
        }

        // Gestion du footer
        document.addEventListener('DOMContentLoaded', function () {
            const footer = document.getElementById('footer');
            let lastScrollY = window.scrollY;

            window.addEventListener('scroll', function () {
                const scrollY = window.scrollY;
                const windowHeight = window.innerHeight;
                const documentHeight = document.documentElement.scrollHeight;

                if (scrollY === 0 || scrollY + windowHeight >= documentHeight - 10) {
                    footer.classList.remove('hidden-footer');
                    footer.classList.add('visible-footer');
                } else if (scrollY > lastScrollY) {
                    footer.classList.remove('visible-footer');
                    footer.classList.add('hidden-footer');
                }

                lastScrollY = scrollY;
            });
        });
    </script>
</body>
</html>
