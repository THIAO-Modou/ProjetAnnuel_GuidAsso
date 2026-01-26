<?php
include_once __DIR__ . '/../../config/BD.php';
// Spécifie que la réponse sera au format JSON
header('Content-Type: application/json');

// Vérifie que le paramètre POST 'assoc' est bien transmis et non vide
if (isset($_POST['assoc']) && !empty($_POST['assoc'])) {
    $assoc = trim($_POST['assoc']); // Nettoyage du texte de la requête (supprime les espaces superflus)
    
    // Gestion de la pagination : nombre d'entrées par page
    $nbLignes = isset($_POST['nbLignes']) ? intval($_POST['nbLignes']) : 20;
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1; //page courante (si non précisé, page 1 par défaut)
    $offset = ($page - 1) * $nbLignes;

    // Compter le nombre total d'entrées
    $countQuery = "SELECT COUNT(*) FROM QUESTIONNAIRE WHERE NOMASSO LIKE :assoc";
    $stmtCount = $pdo->prepare($countQuery);
    $stmtCount->bindValue(':assoc', "%$assoc%", PDO::PARAM_STR);
    $stmtCount->execute();
    $totalEntries = $stmtCount->fetchColumn(); // nombre total de lignes
    $totalPages = $totalEntries > 0 ? ceil($totalEntries / $nbLignes) : 1; //nombre total de pages (en arrondissant à l’entier supérieur)

    // Requête paginée sécurisée
    $query = "
        SELECT QUESTIONNAIRE.*, CONTACT.*, GUIDASSO.*
        FROM QUESTIONNAIRE
        LEFT JOIN CONTACT ON QUESTIONNAIRE.IDCONTACT = CONTACT.IDCONTACT
        LEFT JOIN GUIDASSO ON QUESTIONNAIRE.MAILGUIDASSO = GUIDASSO.MAIL
        WHERE QUESTIONNAIRE.NOMASSO LIKE :assoc
        ORDER BY QUESTIONNAIRE.HORODATEUR DESC
        LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':assoc', "%$assoc%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $nbLignes, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    //Envoie les résultats sous forme JSON au JS
    echo json_encode([
        "entries" => $stmt->fetchAll(PDO::FETCH_ASSOC),
        "totalPages" => $totalPages
    ]);
} else {
    // Si aucun nom d’association n’est fourni, evoyer une réponse vide
    echo json_encode(["entries" => [], "totalPages" => 1]);
}

?>
