<?php
session_start(); // Démarrer la session
include_once __DIR__ . '/config/BD.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['MAIL'] ?? '';
    $ancien_mdp = $_POST['ancien_mdp'] ?? '';
    $nouveau_mdp = $_POST['nouveau_mdp'] ?? '';
    $confirm_mdp = $_POST['confirm_mdp'] ?? '';

    if (empty($email) || empty($ancien_mdp) || empty($nouveau_mdp) || empty($confirm_mdp)) {
        $_SESSION['error_message_modif_mdp'] = "Tous les champs sont requis.";
        header("Location: /views/mon_espace.php");
        exit();
    } elseif ($ancien_mdp == $nouveau_mdp) {
        $_SESSION['error_message_modif_mdp'] = "Le nouveau mot de passe ne peut pas être identique à l'ancien.";
        header("Location: /views/mon_espace.php");
        exit();
    } elseif (strlen($nouveau_mdp) < 8 || !preg_match('/[^a-zA-Z\d]/', $nouveau_mdp)) {
        $_SESSION['error_message_modif_mdp'] = "Le mot de passe doit comporter au moins 8 caractères et inclure un caractère spécial.";
        header("Location: /views/mon_espace.php");
        exit();
    } elseif ($nouveau_mdp !== $confirm_mdp) {
        $_SESSION['error_message_modif_mdp'] = "Les nouveaux mots de passe ne correspondent pas.";
        header("Location: /views/mon_espace.php");
        exit();
    } else {
        try {
            // Vérifier l'ancien mot de passe
            $stmt = $pdo->prepare("SELECT MOTDEPASSE FROM GUIDASSO WHERE MAIL = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($ancien_mdp, $user['MOTDEPASSE'])) {
                $_SESSION['error_message_modif_mdp'] = "Ancien mot de passe incorrect.";
                header("Location: /views/mon_espace.php");
                exit();
            } else {
                // Hacher le nouveau mot de passe
                $hashed_password = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
                
                // Mettre à jour le mot de passe dans la base de données
                $update_stmt = $pdo->prepare("UPDATE GUIDASSO SET MOTDEPASSE = :nouveau_mdp WHERE MAIL = :email");
                $update_stmt->execute(['nouveau_mdp' => $hashed_password, 'email' => $email]);
                
                $_SESSION['success_message'] = "Mot de passe modifié avec succès.";
                header("Location: /views/mon_espace.php");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['error_message_modif_mdp'] = "Erreur lors de la modification du mot de passe : " . $e->getMessage();
            header("Location: /views/mon_espace.php");
            exit();
        }
    }
}
?>
