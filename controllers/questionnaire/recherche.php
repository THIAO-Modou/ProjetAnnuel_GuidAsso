<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/../../config/BD.php';
include_once __DIR__ . '/../../config/session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $adresse_utilisateur = $_SESSION['MAIL'] ?? 'inconnu';

    ////////////////////////////////////////////////////// POUR PIECE JOINTE DE BLOC NOTE

    // Vérification si un fichier a été téléchargé
    if (isset($_FILES['PJ']) && $_FILES['PJ']['error'] === UPLOAD_ERR_OK) {
    
        // Répertoire de stockage des fichiers
        $bndir = 'pjbn/';

        // Vérifiez que le répertoire existe, sinon le créer
        if (!is_dir($bndir)) {
            mkdir($bndir, 0777, true);
        }

        // Récupérer le fichier téléchargé
        $PJ = $bndir . basename($_FILES['PJ']['name']);
        $fileType = pathinfo($PJ, PATHINFO_EXTENSION);

        // Vérification de l'extension du fichier
        $allowedTypes = ['pdf', 'doc', 'docx'];
        if (!in_array(strtolower($fileType), $allowedTypes)) {
            throw new Exception("Erreur : Seuls les fichiers .pdf, .doc, et .docx sont autorisés.");
        }

        // Déplacement du fichier téléchargé
        if (!move_uploaded_file($_FILES['PJ']['tmp_name'], $PJ)) {
            throw new Exception("Erreur : Impossible de télécharger le fichier.");
        }
    }

    // Si aucun fichier n'est envoyé, le script continue sans erreur.

        // Vérification des champs obligatoires
        if (empty($_POST['classification']) || empty($_POST['themeG'])) {
            throw new Exception("Certains champs obligatoires sont manquants.");
        }

        // Récupération des données du formulaire
        $classification = $_POST['classification'];
        $themeG = $_POST['themeG'];
        $themes = $_POST['theme'] ?? [];
        $ressources = $_POST['ressources'] ?? [];
        $reponse = $_POST['reponse'];
        $dossierprovenantde = $_POST['RecherchepartageeR'];
        $dossiertransmisa = $_POST['RecherchepartageeR1'];
        $autreT = $_POST['autreTheme'] ?? null;
        $autreChamp2 = $_POST['autreChamp2'] ?? null;
        $m = $_POST['minutes'] ?? 0;
        $h = $_POST['heures'] ?? 0;
        $BN = $_POST['BN'] ?? '';

        // Traitement des données
        $theme = !empty($themes) ? implode(", ", $themes) : "";
        
        if (empty($_POST['ReponsepartageeRDV'])) {
            $missingFields[] = "Provenant de";
        }
        if (empty($_POST['ReponsepartageeRDV1'])) {
            $missingFields[] = "Partagé avec";
        }

        $ressource = !empty($ressources) ? implode(", ", $ressources) : "";

        if ($themeG == "Autre") {
            $themeG = $autreT;
        }
        
        $h = !empty($h) ? (int)$h : 0; // Convertit en entier, ou 0 si vide/null
        $m = (int)$m; // Assure que $m est un entier
        
        $temps = ($h * 60) + $m; // Conversion en minutes

        // Insertion dans la base de données
        $sql = "INSERT INTO QUESTIONNAIRE (CLASSIFICATIONGUIDASSO, THEMEGENERAL, AUTRETHEMATIQUE, RESSOURCE, TEMPSPASSE, REPONSE, TRANSMISPAR, TRANSMISA, MAILGUIDASSO, NBRDV, BLOCNOTE, PJBLOCNOTE) 
                VALUES (:classification, :themeG, :theme, :ressources, :temps, :reponse, :dossierpartage,:dossiertransmis, :mail, 1, :BN, :PJ)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':classification', $classification);
        $stmt->bindParam(':themeG', $themeG);
        $stmt->bindParam(':theme', $theme);
        $stmt->bindParam(':ressources', $ressource);
        $stmt->bindParam(':temps', $temps);
        $stmt->bindParam(':reponse', $reponse);
        $stmt->bindParam(':dossierpartage', $dossierprovenantde);
        $stmt->bindParam(':dossiertransmis', $dossiertransmisa);
        $stmt->bindParam(':mail', $adresse_utilisateur);
        $stmt->bindParam(':BN', $BN);
        $stmt->bindParam(':PJ', $PJ);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Les données ont été enregistrées avec succès !";
            $_SESSION['show_form'] = "recherche"; // Afficher le bon formulaire
        } else {
            throw new Exception("Erreur : Les données n'ont pas pu être enregistrées.");
        }
    } catch (Exception $e) {
        $_SESSION['error_message_recherche'] = $e->getMessage();
        $_SESSION['show_form'] = "recherche"; // Afficher le bon formulaire en cas d'erreur
    }

    // Redirection propre
    header("Location: /../../views/questionnaire.php");
    exit();
}
?>
