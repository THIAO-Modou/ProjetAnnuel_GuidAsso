<?php
// Active l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/../../config/BD.php';

try {
    //  Gestion de la pagination
    $nbLignes = isset($_GET['nbLignes']) ? intval($_GET['nbLignes']) : 10;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($page - 1) * $nbLignes;

    // Compter le nombre total d'entrées
    $totalQuery = $pdo->query("SELECT COUNT(*) FROM QUESTIONNAIRE WHERE TYPEQUESTIONNAIRE != 'AN'");
    $totalEntries = $totalQuery->fetchColumn();
    $totalPages = max(1, ceil($totalEntries / $nbLignes));

    // Récupération des données
    $sql = "
        SELECT 
            Q.HORODATEUR,
            Q.NOMASSO, 
            Q.NOMSTRUCTURE, 
            Q.NOMEVENEMENT,
            C.NOMCONTACT,
            C.CIVILITE,
            Q.THEMEGENERAL,
            Q.AUTRETHEMATIQUE,
            Q.TYPEQUESTIONNAIRE,
            G.NOMPERSONNE AS NOMUTILISATEUR,
            G.PRENOMPERSONNE AS PRENOMUTILISATEUR
        FROM QUESTIONNAIRE Q
        LEFT JOIN CONTACT C ON Q.IDCONTACT = C.IDCONTACT
        LEFT JOIN GUIDASSO G ON Q.MAILGUIDASSO = G.MAIL
        WHERE Q.TYPEQUESTIONNAIRE IS NULL OR Q.TYPEQUESTIONNAIRE != 'AN'
        ORDER BY Q.HORODATEUR DESC
        LIMIT :nbLignes OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nbLignes', $nbLignes, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    // Recuperation des resultats
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

     //Envoie les résultats sous forme JSON aux fichier JS
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        "entries" => $entries,
        "totalPages" => $totalPages
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}
