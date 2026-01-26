<?php
session_start();
include_once __DIR__ . '/../../config/BD.php';

if (!isset($_SESSION['MAIL'])) {
    $_SESSION['error_message_update'] = "Vous devez être connecté pour modifier vos informations.";
    header("Location: /views/pageconnexion.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Connexion à la base de données
    global $pdo;

    // Récupérer et nettoyer les données du formulaire
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $classification = trim($_POST['classification'] ?? '');
    $email = $_SESSION['MAIL']; // L'email ne peut pas être modifié

    // Vérifier que les champs ne sont pas vides
    if (empty($nom) || empty($prenom) || empty($classification)) {
        $_SESSION['error_message_update'] = "Tous les champs doivent être remplis.";
        header("Location: /views/mon_espace.php");
        exit();
    }

    // Vérifier que le nom et prénom n'ont pas de caractères spéciaux dangereux
    if (!preg_match("/^[a-zA-ZÀ-ÿ\s'-]+$/", $nom) || !preg_match("/^[a-zA-ZÀ-ÿ\s'-]+$/", $prenom)) {
        $_SESSION['error_message_update'] = "Le nom et le prénom ne doivent contenir que des lettres.";
        header("Location: /views/mon_espace.php");
        exit();
    }

    try {
        // Préparer la requête SQL pour éviter l'injection
        $stmt = $pdo->prepare("UPDATE GUIDASSO SET NOMPERSONNE = ?, PRENOMPERSONNE = ?, CLASSIFICATION = ? WHERE MAIL = ?");
        $stmt->execute([$nom, $prenom, $classification, $email]);

        // Vérifier si la mise à jour a bien eu lieu
        if ($stmt->rowCount() > 0) {
            $_SESSION['success_message'] = "Informations mises à jour avec succès.";
        } else {
            $_SESSION['error_message_update'] = "Aucune modification détectée.";
        }

        header("Location: /views/mon_espace.php");
        exit();
    } catch (PDOException $e) {
        error_log("❌ Erreur SQL : " . $e->getMessage());
        $_SESSION['error_message_update'] = "Une erreur s'est produite lors de la mise à jour.";
        header("Location: /views/mon_espace.php");
        exit();
    }
}
?>
