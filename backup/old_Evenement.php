<?php
include_once __DIR__ . '/config/BD.php';
include_once __DIR__ . '/config/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['emargement'])) {
    // Répertoire où les fichiers seront enregistrés
    $uploadDir = 'uploads/'; 

    // Vérifiez que le répertoire existe, sinon le créer
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Récupérer le fichier téléchargé
    $uploadFile = $uploadDir . basename($_FILES['emargement']['name']);
    $fileType = pathinfo($uploadFile, PATHINFO_EXTENSION);

    // Vérification de l'extension
    $allowedTypes = ['pdf', 'doc', 'docx'];
    if (!in_array(strtolower($fileType), $allowedTypes)) {
        die("Erreur : Seuls les fichiers .pdf, .doc, et .docx sont autorisés.");
    }

    // Déplacement du fichier téléchargé vers le répertoire de destination
    if (move_uploaded_file($_FILES['emargement']['tmp_name'], $uploadFile)) {
        try {
            // Récupération des données du formulaire
            $evenement = $_POST['evenement'];
            $themes = $_POST['theme'];
            $atelier = $_POST['atelier'];
            $personne = $_POST['personne'];
            $Date = $_POST['Date'];
            $Lieu = $_POST['Lieu'];
            $CP = $_POST['CP'];
            $CDC = $_POST['CDC'];
            $m = $_POST['minutes'];
            $h = $_POST['heures'];
            $commune = $_POST['commune'];
            
            // Traitement des données du formulaire
            $theme = isset($themes) && is_array($themes) ? implode(", ", $themes) : "";
            $temps = $h * 60 + $m;
            $adresse_utilisateur = $_SESSION['MAIL'];

            // Construction de la requête SQL pour récupérer les données correspondant à la commune
            $requete_sql = "SELECT * FROM COMMUNE WHERE Commune = :commune";

            // Préparation de la requête SQL
            $stmt = $pdo->prepare($requete_sql);
            $stmt->bindParam(':commune', $commune);
            
            // Exécution de la requête SQL
            $stmt->execute();
            
            // Récupération des résultats
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Vérification si des données ont été trouvées
            if (count($resultat) > 0) {
                // Récupération des données de la commune
                $row = $resultat[0];
                $INSEE = $row['Code_INSEE'];
                $CP = $row['Code_Postal'];
                $Arrondissement = $row['Arrondissement'];
                $EPCI = $row['EPCI'];

                // Insertion de toutes les données dans la même requête
                $sql = "INSERT INTO QUESTIONNAIRE (NOMEVENEMENT, TITREEVENEMENT, THEMEGENERAL, AUDIENCE, DATEEVE, TEMPSPASSE, NBRDV, MAILGUIDASSO, COMMUNE, CODE_INSEE, CODE_POSTAL, ARRONDISSEMENT, EPCI, FILE) 
                        VALUES (:evenement, :atelier, :themes, :personne, :Date, :Temps, 1, :mail, :commune, :insee, :cp, :arr, :epci, :file)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':evenement', $evenement);
                $stmt->bindParam(':atelier', $atelier);
                $stmt->bindParam(':themes', $theme);
                $stmt->bindParam(':personne', $personne);
                $stmt->bindParam(':Date', $Date);
                $stmt->bindParam(':Temps', $temps);
                $stmt->bindParam(':mail', $adresse_utilisateur);
                $stmt->bindParam(':commune', $commune);
                $stmt->bindParam(':insee', $INSEE);
                $stmt->bindParam(':cp', $CP);
                $stmt->bindParam(':arr', $Arrondissement);
                $stmt->bindParam(':epci', $EPCI);
                $stmt->bindParam(':file', $uploadFile);

                if ($stmt->execute()) {
                    header("Location: questionnaire.php");
                    echo "Les données ont été enregistrées avec succès.";
                } else {
                    echo "Erreur : Les données n'ont pas pu être enregistrées.";
                }
            } else {
                echo "Aucune donnée correspondante trouvée pour la commune : $commune";
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }

        // Fermeture de la connexion à la base de données
        $pdo = null;
    } else {
        echo "Erreur : Impossible de télécharger le fichier.";
    }
} else {
    echo "Erreur : Aucun fichier reçu.";
}
?>
