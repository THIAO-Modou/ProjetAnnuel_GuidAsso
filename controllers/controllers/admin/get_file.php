<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../../config/BD.php';
header('Content-Type: application/json');

if (!isset($_SESSION['MAIL'])) {
    echo json_encode(["files" => [], "totalPages" => 0]);
    exit();
}
$user = getUserInfoByEmail($_SESSION['MAIL']);
$email = $_SESSION['MAIL'];

if ($user) {
    $idFonction = $user['IDFONCTION'];
} else {
    echo json_encode(["error" => "Fonction utilisateur introuvable"]);
    exit();
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idquestionnaire'])) {
        $id = (int)$_POST['idquestionnaire'];
        $stmt = $pdo->prepare("UPDATE QUESTIONNAIRE SET FILE = NULL WHERE IDQUESTIONNAIRE = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        echo json_encode(["success" => true]);
        exit();
    }

    $nbLignes = isset($_GET['nbLignes']) ? (int)$_GET['nbLignes'] : 5;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $nbLignes;

    $countStmt = $pdo->query("SELECT COUNT(*) FROM QUESTIONNAIRE WHERE FILE IS NOT NULL AND FILE <> ''");
    $totalRows = (int)$countStmt->fetchColumn();

    if ($idFonction == 2 || $idFonction ==3) {
        $stmt = $pdo->prepare("
            SELECT
                IDQUESTIONNAIRE,
                NOMEVENEMENT,
                FILE AS FILE,
                DATE_FORMAT(HORODATEUR, '%Y-%m-%d') AS HORODATEUR,
                MAILGUIDASSO AS UPLOADED_BY 
            FROM QUESTIONNAIRE 
            WHERE FILE IS NOT NULL
            ORDER BY HORODATEUR DESC
            LIMIT :limit OFFSET :offset
        ");
    } elseif($idFonction == 1 || $idFonction == 4) {
        $stmt = $pdo->prepare("
            SELECT
                IDQUESTIONNAIRE,
                NOMEVENEMENT,
                FILE AS FILE,
                DATE_FORMAT(HORODATEUR, '%Y-%m-%d') AS HORODATEUR,
                MAILGUIDASSO AS UPLOADED_BY 
            FROM QUESTIONNAIRE 
            WHERE MAILGUIDASSO = :email AND FILE IS NOT NULL
            ORDER BY HORODATEUR DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    }
    if (!isset($totalRows)) {
        $countStmt->execute();
        $totalRows = (int) $countStmt->fetchColumn();
    }

    $stmt->bindValue(':limit', $nbLignes, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($files as &$file) {
        if (!empty($file["FILE"])) {
            $fileName = basename($file["FILE"]);
            $file["FILE_NAME"] = $fileName;
            $file["DOWNLOAD_URL"] = "/../controllers/admin/download_file.php?file=" . urlencode($fileName);
        } 
    }
    if (headers_sent()) {
  file_put_contents('headers_debug.log', "Headers déjà envoyés avant JSON\n", FILE_APPEND);
}


    echo json_encode([
        "files" => $files,
        "totalPages" => ceil($totalRows / $nbLignes)
    ]);
} catch (Exception $e) {
    echo json_encode(["files" => [], "totalPages" => 0]);
}
?>
