<?php
include_once __DIR__ . '/config/BD.php';


if(isset($_POST["query"])){
    $query = $_POST["query"];

    $stmt = $pdo->prepare("SELECT NOMCONTACT FROM CONTACT WHERE NOMCONTACT LIKE :query");
    $stmt->execute(['query' => '%' . $query . '%']);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($result as $row){
        $nomContact = $row["NOMCONTACT"];
        echo '<ul>'.$nomContact.'</ul>';
    }
}
?>