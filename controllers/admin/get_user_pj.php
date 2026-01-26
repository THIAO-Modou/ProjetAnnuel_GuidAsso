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
     // Suppression piece joint
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idquestionnaire'])) {
        ob_end_clean();
        $idquestionnaire = (int)$_POST['idquestionnaire'];
        $stmt = $pdo->prepare("UPDATE QUESTIONNAIRE SET PJBLOCNOTE = NULL WHERE IDQUESTIONNAIRE = :id");
        $stmt->bindValue(':id', $idquestionnaire, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(["success" => ($stmt->rowCount() > 0)]);
        exit();
    }

    $nbLignes = isset($_GET['nbLignes']) ? intval($_GET['nbLignes']) : 10;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($page - 1) * $nbLignes;

    // Récupérer uniquement les entrées où le bloc-note n'est pas vide pour les profiles admin et admin visiteur
    if($idFonction == 2 || $idFonction == 3){
        $countStmt = $pdo->query("SELECT COUNT(*) FROM QUESTIONNAIRE WHERE BLOCNOTE IS NOT NULL AND BLOCNOTE != ''");
        $stmt = $pdo->prepare("
                SELECT PJBLOCNOTE AS FILE, HORODATEUR, IDQUESTIONNAIRE
                FROM QUESTIONNAIRE 
                WHERE PJBLOCNOTE IS NOT NULL");
    }elseif ($idFonction == 1 || $idFonction ==4) {
        $stmt = $pdo->prepare("
            SELECT PJBLOCNOTE AS FILE, HORODATEUR, IDQUESTIONNAIRE
            FROM QUESTIONNAIRE 
            WHERE PJBLOCNOTE IS NOT NULL AND MAILGUIDASSO = :email
        ");
        $stmt->bindParam(':email', $user_email, PDO::PARAM_STR);
    }
    $stmt->execute();
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($files as &$file) {
        if (!empty($file["FILE"])) {
            $fileName = basename($file["FILE"]);
            $file["FILE_NAME"] = $fileName;
            $file["DOWNLOAD_URL"] = "/controllers/admin/download_file.php?file=" . urlencode($fileName);
        }
    }

    echo json_encode($files);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erreur SQL : " . $e->getMessage()]);
}
?>
