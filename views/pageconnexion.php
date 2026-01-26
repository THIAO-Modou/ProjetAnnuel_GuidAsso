<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once __DIR__ . '/../config/BD.php';
include_once __DIR__ . '/../config/session.php';



$Retourmsg = ""; // messages d'erreurs
$Avertirmsg =""; //Message pour avertir
// Initialisation
$_SESSION['ECHECS_CONNEXION'] = $_SESSION['ECHECS_CONNEXION'] ?? 0;
$_SESSION['ECHECS_ALERTE'] = $_SESSION['ECHECS_ALERTE'] ?? false;
$_SESSION['EMAIL_TENTE'] = $_SESSION['EMAIL_TENTE'] ?? '';
$_SESSION['LISTE_ADMIN_EMAILS'] = $_SESSION['LISTE_ADMIN_EMAILS'] ?? [];


//-----------------------------------------------------------------------//
//-------------------- PHP GESTION CONNEXION BDD ------------------------//
//-----------------------------------------------------------------------//

//envoie info du formulaire
if (isset($_POST['email']) && isset($_POST['password'])) {
    $Mdp = $_POST['password'];  
    $Email = htmlentities($_POST['email'], ENT_QUOTES);
    $_SESSION['EMAIL_TENTE'] = $Email;  

    // Verifi si email existe dans bdd
    $sql = "SELECT * FROM GUIDASSO WHERE Mail = :email";
    $result = $pdo->prepare($sql);
    $result->bindValue(':email', $Email, PDO::PARAM_STR);

    try {
        $result->execute();
        $Existe = $result->rowCount(); //encore pour récupérer ligne 

        if ($Existe > 0) {
            $row = $result->fetch(PDO::FETCH_ASSOC); // Récupère les données utilisateur
            
            // Vérification du mot de passe hashé
            if (password_verify($Mdp, $row['MOTDEPASSE'])) {
                // on stock les infos utilistaeurs
                $_SESSION['MAIL'] = $row['MAIL'];
                $_SESSION['IDFONCTION'] = $row['IDFONCTION'];

                // Redirection en fonction rôle de l'admin
                if ($_SESSION['IDFONCTION'] == 1 || $_SESSION['IDFONCTION'] == 4) {
                    $_SESSION['ECHECS_CONNEXION'] = 0;
                    $_SESSION['ECHECS_ALERTE'] = false;
                    header('Location: /../views/questionnaire.php');
                    exit();
                } elseif ($_SESSION['IDFONCTION'] == 2 || $_SESSION['IDFONCTION'] == 3) {
                    $_SESSION['ECHECS_CONNEXION'] = 0;
                    $_SESSION['ECHECS_ALERTE'] = false;
                    header('Location: /../views/pageadmin.php');
                    exit();
                }
            } else {
                // Si mdp pas bon
                $Retourmsg = "Mot de passe incorrect.";
                $_SESSION['ECHECS_CONNEXION']++;
            }
        } else {
            // Si email existe pas
            $Retourmsg = "Adresse email incorrecte.";
            $_SESSION['ECHECS_CONNEXION']++;
        }
        if($_SESSION['ECHECS_CONNEXION']>3){
            $Avertirmsg = "Il vous reste ".(5-$_SESSION['ECHECS_CONNEXION'])."tentative";
        }
    } catch (PDOException $e) {
        // En cas d'erreur avec la base de données
        error_log("Erreur lors de l'exécution de la requête : " . $e->getMessage());
        $Retourmsg = "Erreur serveur. Veuillez réessayer plus tard.";
    }

    //-------------------------------------------------------------------------
    //------------- ENVOI D'ALERTE APRÈS 5 ÉCHECS DE CONNEXION (via EmailJS en JS)
    //-------------------------------------------------------------------------

    if ($_SESSION['ECHECS_CONNEXION'] >= 5) {
        try {
            // Récupération de tous les mails admin (profil admin)
            $stmtAdmins = $pdo->query("SELECT MAIL FROM GUIDASSO WHERE IDFONCTION = 2");
            $_SESSION['LISTE_ADMIN_EMAILS'] = $stmtAdmins->fetchAll(PDO::FETCH_COLUMN);
            $_SESSION['EMAIL_TENTE'] = $Email;
            $_SESSION['ALERTE_CONNEXION'] = true;

            // Réinitialisation du compteur
            $_SESSION['ECHECS_CONNEXION'] = 0;

        } catch (PDOException $err) {
            error_log("Erreur récupération admins : " . $err->getMessage());
        }
    }

}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>

    <link rel="icon" href="/../public/img/logo_page.png" type="image/png">
    
    <link rel="stylesheet" href="/../public/css/style_pageconnexion.css">
    <link rel="stylesheet" href="/../public/css/responsive_home.css">
    
    <!--Pour la responsivité -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <script src="/../public/js/connexion.js"></script>
 

    <!----------------------------------------------------------------------->
    <!------------------------------SCRIPT JS ------------------------------->
    <script>
        // fonction qui affiche ou non le mdp (traduit en texte le mdp)
        function togglePasswordVisibility(inputId) {
            const passwordField = document.getElementById(inputId);
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
    <!-- Importe la bibliothèque EmailJS depuis un CDN -->
    <script src="https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        emailjs.init("Tm-DDESe2F5bDCKvs"); // USER ID personnel de EmailJS

        // Récupération des variables PHP transmises vers JavaScript
        const alerte = "<?php echo $_SESSION['ALERTE_CONNEXION'] ?? false; ?>";
        const tentativeEmail = "<?php echo addslashes($_SESSION['EMAIL_TENTE'] ?? ''); ?>";
        const adminEmails = <?php echo json_encode($_SESSION['LISTE_ADMIN_EMAILS'] ?? []); ?>;

        if (alerte === "1" || alerte === "true") {
            // Pour chaque administrateur concerné...
            adminEmails.forEach(function (admin) {
                const params = {
                    to_email: admin,
                    from_name: "Système Guid'Asso",
                    subject: "Guid'Asso - 5 tentatives de connexion échouées",
                    message: `Un utilisateur a échoué 5 fois à se connecter avec l'adresse : ${tentativeEmail}\nIP : <?php echo $_SERVER['REMOTE_ADDR']; ?>\nHeure : <?php echo date('Y-m-d H:i:s'); ?>`
                };

                // Vérifie si l'alerte n’a pas déjà été envoyée à cet admin (pour éviter les doublons)
                if (!sessionStorage.getItem("alerte_envoyee_" + admin)) {
                    // Envoie l’email via EmailJS
                    emailjs.send("service_rln42mj", "template_i0dp3s5", params)
                    .then(() => {
                        console.log(` Alerte envoyée à ${admin}`);
                        // Enregistre localement que l’alerte a été envoyée (dans sessionStorage)
                        sessionStorage.setItem("alerte_envoyee_" + admin, "true");
                    })
                    .catch(err => console.error(" Erreur EmailJS : ", err));
                }

            });
            // Réinitialise la variable d’alerte côté serveur (nettoyage de session)
            fetch('/controllers/admin/reset_alerte_connexion.php')
            .then(res => res.json())
            .then(data => console.log(" Session d’alerte réinitialisée :", data))
            .catch(err => console.error(" Échec réinitialisation :", err));

        }
    });
    </script>



    <!------------------------------------------------------------------->
    <!---------------------------PARTIE HTML----------------------------->
    <!------------------------------------------------------------------->
</head>
<body class="pageconnexion">
    <h3>
        <div class="image-container">
            <img src="/../public/img/LogoRéseau1.png">
            <img src="/../public/img/LogoInformation.png">
            <img src="/../public/img/LogoOrientation1.png">
            <img src="/../public/img/LogoAccompagnementG1.png">
        </div>
        <br><br>
    </h3>

    <div id="main-content">

      <? 
    //  Récupérer la structure de la table
    // $sql = "DESCRIBE CONTACT";
    // $stmt = $pdo->query($sql);

    // //  Afficher les colonnes
    // echo "<table border='1'>";
    // echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";

    // while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    //     echo "<tr>";
    //     echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
    //     echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
    //     echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
    //     echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
    //     echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
    //     echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
    //     echo "</tr>";
    // }

    // echo "</table>";
    ?> 


    <div id="connexion-container" class="connexion-section">
        <h3 class="dashboard-title">Connexion</h3>
        <div class="content">
            <form action="/../views/pageconnexion.php" method="POST">
                <div class="user-details1">
                    <div class="connexion-form-group">
                        <label for="mail">Adresse mail :</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="connexion-form-group">
                        <label for="motdepasse">Mot de passe :</label>
                        <input type="password" id="password" name="password" required>
                        <label class="show-password"> <!-- Afficher mdp si on coche case -->
                        <input type="checkbox" onclick="togglePasswordVisibility('password')"> Afficher le mot de passe
                        </label>
                    </div>
                </div>
                 <!-- Lien pour mot de passe oublié -->
                 <p class="forgot-password">
                    <a href="/../views/mdp-oublie.php">Mot de passe oublié ?</a>
                </p>
                <!-- Affichage du message d'erreur selon le cas (VOIR POUR AMELIORER CSS)-->
                <?php 
                if ($Retourmsg != "") {
                    echo '<div class="alert-box">' . htmlspecialchars($Retourmsg) . '</br></div>';
                }
                if ($Avertirmsg != "") {
                    echo '<div class="alert-box">' . htmlspecialchars($Avertirmsg) . '</br></div>';
                }
                ?>
                <button type="submit">Se connecter</button>
                </div>
            </form>
        </div>
    </div>
    </div>

   

    <!-- Bandeau de footer -->
    <section id="footer" class="footer">
        <div class="footer-content">
            <div class="footer-text">
                <p>Guid'Asso</p>
                <p>Patrice Mancino : 06 07 08 09 10</p>
                <p>Assistance client : vieasso86@guidasso86.fr</p>
            </div>  
            <div class="footer-image">
                <a href="/../views/pageconnexion.php" class="btn-home" title="Retour à l'accueil" style="margin-bottom: 20px;">
                    <img src="/../public/img/home.png" alt="Accueil" />
                </a>  
                <img class="logo_CRAIG" src="/../public/img/CRAIG.png" alt="Logo">
            </div>
        </div>
    </section>

</body>
</html>
