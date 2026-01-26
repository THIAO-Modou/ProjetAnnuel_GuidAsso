<?php
session_start();
require_once __DIR__ . '/../../config/BD.php';

// Définit le type de réponse en JSON
header('Content-Type: application/json');

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['MAIL'])) {
    echo json_encode(["error" => "Utilisateur non authentifié"]);
    exit();
}
// Récupère l'adresse mail de l'utilisateur connecté
$user_email = $_SESSION['MAIL'];

// Sélectionne les fichiers (bloc-notes joints) associés à cet utilisateur
try {
    $stmt = $pdo->prepare("
        SELECT PJBLOCNOTE AS FILE, HORODATEUR 
        FROM QUESTIONNAIRE 
        WHERE PJBLOCNOTE IS NOT NULL AND MAILGUIDASSO = :email
    ");
    // Excecution avec email dans la requête SQL
    $stmt->bindParam(':email', $user_email, PDO::PARAM_STR);
    $stmt->execute();
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Pour chaque fichier trouvé...
    foreach ($files as &$file) {
        // Extrait le nom du fichier
        if (!empty($file["FILE"])) {
            $fileName = basename($file["FILE"]);
            $file["FILE_NAME"] = $fileName;

            // Crée le lien de téléchargement du fichier
            $file["DOWNLOAD_URL"] = "/../controllers/user/download_user.php?file=" . urlencode($fileName);
        }
    }

    // Envoie le tableau complet de fichiers au format JSON
    echo json_encode($files);
} catch (PDOException $e) {
    // Gestion des erreurs SQL
    echo json_encode(["error" => "Erreur SQL : " . $e->getMessage()]);
}
?>
