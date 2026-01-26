<?php
session_start();

// Vérifier la session de l'utilisateur
if (!isset($_SESSION['MAIL'])) {
    die(" Accès non autorisé.");
}

// Verifier s'il y a un fichier 
if (!isset($_GET['file'])) {
    die(" Aucun fichier spécifié.");
}

// Sécuriser le fichier (évite les hacks par inclusion)
$fileName = basename($_GET['file']);
// $filePath = realpath(__DIR__ . '/../controllers/questionnaire/uploads/' . $fileName); // 🔹 
$expectedDir = realpath(__DIR__ . '/../questionnaire/uploads/');
if (!$filePath || strpos($filePath, $expectedDir) !== 0 || !file_exists($filePath)) {
    die(" Fichier introuvable !");
}

// Vérifier si le fichier existe et est bien dans le bon dossier
if (!$filePath || !file_exists($filePath) || strpos($filePath, realpath(__DIR__ . '/../controllers/questionnaire/uploads/')) !== 0) {
    die(" Fichier introuvable !");
}

//  Gestion de l'aperçu : Ouvre directement dans le navigateur au lieu de télécharger
if (isset($_GET['preview'])) {
    $mimeType = mime_content_type($filePath);
    header("Content-Type: $mimeType");
    header("Content-Disposition: inline; filename=\"$fileName\""); // INLINE = affichage
    readfile($filePath);
    exit();
}

// ✅ Téléchargement forcé 
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $fileName . '"'); 
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));
flush();
readfile($filePath);
exit();
