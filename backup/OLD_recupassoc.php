<?php
include_once __DIR__ . '/config/BD.php';

if(isset($_POST["query"])){
    $query = $_POST["query"];

    $stmt = $pdo->prepare("SELECT NOMASSO FROM ASSOCIATION WHERE NOMASSO LIKE :query");
    $stmt->execute(['query' => '%' . $query . '%']);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($result as $row){
        $nomAsso = $row["NOMASSO"];
        echo '<ul>'.$nomAsso.'</ul>';
    }
}
?>
