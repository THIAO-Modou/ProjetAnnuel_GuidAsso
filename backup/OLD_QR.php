<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/../config/BD.php';
include_once __DIR__ . '/../config/session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($pdo)) {
            throw new Exception("Erreur de connexion à la base de données.");
        }

        $adresse_utilisateur = $_SESSION['MAIL'] ?? 'inconnu';

        // Vérifier les champs obligatoires
        if (empty($_POST['type']) || empty($_POST['themeG']) || empty($_POST['assoc']) || empty($_POST['Reponsepartagee'])) {
            throw new Exception("Tous les champs obligatoires doivent être remplis.");
        }

        // Récupération des valeurs
        $type = $_POST['type'];
        $themeG = $_POST['themeG'];
        $autrethemes = $_POST['theme'] ?? [];
        $reponse = $_POST['rep'] ?? '';
        $nom_association = $_POST['assoc'];
        $CDC = $_POST['CDC'] ?? null;
        $CP = $_POST['CP'] ?? null;
        $activite = $_POST['activites'];
        $activiteSec = $_POST['act_sec'] ?? [];
        $dossierTC = $_POST['Reponsepartagee'];
        $autreT = $_POST['autreTheme'] ?? null;
        $autreChamp = $_POST['autreChamp'] ?? null;
        $commune = $_POST['commune'] ?? null;

        // Gestion des activités secondaires et thèmes multiples
        $actS = is_array($activiteSec) ? implode(", ", $activiteSec) : "";
        $theme = is_array($autrethemes) ? implode(", ", $autrethemes) : "";
        if (!empty($autreChamp)) {
            $theme .= !empty($theme) ? ", " . $autreChamp : $autreChamp;
        }
        if ($themeG === "Autre") {
            $themeG = $autreT;
        }

        // AJOUT "HORS DEPARTEMENT"
        if (isset($_POST['horsDepars département"rtementCheckbox']) && $_POST['horsDepartementCheckbox'] === "on") {
            $commune = "Hors département";
            $INSEE = 0;
            $CP = $Arrondissement = $EPCI = null;
        } else { // FIN AJOUT HORS DEPARTEMENT
            
            // Vérification de la commune
            $stmt = $pdo->prepare("SELECT * FROM COMMUNE WHERE Commune = :commune");
            $stmt->bindParam(':commune', $commune, PDO::PARAM_STR);
            $stmt->execute();
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$resultat) {
                throw new Exception("Aucune donnée trouvée pour la commune : $commune");
            }

            $INSEE = $resultat['Code_INSEE'];
            $CP = $resultat['Code_Postal'];
            $Arrondissement = $resultat['Arrondissement'];
            $EPCI = $resultat['EPCI'];
        }

        // Vérification si l'association existe
        $stmt_assoc = $pdo->prepare("SELECT NOMASSO FROM ASSOCIATION WHERE NOMASSO = :nom_association");
        $stmt_assoc->bindParam(':nom_association', $nom_association);
        $stmt_assoc->execute();
        $row_assoc = $stmt_assoc->fetch(PDO::FETCH_ASSOC);

        if (!$row_assoc) {
            // Insérer l'association si elle n'existe pas
            $stmt_insert_assoc = $pdo->prepare("INSERT INTO ASSOCIATION (NOMASSO, CODEPOSTAL, EPCI, ACTIVITEPRINCIPALEASSO, ACTIVITESECONDAIRE)
                                                VALUES (:nom_association, :cp, :epci, :activite, :activiteSec)");
            $stmt_insert_assoc->bindParam(':nom_association', $nom_association);
            $stmt_insert_assoc->bindParam(':cp', $CP);
            $stmt_insert_assoc->bindParam(':epci', $EPCI);
            $stmt_insert_assoc->bindParam(':activite', $activite);
            $stmt_insert_assoc->bindParam(':activiteSec', $actS);
            $stmt_insert_assoc->execute();
        }

        // Insérer les données dans le questionnaire
        $stmt = $pdo->prepare("INSERT INTO QUESTIONNAIRE (NOMASSO, ACTIVITEPRINCIPALEASSO, ACTIVITESECONDAIRE, NATUREECHANGE, THEMEGENERAL, AUTRETHEMATIQUE, REPONSE, TRANSMISA, MAILGUIDASSO, NBRDV, COMMUNE, CODE_INSEE, CODE_POSTAL, ARRONDISSEMENT, EPCI) 
                                VALUES (:nom_association, :activite, :activiteSec, :type, :themeG, :theme, :reponse, :dossierTC, :mail, 1, :commune, :insee, :cp, :arr, :epci)");

        $stmt->bindParam(':nom_association', $nom_association);
        $stmt->bindParam(':activite', $activite);
        $stmt->bindParam(':activiteSec', $actS);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':themeG', $themeG);
        $stmt->bindParam(':theme', $theme);
        $stmt->bindParam(':reponse', $reponse);
        $stmt->bindParam(':dossierTC', $dossierTC);
        $stmt->bindParam(':mail', $adresse_utilisateur);
        $stmt->bindParam(':commune', $commune);
        $stmt->bindParam(':insee', $INSEE, PDO::PARAM_INT);
        $stmt->bindParam(':cp', $CP, PDO::PARAM_INT);
        $stmt->bindParam(':arr', $Arrondissement, PDO::PARAM_INT);
        $stmt->bindParam(':epci', $EPCI, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: /../questionnaire_test_laetitia.php");
            exit();
        } else {
            throw new Exception("Erreur : Les données n'ont pas pu être enregistrées.");
        }
    } catch (Exception $e) {
        echo "<p style='color:red;'>Erreur : " . $e->getMessage() . "</p>";
    }
}
?>
