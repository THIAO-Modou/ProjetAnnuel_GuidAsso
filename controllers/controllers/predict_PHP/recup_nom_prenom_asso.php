<?php
include_once __DIR__ . '/../../config/BD.php';

// Active l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérifie si le paramètre POST "query" a été envoyé
if (isset($_POST["query"])) {
    $query = $_POST["query"]; // Récupère et nettoie la valeur saisie par l'utilisateur

    // Prépare une requête SQL pour chercher les contacts correspondant au nom
    $stmt = $pdo->prepare("
        SELECT DISTINCT C.NOMCONTACT, C.PRENOMCONTACT, Q.NOMASSO
        FROM CONTACT C
        LEFT JOIN QUESTIONNAIRE Q ON C.IDCONTACT = Q.IDCONTACT
        WHERE C.NOMCONTACT LIKE :query
    ");
    $stmt->execute(['query' => '%' . $query . '%']);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<ul>';
    if (!empty($result)) {
        foreach ($result as $row) {
            $nom_contact = htmlspecialchars($row["NOMCONTACT"] ?? '', ENT_QUOTES, 'UTF-8');
            $prenom = htmlspecialchars($row["PRENOMCONTACT"] ?? '', ENT_QUOTES, 'UTF-8');
            $asso = htmlspecialchars($row["NOMASSO"] ?? '', ENT_QUOTES, 'UTF-8');

            // Affiche les résultats dans une liste HTML
            echo "<li data-nomcontact='$nom_contact'>
                    <span style='font-size:12px; font-weight:bold;'>$nom_contact</span>
                    <span style='font-size:9px;'> $prenom ";if(!empty($asso)) echo "[$asso]"; // Si l'association est renseignée, on l'affiche entre crochets
                    echo"</span>";
            echo"</li>";
        }
    } else {
        echo '<li>Aucun contact trouvé</li>';
    }
    echo '</ul>';
}
?>
