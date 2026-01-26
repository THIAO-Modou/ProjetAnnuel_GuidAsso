<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start(); // ✅ Évite les erreurs de sortie avant la redirection

include_once __DIR__ . '/../../config/BD.php';
include_once __DIR__ . '/../../config/session.php';

$mailGuidAsso = htmlspecialchars($_SESSION['MAIL'] ?? '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $missingFields = [];
        $adresse_utilisateur = $_SESSION['MAIL'] ?? 'inconnu';

        // 🔹 Vérification et nettoyage des champs
        $nom = isset($_POST['nom']) ? htmlspecialchars(trim($_POST['nom'])) : '';
        $prenom = isset($_POST['prenom']) ? htmlspecialchars(trim($_POST['prenom'])) : '';
        $mail = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
        $role = isset($_POST['role']) ? intval($_POST['role']) : 0;
        $mdp = isset($_POST['mdp']) ? trim($_POST['mdp']) : '';
        $mdp2 = isset($_POST['confirm_mdp']) ? trim($_POST['confirm_mdp']) : '';
        $classification = isset($_POST['classification']) ? htmlspecialchars(trim($_POST['classification'])) : '';

        if (empty($nom) || empty($prenom) || empty($mail) || empty($role) || empty($mdp) || empty($mdp2) || empty($classification)) {
            throw new Exception("Tous les champs sont obligatoires.");
        }

        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("L'adresse e-mail n'est pas valide.");
        }

        if ($mdp !== $mdp2) {
            throw new Exception("Les mots de passe ne correspondent pas.");
        }

        if (strlen($mdp) < 8 || !preg_match('/[^a-zA-Z\d]/', $mdp)) {
            throw new Exception("Le mot de passe doit comporter au moins 8 caractères et inclure un caractère spécial.");
        }

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM GUIDASSO WHERE MAIL = :mail");
        $stmt->bindParam(':mail', $mail);
        $stmt->execute();

        if ($stmt->fetchColumn() > 0) {
            throw new Exception("L'adresse e-mail est déjà utilisée.");
        }

        $hashed_password = password_hash($mdp, PASSWORD_DEFAULT);

        // 🔹 Génération du token unique
        $token = bin2hex(random_bytes(32));
        $expiration = new DateTime('+1 hour');
        $expirationFormatted = $expiration->format('Y-m-d H:i:s');

        // 🔹 Stockage du token en base
        $sqlUpdate = "UPDATE GUIDASSO SET active_token = :token, token_expiration = :expiration WHERE MAIL = :email";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([
            ':token' => $token,
            ':expiration' => $expirationFormatted,
            ':email' => $mail,
        ]);

        // 🔹 Insertion de l'utilisateur en base
        $stmt = $pdo->prepare("INSERT INTO GUIDASSO (NOMPERSONNE, PRENOMPERSONNE, MAIL, MOTDEPASSE, IDFONCTION, CLASSIFICATION) 
                               VALUES (:nom, :prenom, :mail, :password, :role, :classification)");
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':mail' => $mail,
            ':password' => $hashed_password,
            ':role' => $role,
            ':classification' => $classification,
        ]);

        // ✅ Succès
        $_SESSION['success_message'] = "Utilisateur ajouté avec succès !";
        $_SESSION['emailValide'] = true;
        $_SESSION['token'] = $token;
        $_SESSION['email'] = $mail;
        $_SESSION['nom'] = $nom;

        session_write_close(); // ✅ Ferme la session proprement pour éviter les conflits
        header("Location: /../../views/pageadmin.php");
        exit();

    } catch (Exception $e) {
        error_log("❌ ERREUR : " . $e->getMessage());
        $_SESSION['error_message_inscription'] = $e->getMessage();
        header("Location: /../../views/pageadmin.php"); // ✅ Redirection directe
        exit();
    }
}
?>

<!-- 🔹 Partie JavaScript pour EmailJS -->
<script src="https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ Le script EmailJS s'exécute bien après chargement !");
    emailjs.init("Tm-DDESe2F5bDCKvs");

    const phpValidation = "<?php echo $_SESSION['emailValide'] ? 'true' : 'false'; ?>";
    const email = "<?php echo addslashes($_SESSION['email'] ?? ''); ?>";
    const token = "<?php echo addslashes($_SESSION['token'] ?? ''); ?>";

    // 🔹 Réinitialiser emailEnvoye si l'email est différent de celui déjà enregistré
    const dernierEmailEnvoye = sessionStorage.getItem("dernierEmailEnvoye") || "";
    
    if (phpValidation === "true" && email && email !== dernierEmailEnvoye) {
        sessionStorage.setItem("dernierEmailEnvoye", email); // ✅ Enregistre le nouvel email
        console.log("✅ Nouvel utilisateur détecté, envoi autorisé !");
        //Penser a changer le lien lors de l'instalation d'une bese
        const activationLink = `https://guide-asso-m2.geniephy.net/views/pageconnexion.php?email=${encodeURIComponent(email)}&token=${encodeURIComponent(token)}`;
        
        const emailParams = {
            to_email: email,
            from_name: "Guid'Asso",
            subject: "Guid'Asso - Activation de votre compte.", 
            message: `Bonjour, \n\nPour commencer à utiliser votre compte Guid'Asso, merci de changer votre mot de passe en cliquant sur ce lien : \n${activationLink}\n\n`,
        };

        console.log("Vérification des paramètres EmailJS :", emailParams);
        
        emailjs.send("service_rln42mj", "template_i0dp3s5", emailParams)
        .then(() => {
            console.log(" Email envoyé à :", email);
            setTimeout(() => {
                window.location.href = "/../../views/pageadmin.php";
            }, 3000);
        })
        .catch((error) => {
            console.error("Erreur EmailJS :", error);
            alert("Erreur EmailJS : " + JSON.stringify(error));
        });
    } else {
        console.log("Condition d'envoie Email non remplie.");
    }
});

</script>
