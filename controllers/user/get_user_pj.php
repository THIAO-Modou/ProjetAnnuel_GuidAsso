<?php
session_start();
require_once __DIR__ . '/../../config/BD.php';

header('Content-Type: application/json');

if (!isset($_SESSION['MAIL'])) {
    echo json_encode(["error" => "Utilisateur non authentifié"]);
    exit();
}

$user_email = $_SESSION['MAIL'];

try {
    $stmt = $pdo->prepare("
        SELECT PJBLOCNOTE AS FILE, HORODATEUR 
        FROM QUESTIONNAIRE 
        WHERE PJBLOCNOTE IS NOT NULL AND MAILGUIDASSO = :email
    ");
    $stmt->bindParam(':email', $user_email, PDO::PARAM_STR);
    $stmt->execute();
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($files as &$file) {
        if (!empty($file["FILE"])) {
            $fileName = basename($file["FILE"]);
            $file["FILE_NAME"] = $fileName;
            $file["DOWNLOAD_URL"] = "/controllers/user/download_user.php?file=" . urlencode($fileName);
        }
    }

    echo json_encode($files);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erreur SQL : " . $e->getMessage()]);
}
?>
