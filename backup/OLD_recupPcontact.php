
<?php
include_once __DIR__ . '/config/BD.php';

if(isset($_POST["query"])){
    $query = $_POST["query"];

    $stmt = $pdo->prepare("SELECT PRENOMCONTACT FROM CONTACT WHERE PRENOMCONTACT LIKE :query");
    $stmt->execute(['query' => '%' . $query . '%']);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($result as $row){
        $prenomContact = $row["PRENOMCONTACT"];
        echo '<ul>'.$prenomContact.'</ul>';
    }
}
?>