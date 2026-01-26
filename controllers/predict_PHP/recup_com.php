<?php
include_once __DIR__ . '/../../config/BD.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST["query"])) {
    $query = $_POST["query"];

    // Requête sécurisée pour rechercher les communes
    $stmt = $pdo->prepare("SELECT DISTINCT Commune, CODE_POSTAL FROM COMMUNE WHERE Commune LIKE :query");
    $stmt->execute(['query' => '%' . $query . '%']);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Vérifier le nombre d'occurrences de chaque commune
    $communeCount = [];
    foreach ($result as $row) {
        $commune = $row["Commune"] ?? '';
        if (!empty($commune)) {
            if (!isset($communeCount[$commune])) {
                $communeCount[$commune] = 0;
            }
            $communeCount[$commune]++;
        }
    }

    // Générer la liste des suggestions
    if (!empty($result)) {
        echo '<ul>';
        foreach ($result as $row) {
            $commune = htmlspecialchars($row["Commune"] ?? '', ENT_QUOTES, 'UTF-8');
            $codePostal = htmlspecialchars($row["CODE_POSTAL"] ?? '', ENT_QUOTES, 'UTF-8');

            // Affichage du code postal si plusieurs communes ont le même nom
            if (isset($communeCount[$commune]) && $communeCount[$commune] > 1 && !empty($codePostal)) {
                echo "<li data-commune='$commune' data-code='$codePostal'>$commune - $codePostal</li>";
            } else {
                echo "<li data-commune='$commune'>$commune</li>";
            }
        }
        echo '</ul>';
    } else {
        echo '<ul><li>Aucune commune trouvée</li></ul>';
    }
}
