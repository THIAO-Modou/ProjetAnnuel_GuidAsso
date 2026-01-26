<?php
include_once __DIR__ . '/config/BD.php';

if(isset($_POST["query"])){
    $query = $_POST["query"];

    $stmt = $pdo->prepare("SELECT MAIL FROM GUIDASSO WHERE MAIL LIKE :query");
    $stmt->execute(['query' => '%' . $query . '%']);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($result as $row){
        $nomMail = $row["MAIL"];
        echo '<ul>'.$nomMail.'</ul>';
    }
}
?>