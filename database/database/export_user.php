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

if ($user) {
    $email = $user['MAIL'];
    $nom = $user['NOMPERSONNE'];
    $prenom = $user['PRENOMPERSONNE'];
    $idFonction = $user['IDFONCTION'];
    $classification = $user['CLASSIFICATION'];
} else {
    $prenom = "Utilisateur inconnu";
}

// Vérifier si la connexion à la base de données existe
if (!isset($pdo)) {
    die("Erreur : connexion à la base de données non établie.");
}

// Définir les en-têtes HTTP pour le téléchargement du fichier CSV
$filename = "base_de_donnees_GuidAsso.csv";
header('Content-Encoding: UTF-8');
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
echo "\xEF\xBB\xBF"; // Ajoute BOM UTF-8 pour éviter les problèmes d'encodage sous Excel

$fp = fopen('php://output', 'w');

// Récupérer les données des tables QUESTIONNAIRE et CONTACT en fonction des profils

$query_questionnaire = "SELECT QUESTIONNAIRE.*, CONTACT.* 
                        FROM QUESTIONNAIRE 
                        LEFT JOIN CONTACT ON QUESTIONNAIRE.IDCONTACT = CONTACT.IDCONTACT 
                        ORDER BY QUESTIONNAIRE.HORODATEUR ASC;";
$result_questionnaire = $pdo->query($query_questionnaire);

// Écriture des données de la table QUESTIONNAIRE
$flag_questionnaire = false;
while ($row = $result_questionnaire->fetch(PDO::FETCH_ASSOC)) {
    if (!$flag_questionnaire) {
        fputcsv($fp, array_keys($row), ';', '"'); // Écrit les en-têtes de colonnes
        $flag_questionnaire = true;
    }
    fputcsv($fp, $row, ';', '"'); // Écrit les lignes de données
}

fwrite($fp, "\n"); // Séparation entre les sections

// Récupérer les données de la table CONTACT indépendamment (si nécessaire)
$query_contact = "SELECT * FROM CONTACT;";
$result_contact = $pdo->query($query_contact);

// Écriture des données de la table CONTACT
$flag_contact = false;
while ($row = $result_contact->fetch(PDO::FETCH_ASSOC)) {
    if (!$flag_contact) {
        fputcsv($fp, array_keys($row), ';', '"'); // Écrit les en-têtes de colonnes
        $flag_contact = true;
    }
    fputcsv($fp, $row, ';', '"'); // Écrit les lignes de données
}

fclose($fp);
$pdo = null; // Fermer la connexion à la base de données
exit();
?>
