<?php
include_once __DIR__ . '/config/BD.php';

if(isset($_POST["query"])){
    $query = $_POST["query"];

    $stmt = $pdo->prepare("SELECT Commune FROM COMMUNE WHERE Commune LIKE :query");
    $stmt->execute(['query' => '%' . $query . '%']);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($result as $row){
        $commune = $row["Commune"];
        echo '<ul>'.$commune.'</ul>';
    }
}
?>