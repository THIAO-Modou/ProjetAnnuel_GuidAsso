<?php
include_once __DIR__ . '/../../config/BD.php';

// Spécifie que la réponse sera au format JSON
header('Content-Type: application/json');

// Vérifie que le paramètre POST 'assoc' est bien transmis et non vide
if (isset($_POST['nom_contact']) && !empty($_POST['nom_contact'])) {
    $nomContact = trim($_POST['nom_contact']);

    // Gestion de la pagination : nombre d'entrées par page
    $nbLignes = isset($_POST['nbLignes']) ? intval($_POST['nbLignes']) : 20;
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $offset = ($page - 1) * $nbLignes;

    // Compter le nombre total de résultats
    $countQuery = "
        SELECT COUNT(*) 
        FROM CONTACT 
        LEFT JOIN QUESTIONNAIRE ON CONTACT.IDCONTACT = QUESTIONNAIRE.IDCONTACT
        WHERE CONTACT.NOMCONTACT LIKE :nom
    ";
    $stmtCount = $pdo->prepare($countQuery);
    $stmtCount->bindValue(':nom', "%$nomContact%", PDO::PARAM_STR);
    $stmtCount->execute();
    $totalEntries = $stmtCount->fetchColumn();
    $totalPages = $totalEntries > 0 ? ceil($totalEntries / $nbLignes) : 1;

    // Requête avec pagination
    $query = "
        SELECT QUESTIONNAIRE.*, CONTACT.*, GUIDASSO.*
        FROM QUESTIONNAIRE 
        LEFT JOIN CONTACT ON QUESTIONNAIRE.IDCONTACT = CONTACT.IDCONTACT
        LEFT JOIN GUIDASSO ON QUESTIONNAIRE.MAILGUIDASSO = GUIDASSO.MAIL
        WHERE CONTACT.NOMCONTACT LIKE :nom
        ORDER BY QUESTIONNAIRE.HORODATEUR DESC
        LIMIT :limit OFFSET :offset
    ";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':nom', "%$nomContact%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $nbLignes, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    //Envoie les résultats sous forme JSON aux fichier JS
    echo json_encode([
        "entries" => $stmt->fetchAll(PDO::FETCH_ASSOC),
        "totalPages" => $totalPages
    ]);
} else {
    // Si aucun nom de contact n’est fourni, evoyer une réponse vide
    echo json_encode(["entries" => [], "totalPages" => 1]);
}
?>
