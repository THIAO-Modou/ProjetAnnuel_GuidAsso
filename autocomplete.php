<?php
include_once __DIR__ . '/config/BD.php';


if (isset($_POST['query'])) {
    $query = trim($_POST['query']); // Supprime les espaces inutiles

    if (!empty($query)) {
        // Préparer la requête pour récupérer les e-mails correspondants
        $stmt = $pdo->prepare("SELECT MAIL FROM GUIDASSO WHERE MAIL LIKE :query LIMIT 10");
        $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Construire une liste HTML avec les résultats
            echo '<ul>';
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<li>' . htmlspecialchars($row['MAIL']) . '</li>';
            }
            echo '</ul>';
        } else {
            // Aucun résultat trouvé
            echo '<ul><li style="color: #999;">Aucune correspondance trouvée</li></ul>';
        }
    } else {
        // Si la requête est vide
        echo '<ul><li style="color: #999;">Veuillez entrer un texte</li></ul>';
    }
} else {
    // Si aucun paramètre "query" n'est envoyé
    echo '<ul><li style="color: #999;">Requête invalide</li></ul>';
}
?>
