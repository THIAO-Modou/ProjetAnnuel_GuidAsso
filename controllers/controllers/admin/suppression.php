<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include_once __DIR__ . '/../../config/BD.php';
include_once __DIR__ . '/../../config/session.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Initialisation des variables d'erreur et de succès
        $_SESSION['error_message_suppression'] = "";
        $_SESSION['success_message'] = "";
        $_SESSION['form_data'] = $_POST; // Conserver les valeurs en cas d'erreur

        if (!isset($_POST['form_type'])) {
            throw new Exception("Type de formulaire non spécifié.");
        }

        // Vérification des champs obligatoires
        $missingFields = [];

    } catch (Exception $e) {
        $_SESSION['error_message_suppression'] = "Erreur : " . $e->getMessage();
    }
}
?>
