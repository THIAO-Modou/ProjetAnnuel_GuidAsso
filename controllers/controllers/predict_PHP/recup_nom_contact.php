<?php
include_once __DIR__ . '/../../config/BD.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST["query"])) {
    $query = $_POST["query"];

    $stmt = $pdo->prepare("
        SELECT DISTINCT NOMCONTACT FROM CONTACT
        WHERE NOMCONTACT LIKE :query
    ");
    $stmt->execute(['query' => '%' . $query . '%']);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<ul>';
    if (!empty($result)) {
        foreach ($result as $row) {
            $nom_contact = htmlspecialchars($row["NOMCONTACT"] ?? '', ENT_QUOTES, 'UTF-8');
            echo "<li data-nomcontact='$nom_contact'>$nom_contact</li>";
        }
    } else {
        echo '<li>Aucun contact trouvé</li>';
    }
    echo '</ul>';
}
?>
