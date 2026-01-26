<?php
session_start();
include("/config/BD.php");

if (isset($_POST['email1'], $_POST['nom1'], $_POST['prenom1'], $_POST['classification'])) {
    $email = $_POST['email1'];
    $nom = $_POST['nom1'];
    $prenom = $_POST['prenom1'];
    $classification = $_POST['classification'];

    try {
        $stmt = $pdo->prepare("UPDATE GUIDASSO SET NOMPERSONNE = :nom, PRENOMPERSONNE = :prenom, CLASSIFICATION = :classification WHERE MAIL = :mail");

        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':classification', $classification);
        $stmt->bindParam(':mail', $email);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "La modification a été effectuée avec succès.";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la modification de l'utilisateur.";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Une erreur est survenue : " . $e->getMessage();
    }

    // Redirection vers la page principale
    header("Location: pageadmin.php");
    exit();
}
?>
