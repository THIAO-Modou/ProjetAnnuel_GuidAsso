<?php
include_once __DIR__ . '/config/BD.php';

try {
    $nbLignes = isset($_GET['nbLignes']) ? intval($_GET['nbLignes']) : 20;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($page - 1) * $nbLignes;

    // 🔹 Calculer le nombre total de pages
    $countQuery = "SELECT COUNT(*) FROM QUESTIONNAIRE";
    $totalEntries = $pdo->query($countQuery)->fetchColumn();
    $totalPages = ceil($totalEntries / $nbLignes);

    // 🔹 Récupérer les données paginées
    $query = "SELECT 
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
                WHERE TYPEQUESTIONNAIRE != 'AN'
                ORDER BY Q.HORODATEUR DESC 
                LIMIT :nbLignes OFFSET :offset";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":nbLignes", $nbLignes, PDO::PARAM_INT);
    $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode([
        "entries" => $entries,
        "totalPages" => $totalPages
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur lors de l'exécution de la requête."]);
    exit();
}
?>
