<?php
include_once __DIR__ . '/../../config/BD.php';

// Active l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(isset($_POST["query"])){
    $query = $_POST["query"];

    // Récupérer les structures et vérifier les doublons
    $stmt = $pdo->prepare("SELECT NOMSTRUCTURE, EPCI, CODEPOSTAL FROM STRUCTURE WHERE NOMSTRUCTURE LIKE :query");
    $stmt->execute(['query' => '%' . $query . '%']);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Vérifier le nombre d'occurrences de chaque structure
    $structureCount = [];
    foreach ($result as $row) {
        $nomStruc = $row["NOMSTRUCTURE"] ?? ''; // Assure que NOMSTRUCTURE n'est jamais NULL
        if (!empty($nomStruc)) {
            if (!isset($structureCount[$nomStruc])) {
                $structureCount[$nomStruc] = 0;
            }
            $structureCount[$nomStruc]++;
        }
    }

    // Générer la liste des suggestions
    if (!empty($result)) {
        echo '<ul>';
        foreach ($result as $row) {
            $nomStruc = htmlspecialchars($row["NOMSTRUCTURE"] ?? '', ENT_QUOTES, 'UTF-8');
            $codePostal = htmlspecialchars($row["CODEPOSTAL"] ?? '', ENT_QUOTES, 'UTF-8');
            $epci = htmlspecialchars($row["EPCI"] ?? '', ENT_QUOTES, 'UTF-8');

            // Si la structure a des doublons, on affiche le code postal
            if (isset($structureCount[$nomStruc]) && $structureCount[$nomStruc] > 1 && !empty($codePostal)) {
                echo "<li data-nom='$nomStruc' data-epci='$epci' data-code='$codePostal'>$nomStruc [$epci - $codePostal]</li>";
            } else {
                echo "<li data-nom='$nomStruc'>
                    <span style='font-size:12px; font-weight:bold;'>$nomStruc</span>
                    <span style='font-size:9px;'> $epci - $codePostal</span>
                </li>";   
            // Pas de code postal pour les uniques
            }
        }
        echo '</ul>';
    } else {
        echo '<ul><li>Aucune structure trouvée</li></ul>';
    }
}
