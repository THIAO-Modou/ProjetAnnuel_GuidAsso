<?php
// 🔔 Affichage des erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/../config/BD.php';
include_once __DIR__ . '/../config/session.php';

// Vérifie session utilisateur
if (!isset($_SESSION['MAIL'])) {
    header("Location: /CRAIG86/views/pageconnexion.php");
    exit();
}

// Vérifie connexion PDO
if (!isset($pdo)) {
    die("Erreur : connexion à la base de données non établie.");
}

// Traitement du fichier CSV envoyé via formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    try {
        // 🔎 Vérifie transmission du fichier
        if ($_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erreur : fichier non envoyé.");
        }

        // Vérifie extension CSV
        if (pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION) !== 'csv') {
            throw new Exception("Erreur : Format non supporté. Veuillez utiliser un fichier CSV.");
        }

        // Ouvre le fichier en lecture
        $csvFile = fopen($_FILES['csv_file']['tmp_name'], 'r');
        if (!$csvFile) {
            throw new Exception("Erreur : impossible d'ouvrir le fichier CSV.");
        }

        // Récupère les noms de colonnes
        $columns = fgetcsv($csvFile, 1000, ';');
        if (!$columns) {
            throw new Exception("Erreur : fichier CSV vide ou mal formaté.");
        }

        //  Mappage des colonnes
        // Mappings des colonnes
        $columnMap_questionnaire = [
            'HORODATEUR' => ':horodateur',
            'NOMASSO' => ':nom_association',
            'MAILGUIDASSO' => ':mail_guidasso',
            'COMMUNE' => ':commune',
            'CODE_INSEE' => ':code_insee',
            'CODE_POSTAL' => ':code_postal',
            'ARRONDISSEMENT' => ':arrondissement',
            'EPCI' => ':epci',
            'ACTIVITEPRINCIPALEASSO' => ':activite_principale',
            'ACTIVITESECONDAIRE' => ':activite_secondaire',
            'CLASSIFICATIONGUIDASSO' => ':classification',
            'NATUREECHANGE' => ':nature_echange',
            'THEMEGENERAL' => ':theme_general',
            'TRANSMISPAR' => ':dossier_transmis_par',
            'TRANSMISA' => ':dossier_transmis_a',
            'NBRDV' => ':nombre_rdv',
            'TEMPSPASSE' => ':temps_passe',
            'RECHERCHE' => ':recherche',
            'AUTRETHEMATIQUE' => ':autre_thematique',
            'QUESTION' => ':question',
            'REPONSE' => ':reponse',
            'PERMANENCE' => ':permanence',
            'RESSOURCE' => ':ressource',
            'NOMEVENEMENT' => ':nom_evenement',
            'TITREEVENEMENT' => ':titre_evenement',
            'AUDIENCE' => ':audience',
            'DATEEVE' => ':date_evenement',
            'FILE' => ':fichier_joint',
            'NOMSTRUCTURE' => ':nom_structure',
            'ACTIVITESTRUCTURE' => ':activite_structure',
            'BLOCNOTE' => ':bloc_note',
            'PJBLOCNOTE' => ':pj_bloc_note',
            'TYPEQUESTIONNAIRE' => ':typequestionnaire',
            'EMPLOYEUR' => ':employeur',
            'POURQUI' => ':pourqui',

        ];

        $columnMap_contact = [
            'NOMCONTACT' => ':nom_contact',
            'PRENOMCONTACT' => ':prenom_contact',
            'CIVILITE' => ':civilite',
            'EMAILCORRESPONDANT' => ':email_correspondant'
        ];

        $insertedRows = 0;
        $row = fgetcsv($csvFile, 1000, ';', '"', '\\');


        //  Parcours des lignes CSV
        while (($row = fgetcsv($csvFile, 1000, ';', '"', '\\')) !== false) {
            if (count($row) < count($columns)) {
                $row = array_pad($row, count($columns), '');
            }

            $data_contact = [];
            $data_questionnaire = [];

            foreach ($columns as $index => $columnName) {
                if (isset($columnMap_contact[$columnName])) {
                    $data_contact[$columnMap_contact[$columnName]] = $row[$index] ?? '';
                }
                if (isset($columnMap_questionnaire[$columnName])) {
                    $data_questionnaire[$columnMap_questionnaire[$columnName]] = !empty($row[$index]) ? trim($row[$index]) : null;
                }
            }

            //  Recherche du contact via email
            $stmt_contact = $pdo->prepare("SELECT IDCONTACT FROM CONTACT WHERE EMAILCORRESPONDANT = :email_correspondant");
            $email = $data_contact[':email_correspondant'] ?? null;
            $stmt_contact->execute([':email_correspondant' => $email]);

            $row_contact = $stmt_contact->fetch(PDO::FETCH_ASSOC);

            if ($row_contact) {
                $id_contact = $row_contact['IDCONTACT'];
            } else {
                //  Insertion nouveau contact
                $stmt_insert_contact = $pdo->prepare("INSERT INTO CONTACT (NOMCONTACT, PRENOMCONTACT, CIVILITE, EMAILCORRESPONDANT)
                                                      VALUES (:nom_contact, :prenom_contact, :civilite, :email_correspondant)");
                $stmt_insert_contact->execute($data_contact);
                $id_contact = $pdo->lastInsertId();
            }

            //  Ajout IDCONTACT dans données questionnaire
            $data_questionnaire[':id_contact'] = $id_contact;

            //  Traitement horodateur
            if (!empty($data_questionnaire[':horodateur'])) {
                $originalDate = str_replace('/', '-', trim($data_questionnaire[':horodateur']));
                $formats = ['Y-m-d H:i:s', 'Y-m-d H:i', 'd-m-Y H:i', 'd-m-Y H:i:s', 'Y/m/d H:i:s'];
                $dateConvertie = null;
                foreach ($formats as $format) {
                    $dateConvertie = date_create_from_format($format, $originalDate);
                    if ($dateConvertie) break;
                }
                $data_questionnaire[':horodateur'] = $dateConvertie
                    ? $dateConvertie->format('Y-m-d H:i:s')
                    : date('Y-m-d H:i:s');
            } else {
                $data_questionnaire[':horodateur'] = date('Y-m-d H:i:s');
            }

            //  Construction SQL
            $columns_sql = [];
            $placeholders = [];
            foreach ($columns as $colName) {
                if (isset($columnMap_questionnaire[$colName])) {
                    $columns_sql[] = $colName;
                    $placeholders[] = $columnMap_questionnaire[$colName];
                }
            }

            $columns_sql[] = 'IDCONTACT';            //  Ajoute colonne manuellement
            $placeholders[] = ':id_contact';         //  Ajoute valeur correspondante

            // Ignore si ligne vide
            $temp = $data_questionnaire;
            unset($temp[':id_contact']);
            if (count(array_filter($temp)) === 0) {
                error_log(" Ligne ignorée car vide");
                continue;
            }

            $query = "INSERT INTO QUESTIONNAIRE (" . implode(', ', $columns_sql) . ") 
                      VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $pdo->prepare($query);

            try {
                $stmt->execute($data_questionnaire);
                $insertedRows++;
                error_log(" Ligne insérée avec succès");
            } catch (PDOException $e) {
                error_log(" Échec insertion ligne : " . $e->getMessage());
            }
        }

        fclose($csvFile);

        $_SESSION['success_message'] = $insertedRows > 0
            ? "Importation réussie ! $insertedRows lignes insérées."
            : "Erreur : Aucune ligne insérée.";
        header("Location: /CRAIG86/views/pageadmin.php");
        exit();

    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
        exit();
    }
}
?>
