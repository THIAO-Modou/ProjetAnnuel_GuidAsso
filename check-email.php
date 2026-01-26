<?php
include_once __DIR__ . '/config/BD.php';

// Requête pour récupérer les données de la colonne
$sql = "SELECT MAIL FROM GUIDASSO";
$result = $pdo->query($sql);

?>
