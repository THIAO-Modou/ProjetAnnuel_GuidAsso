<?php
session_start(); // Démarrer la session
include_once __DIR__ . '/config/BD.php';

// Vérifie que le formulaire a bien été soumis en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère l'e-mail de session et les champs du formulaire
    $email = $_SESSION['MAIL'] ?? '';
    $ancien_mdp = $_POST['ancien_mdp'] ?? '';
    $nouveau_mdp = $_POST['nouveau_mdp'] ?? '';
    $confirm_mdp = $_POST['confirm_mdp'] ?? '';

    // Vérifie que tous les champs sont remplis
    if (empty($email) || empty($ancien_mdp) || empty($nouveau_mdp) || empty($confirm_mdp)) {
        $_SESSION['error_message_modif_mdp'] = "Tous les champs sont requis.";
        header("Location: /../views/mon_espace.php");
        exit();

    // Empêche de réutiliser l'ancien mot de passe comme nouveau
    } elseif ($ancien_mdp == $nouveau_mdp) {
        $_SESSION['error_message_modif_mdp'] = "Le nouveau mot de passe ne peut pas être identique à l'ancien.";
        header("Location: /../views/mon_espace.php");
        exit();
    
    // Vérifie la complexité minimale du nouveau mot de passe
    } elseif (strlen($nouveau_mdp) < 8 || !preg_match('/[^a-zA-Z\d]/', $nouveau_mdp)) {
        $_SESSION['error_message_modif_mdp'] = "Le mot de passe doit comporter au moins 8 caractères et inclure un caractère spécial.";
        header("Location: /../views/mon_espace.php");
        exit();
    
    // Vérifie que les deux nouveaux mots de passe correspondent
    } elseif ($nouveau_mdp !== $confirm_mdp) {
        $_SESSION['error_message_modif_mdp'] = "Les nouveaux mots de passe ne correspondent pas.";
        header("Location: /../views/mon_espace.php");
        exit();

    // Si toutes les vérifications sont OK
    } else {
        try {
            // Récupère le mot de passe actuel dans la base pour vérification
            $stmt = $pdo->prepare("SELECT MOTDEPASSE FROM GUIDASSO WHERE MAIL = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si aucun utilisateur trouvé ou mot de passe incorrect
            if (!$user || !password_verify($ancien_mdp, $user['MOTDEPASSE'])) {
                $_SESSION['error_message_modif_mdp'] = "Ancien mot de passe incorrect.";
                header("Location: /../views/mon_espace.php");
                exit();

            // Sinon, on peut modifier le mot de passe
            } else {
                // Hacher le nouveau mot de passe
                $hashed_password = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
                
                // Mettre à jour le mot de passe dans la base de données
                $update_stmt = $pdo->prepare("UPDATE GUIDASSO SET MOTDEPASSE = :nouveau_mdp WHERE MAIL = :email");
                $update_stmt->execute(['nouveau_mdp' => $hashed_password, 'email' => $email]);
                
                // Confirmation et redirection vers mon_espace
                $_SESSION['success_message'] = "Mot de passe modifié avec succès.";
                header("Location: /../views/mon_espace.php");
                exit();
            }
        } catch (PDOException $e) {
            // Gestion des erreurs SQL
            $_SESSION['error_message_modif_mdp'] = "Erreur lors de la modification du mot de passe : " . $e->getMessage();
            header("Location: /../views/mon_espace.php");
            exit();
        }
    }
}
?>
