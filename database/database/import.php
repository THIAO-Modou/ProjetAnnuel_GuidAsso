<?php
// Active l’affichage des erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/../config/BD.php';
include_once __DIR__ . '/../config/session.php';

if (!isset($_SESSION['MAIL'])) {
    header("Location: /../views/pageconnexion.php");
    exit();
}

// Vérifie que la connexion PDO est bien initialisée
if (!isset($pdo)) {
    die("Erreur : connexion à la base de données non établie.");
}

// Traitement du fichier CSV envoyé via formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    try {
        // Vérifie que le fichier a été bien transmis
        if ($_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erreur : fichier non envoyé.");
        }
        // Vérifie l’extension du fichier : doit être un .csv
        if (pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION) !== 'csv') {
            throw new Exception("Erreur : Format non supporté. Veuillez utiliser un fichier CSV.");
        }

        // Ouvre le fichier CSV en lecture
        $csvFile = fopen($_FILES['csv_file']['tmp_name'], 'r');
        if (!$csvFile) {
            throw new Exception("Erreur : impossible d'ouvrir le fichier CSV.");
        }

        // Récupère la première ligne comme noms de colonnes
        $columns = fgetcsv($csvFile, 1000, ';');
        if (!$columns) {
            throw new Exception("Erreur : fichier CSV vide ou mal formaté.");
        }

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
            'PJBLOCNOTE' => ':pj_bloc_note'
        ];

        $columnMap_contact = [
            'NOMCONTACT' => ':nom_contact',
            'PRENOMCONTACT' => ':prenom_contact',
            'CIVILITE' => ':civilite',
            'EMAILCORRESPONDANT' => ':email_correspondant'
        ];
        // Compteur de lignes insérées
        $insertedRows = 0;

        // Parcours ligne par ligne du fichier
        while (($row = fgetcsv($csvFile, 1000, ';')) !== false) {
            // Si la ligne est incomplète, on la complète avec des valeurs vides
            if (count($row) < count($columns)) {
                $row = array_pad($row, count($columns), '');
            }

            $data_contact = [];
            $data_questionnaire = [];

            // Remplit les tableaux de données avec les valeurs
            foreach ($columns as $index => $columnName) {
                if (isset($columnMap_contact[$columnName])) {
                    $data_contact[$columnMap_contact[$columnName]] = $row[$index] ?? "";
                }
                if (isset($columnMap_questionnaire[$columnName])) {
                    $data_questionnaire[$columnMap_questionnaire[$columnName]] = !empty($row[$index]) ? trim($row[$index]) : null;
                }
            }

            // Recherche du contact via l'email
            $stmt_contact = $pdo->prepare("SELECT IDCONTACT FROM CONTACT WHERE EMAILCORRESPONDANT = :email_correspondant");
            $email = $data_contact[':email_correspondant'] ?? null;
            $stmt_contact->execute([':email_correspondant' => $email]);

            $row_contact = $stmt_contact->fetch(PDO::FETCH_ASSOC);

            // Si le contact existe → on récupère son ID
            if ($row_contact) {
                $id_contact = $row_contact['IDCONTACT'];
            // Sinon → on insère le nouveau contact
            } else {
                $stmt_insert_contact = $pdo->prepare("INSERT INTO CONTACT (NOMCONTACT, PRENOMCONTACT, CIVILITE, EMAILCORRESPONDANT) 
                                                      VALUES (:nom_contact, :prenom_contact, :civilite, :email_correspondant)");
                $stmt_insert_contact->execute($data_contact);
                $id_contact = $pdo->lastInsertId();
            }
            // Ajout de l’ID du contact à la ligne questionnaire
            $data_questionnaire[':id_contact'] = $id_contact;

            // Conversion HORODATEUR
            if (!empty($data_questionnaire[':horodateur'])) {
                $date = str_replace('/', '-', $data_questionnaire[':horodateur']);
                $dateConvertie = date_create_from_format('d-m-Y H:i', $date) ?: date_create_from_format('Y-m-d H:i:s', $date);
                $data_questionnaire[':horodateur'] = $dateConvertie ? $dateConvertie->format('Y-m-d H:i:s') : date('Y-m-d H:i:s');
            } else {
                $data_questionnaire[':horodateur'] = date('Y-m-d H:i:s');
            }

            // Insertion dans la table QUESTIONNAIRE
            $columns_sql = array_keys($columnMap_questionnaire);
            $placeholders = array_map(function ($col) use ($columnMap_questionnaire) {
                return $columnMap_questionnaire[$col];
            }, $columns_sql);
            $columns_sql[] = 'IDCONTACT';
            $placeholders[] = ':id_contact';

            $query = "INSERT INTO QUESTIONNAIRE (" . implode(", ", $columns_sql) . ") 
                      VALUES (" . implode(", ", $placeholders) . ")";
            $stmt = $pdo->prepare($query);

            // Exécute l’insertion et incrémente le compteur de ligne
            try {
                $stmt->execute($data_questionnaire);
                $insertedRows++;
            } catch (PDOException $e) {
                error_log(" Erreur SQL : " . $e->getMessage());
            }
        }

        // On ferme le fichier après lecture
        fclose($csvFile);

        // Redirection vers la page d’admin
        $_SESSION['success_message'] = $insertedRows > 0 ? "Importation réussie ! $insertedRows lignes insérées." : "Erreur : Aucune ligne insérée.";
        header("Location: /../views/pageadmin.php");
        exit();

    } catch (Exception $e) {
        error_log(" Erreur : " . $e->getMessage());
        exit();
    }
}
?>
