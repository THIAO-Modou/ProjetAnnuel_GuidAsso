<?php
include_once __DIR__ . '/../../config/BD.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if(isset($_POST["query"])){
    $query = $_POST["query"];

    // Récupérer les associations et vérifier les doublons
    $stmt = $pdo->prepare("SELECT NOMASSO, CODEPOSTAL, EPCI FROM ASSOCIATION WHERE NOMASSO LIKE :query");
    $stmt->execute(['query' => '%' . $query . '%']);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Vérifier le nombre d'occurrences de chaque association
    $assoCount = [];
    foreach ($result as $row) {
        $nomAsso = $row["NOMASSO"] ?? ''; // Assure que NOMASSO n'est jamais NULL
        if (!empty($nomAsso)) {
            if (!isset($assoCount[$nomAsso])) {
                $assoCount[$nomAsso] = 0;
            }
            $assoCount[$nomAsso]++;
        }
    }

    // Générer la liste des suggestions
    if (!empty($result)) {
        echo '<ul>';
        foreach ($result as $row) {
            $nomAsso = htmlspecialchars($row["NOMASSO"] ?? '', ENT_QUOTES, 'UTF-8');
            $codePostal = htmlspecialchars($row["CODEPOSTAL"] ?? '', ENT_QUOTES, 'UTF-8');
            $epci = htmlspecialchars($row["EPCI"] ?? '', ENT_QUOTES, 'UTF-8');

            // ✅ Si l'association a des doublons, on affiche le code postal
            if (isset($assoCount[$nomAsso]) && $assoCount[$nomAsso] > 1 && !empty($codePostal)) {
                
                echo "<li data-nom='$nomAsso'>
                    <span style='font-size:12px; font-weight:bold; margin-left:3px;'> $nomAsso</span>
                    <span style='font-size:9px;'> [$epci $codePostal]</span>
                </li>";
            } else {
                echo "<li data-nom='$nomAsso'>
                    <span style='font-size:12px; font-weight:bold;'> $nomAsso</span>
                    <span style='font-size:9px;'>"; if(!empty($epci)) echo"[$epci]"; echo"</span>";
                echo"</li>";// Pas de code postal pour les uniques
            }
        }
        echo '</ul>';
    } else {
        echo '<ul><li>Aucune association trouvée</li></ul>';
    }
}

/*
// 🔴 Code mis en commentaire : Récupération automatique de la commune via le code postal
// if(isset($_POST["codepostal"])){
//     $codepostal = $_POST["codepostal"];

//     // Vérifier si la colonne "CODEPOSTAL" existe bien dans la table COMMUNE
//     $checkColumn = $pdo->query("SHOW COLUMNS FROM COMMUNE LIKE 'CODEPOSTAL'")->fetch();
//     $codePostalColumn = $checkColumn ? "CODEPOSTAL" : "Code_Postal"; // Adapte selon le vrai nom

//     // Requête SQL avec le bon nom de colonne
//     $stmt = $pdo->prepare("SELECT Commune FROM COMMUNE WHERE $codePostalColumn = :codepostal LIMIT 1");
//     $stmt->execute(['codepostal' => $codepostal]);
//     $result = $stmt->fetch(PDO::FETCH_ASSOC);

//     // Retourner la commune si trouvée, sinon rien
//     if ($result) {
//         echo htmlspecialchars($result["Commune"] ?? '', ENT_QUOTES, 'UTF-8');
//     } else {
//         echo ''; // Pas de commune trouvée
//     }
// }
*/
?>
