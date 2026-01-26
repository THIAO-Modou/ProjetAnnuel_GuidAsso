<?php
include_once __DIR__ . '/config/BD.php';


if(isset($_POST["query"])){
    $query = $_POST["query"];

    $stmt = $pdo->prepare("SELECT EMAILCORRESPONDANT FROM CONTACT WHERE NOMCONTACT LIKE :query");
    $stmt->execute(['query' => '%' . $query . '%']);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($result as $row){
        $nomContact = $row["EMAILCORRESPONDANT"];
        echo '<ul>'.$nomContact.'</ul>';
    }
}
?>