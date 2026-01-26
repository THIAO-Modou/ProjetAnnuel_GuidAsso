<?php
session_start();

if (!isset($_SESSION['MAIL'])) {
    die("❌ Accès non autorisé.");
}

if (!isset($_GET['file'])) {
    die("❌ Aucun fichier spécifié.");
}

// Sécuriser le fichier (évite les hacks par inclusion)
$fileName = basename($_GET['file']);
$filePath = realpath(__DIR__ . '/../questionnaire/pjbn/' . $fileName); // 

// Vérifier si le fichier existe et est bien dans le bon dossier
if (!$filePath || !file_exists($filePath) || strpos($filePath, realpath(__DIR__ . '/../questionnaire/pjbn/')) !== 0) {
    die("❌ Fichier introuvable !");
}

// ✅ Gestion de l'aperçu : Ouvre directement dans le navigateur au lieu de télécharger
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
