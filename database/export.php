<?php

session_start(); 

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclure la connexion à la base de données car il est dans un fichier avant 
include_once __DIR__ . '/../config/BD.php';

if (!isset($_SESSION['MAIL'])) {
    header("Location: /../views/pageconnexion.php");
    exit();
}
$user = getUserInfoByEmail($_SESSION['MAIL']);
$email = $_SESSION['MAIL'];

if ($user) {
    $idFonction = $user['IDFONCTION'];
} else {
    $prenom = "Utilisateur inconnu";
}

// Vérifier si la connexion à la base de données existe
if (!isset($pdo)) {
    die("Erreur : connexion à la base de données non établie.");
}
//Gestion de l'intervalle d'exportation 
$dateDebut = $_GET['start'] ?? null;
$dateFin = $_GET['end'] ?? null;

// Ajouter les heures par défaut si la date est en format pur YYYY-MM-DD
if (!empty($dateDebut) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateDebut)) {
    $dateDebut .= ' 00:00:00';// début de la journée
}

if (!empty($dateFin) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFin)) {
    $dateFin .= ' 23:59:59'; // fin de la journée incluse
}

// Récupérer les bornes extrêmes dans la table
$stmtMinMax = $pdo->query("SELECT MIN(HORODATEUR) AS minDate, MAX(HORODATEUR) AS maxDate FROM QUESTIONNAIRE");
$dates = $stmtMinMax->fetch(PDO::FETCH_ASSOC);
$minDate = $dates['minDate'];
$maxDate = $dates['maxDate'];

// Appliquer la logique de remplissage simple intelligent
if (!$dateDebut && $dateFin)     $dateDebut = $minDate;
if ($dateDebut && !$dateFin)     $dateFin = $maxDate;
if (!$dateDebut && !$dateFin)    { $dateDebut = $minDate; $dateFin = $maxDate; }

//echo($dateDebut);
//echo($dateFin);


// Définir les en-têtes HTTP pour le téléchargement du fichier CSV
$filename = "base_de_donnees_GuidAsso.csv";
header('Content-Encoding: UTF-8');
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
echo "\xEF\xBB\xBF"; // Ajoute BOM UTF-8 pour éviter les problèmes d'encodage sous Excel

$fp = fopen('php://output', 'w');

// Récupérer les données des tables QUESTIONNAIRE et CONTACT
$query_questionnaire = "SELECT QUESTIONNAIRE.*, CONTACT.* 
    FROM QUESTIONNAIRE 
    LEFT JOIN CONTACT ON QUESTIONNAIRE.IDCONTACT = CONTACT.IDCONTACT
    WHERE QUESTIONNAIRE.HORODATEUR BETWEEN :start AND :end";

// Ajouter filtre MAIL si nécessaire
if ($idFonction == 1) {
    $query_questionnaire .= " AND QUESTIONNAIRE.MAILGUIDASSO = :email";
}
$query_questionnaire .= " ORDER BY QUESTIONNAIRE.HORODATEUR ASC";

$stmt = $pdo->prepare($query_questionnaire);
$stmt->bindValue(':start', $dateDebut);
$stmt->bindValue(':end', $dateFin);

if ($idFonction == 1) {
    $stmt->bindValue(':email', $email);
}
$stmt->execute();

// Écriture des données de la table QUESTIONNAIRE
$flag_questionnaire = false;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (!$flag_questionnaire) {
        fputcsv($fp, array_keys($row), ';', '"', '\\'); // Écrit les en-têtes de colonnes
        $flag_questionnaire = true;
    }
    fputcsv($fp, $row, ';', '"', '\\'); // Écrit les lignes de données
}

fwrite($fp, "\n"); // Séparation entre les sections

fclose($fp);
$pdo = null; // Fermer la connexion à la base de données
exit();
?>
