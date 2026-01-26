<?php
session_start();
require_once __DIR__ . '/../../config/BD.php';
header('Content-Type: application/json');

if (!isset($_SESSION['MAIL'])) {
    echo json_encode(["error" => "Utilisateur non authentifié"]);
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
        ob_end_clean();
        $idquestionnaire = (int)$_POST['idquestionnaire'];
        $stmt = $pdo->prepare("UPDATE QUESTIONNAIRE SET BLOCNOTE = NULL WHERE IDQUESTIONNAIRE = :id");
        $stmt->bindValue(':id', $idquestionnaire, PDO::PARAM_INT);
        $stmt->execute();
        echo json_encode(["success" => ($stmt->rowCount() > 0)]);
        exit();
    }

    $nbLignes = isset($_GET['nbLignes']) ? intval($_GET['nbLignes']) : 10;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($page - 1) * $nbLignes;

    // Récupérer uniquement les entrées où le bloc-note n'est pas vide pour les profiles admin et admin visiteur
    if ($idFonction == 2 || $idFonction ==3) {
        $countStmt = $pdo->query("SELECT COUNT(*) FROM QUESTIONNAIRE WHERE BLOCNOTE IS NOT NULL AND BLOCNOTE != ''");
        $stmt = $pdo->prepare("
            SELECT IDQUESTIONNAIRE, PJBLOCNOTE AS FILE, DATE_FORMAT(HORODATEUR, '%Y-%m-%d') AS HORODATEUR, NOMASSO, THEMEGENERAL, BLOCNOTE, MAILGUIDASSO AS UPLOADED_BY
            FROM QUESTIONNAIRE
            WHERE BLOCNOTE IS NOT NULL AND BLOCNOTE != ''
            ORDER BY HORODATEUR DESC
            LIMIT :limit OFFSET :offset
        ");
    
        // Récupérer uniquement les entrées où le bloc-note n'est pas vide pour les profiles utilisateur et utilisateur visiteur
    } elseif($idFonction == 1 || $idFonction == 4) {
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM QUESTIONNAIRE WHERE MAILGUIDASSO = :email AND BLOCNOTE IS NOT NULL AND BLOCNOTE != ''");
        $countStmt->bindValue(':email', $email, PDO::PARAM_STR);
        $countStmt->execute();
        $stmt = $pdo->prepare("
            SELECT IDQUESTIONNAIRE, PJBLOCNOTE AS FILE, DATE_FORMAT(HORODATEUR, '%Y-%m-%d') AS HORODATEUR, NOMASSO, THEMEGENERAL, BLOCNOTE, MAILGUIDASSO AS UPLOADED_BY
            FROM QUESTIONNAIRE
            WHERE MAILGUIDASSO = :email AND BLOCNOTE IS NOT NULL AND BLOCNOTE != ''
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
            $file["DOWNLOAD_URL"] = "/controllers/admin/download_pjbn.php?file=" . urlencode($fileName);
        } else {
            $file["FILE_NAME"] = "Aucune PJ";
            $file["DOWNLOAD_URL"] = "";
        }
    }

    echo json_encode([
        "files" => $files,
        "totalPages" => ceil($totalRows / $nbLignes)
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erreur SQL : " . $e->getMessage()]);
}
?>