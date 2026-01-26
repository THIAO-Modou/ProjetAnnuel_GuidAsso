<?php
include_once __DIR__ . '/../../config/BD.php';

// Active l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);


if(isset($_POST["query"])){
    $query = $_POST["query"];

    // Requête SQL pour chercher les prénoms correspondants
    $stmt = $pdo->prepare("SELECT DISTINCT PRENOMCONTACT FROM CONTACT WHERE PRENOMCONTACT LIKE :query");
    $stmt->execute(['query' => '%' . $query . '%']);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Affiche les résultats dans une liste HTML s'il y a des entrées
    if (!empty($result)) {
        echo '<ul>';
        foreach ($result as $row) {
            $prenom = htmlspecialchars($row["PRENOMCONTACT"] ?? '', ENT_QUOTES, 'UTF-8'); // Gère NULL et sécurise
            echo "<li data-nom='$prenom'> $prenom</li>";
        }
        echo '</ul>';
    } else {
        echo '<ul><li>Aucun prénom contact trouvé</li></ul>';
    }
}
?>
