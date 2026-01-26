<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=UTF-8');
ob_start(); // Capture toute sortie parasite

include_once __DIR__ . '/../../config/BD.php';

try {
    // Calculer le nombre total d'entrées
    $nbLignes = isset($_GET['nbLignes']) ? intval($_GET['nbLignes']) : 10;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($page - 1) * $nbLignes;

    if (!isset($pdo)) {
        throw new Exception("Erreur : connexion à la base de données non établie.");
    }

    // Suppression utilisateur
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mail'])) {
        ob_end_clean(); // Supprime tout texte parasite
        $mail = trim($_POST['mail']);

        $query = "DELETE FROM GUIDASSO WHERE MAIL = :mail";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":mail", $mail, PDO::PARAM_STR);
        $stmt->execute();

        echo json_encode(["success" => ($stmt->rowCount() > 0)]);
        exit(); // Empêche toute sortie après JSON
    }

    // Affichage des utilisateurs
    $countQuery = "SELECT COUNT(*) FROM GUIDASSO";
    $totalEntries = $pdo->query($countQuery)->fetchColumn();
    $totalPages = ($totalEntries > 0) ? ceil($totalEntries / $nbLignes) : 1;

    if ($totalEntries === 0) {
        echo json_encode(["entries" => [], "totalPages" => 1]);
        exit();
    }

    // Récupérer les résultats avec pagination
    $query = "SELECT 
        G.NOMPERSONNE,
        G.PRENOMPERSONNE,
        G.MAIL,
        G.CLASSIFICATION,
        F.NAMEFONCTION
        FROM GUIDASSO G
        LEFT JOIN FONCTION F ON G.IDFONCTION = F.IDFONCTION
        ORDER BY NOMPERSONNE LIMIT :nbLignes OFFSET :offset";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(":nbLignes", $nbLignes, PDO::PARAM_INT);
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        "entries" => $stmt->fetchAll(PDO::FETCH_ASSOC),
        "totalPages" => $totalPages
    ]);
    exit();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
    exit();
}
