<?php

session_start(); 

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['MAIL'])) {
    header("Location: /pageconnexion.php");
    exit();
}

// Inclure la connexion à la base de données
include_once __DIR__ . '/config/BD.php';

// Vérifier si la connexion à la base de données existe
if (!isset($pdo)) {
    die("Erreur : connexion à la base de données non établie.");
}

// Vérifier si un paramètre de recherche est fourni
if (!isset($_GET['searchQuery']) || empty($_GET['searchQuery'])) {
    die("Erreur : aucun nom d'association fourni.");
}

$nomasso = $_GET['searchQuery'];

// Définir les en-têtes HTTP pour le téléchargement du fichier CSV
$filename = "base_de_donnees_GuidAsso_filtree.csv";
header('Content-Encoding: UTF-8');
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
echo "\xEF\xBB\xBF"; // Ajoute BOM UTF-8 pour éviter les problèmes d'encodage sous Excel

$fp = fopen('php://output', 'w');

// Récupérer les données des tables QUESTIONNAIRE et CONTACT filtrées par NOMASSO
$query_questionnaire = "SELECT QUESTIONNAIRE.*, CONTACT.* 
                        FROM QUESTIONNAIRE 
                        LEFT JOIN CONTACT ON QUESTIONNAIRE.IDCONTACT = CONTACT.IDCONTACT 
                        WHERE QUESTIONNAIRE.NOMASSO = :nomasso 
                        ORDER BY QUESTIONNAIRE.HORODATEUR ASC;";
$stmt_questionnaire = $pdo->prepare($query_questionnaire);
$stmt_questionnaire->bindParam(':nomasso', $nomasso, PDO::PARAM_STR);
$stmt_questionnaire->execute();

// Écriture des données de la table QUESTIONNAIRE filtrées
$flag_questionnaire = false;
while ($row = $stmt_questionnaire->fetch(PDO::FETCH_ASSOC)) {
    if (!$flag_questionnaire) {
        fputcsv($fp, array_keys($row), ';', '"'); // Écrit les en-têtes de colonnes
        $flag_questionnaire = true;
    }
    fputcsv($fp, $row, ';', '"'); // Écrit les lignes de données
}

fwrite($fp, "\n"); // Séparation entre les sectionsÒ

fclose($fp);
$pdo = null; // Fermer la connexion à la base de données
exit();
?>