<?php
session_start();
        
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/../../config/BD.php';
//include_once __DIR__ . '/../../config/session.php';

// Récupération des valeurs des boutons
$RDV = $_SESSION['RDV'] ?? false;
$reseau = $_SESSION['reseau'] ?? false;
$QR = $_SESSION['QR'] ?? false;
$longsuivi = $_SESSION['longsuivi'] ?? false;
$evenementForm = $_SESSION['evenement'] ?? false;
$rechercheForm = $_SESSION['recherche'] ?? false;
$anonyme = $_SESSION['anonyme'] ?? false;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    try {
        
        $adresse_utilisateur = $_SESSION['MAIL'] ?? 'inconnu';

        ////////////////////////////// POUR PIECE JOINTE EMARGEMENT ////////////////////////
        $uploadFile = null;
        // Vérifier si un fichier a été envoyé
        if (isset($_FILES['emargement']) && $_FILES['emargement']['error'] !== UPLOAD_ERR_NO_FILE) {
            // Vérification des erreurs de téléchargement
            if ($_FILES['emargement']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Erreur : Problème lors du téléchargement du fichier.");
            }

            // Répertoire de stockage des fichiers
            $uploadDir = 'uploads/';

            // Vérifiez que le répertoire existe, sinon le créer
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Récupérer le fichier téléchargé
            $uploadFile = $uploadDir . basename($_FILES['emargement']['name']);
            $fileType = pathinfo($uploadFile, PATHINFO_EXTENSION);

            // Vérification de l'extension du fichier
            $allowedTypes = ['pdf', 'doc', 'docx'];
            if (!in_array(strtolower($fileType), $allowedTypes)) {
                throw new Exception("Erreur : Seuls les fichiers .pdf, .doc et .docx sont autorisés.");
            }

            // Déplacement du fichier téléchargé
            if (!move_uploaded_file($_FILES['emargement']['tmp_name'], $uploadFile)) {
                throw new Exception("Erreur : Impossible de télécharger le fichier.");
            }

            // Si le fichier est bien enregistré, on peut stocker son chemin en base de données si nécessaire
            $fichierEmargement = $uploadFile;
        } else {
            // Aucune erreur, mais aucun fichier n'a été envoyé : c'est facultatif
            $fichierEmargement = null;
        }

        ////////////////////////////////////////////////////// POUR PIECE JOINTE ET BLOC NOTE
        
        $PJ = null;

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
            $allowedTypes = ['pdf', 'doc', 'docx', 'zip', 'xlsx', 'xls', 'rar'];
            if (!in_array(strtolower($fileType), $allowedTypes)) {
                throw new Exception("Erreur : Seuls les fichiers .pdf, .doc, .docx, .zip, .xlsx, .xls et .rar sont autorisés.");
            }

            // Déplacement du fichier téléchargé
            if (!move_uploaded_file($_FILES['PJ']['tmp_name'], $PJ)) {
                throw new Exception("Erreur : Impossible de télécharger le fichier.");
            }
        }

        // Si aucun fichier n'est envoyé, le script continue sans erreur.

        // Vérification des champs obligatoires

        // Récupération des données du formulaire
        $type = $_POST['type'] ?? '';
        $type = $_POST['type_radio'] ?? '';
        $evenement = $_POST['evenement'] ?? '';
        $titre_ev = $_POST['titre_ev'] ?? '';
        $personne = $_POST['personne'] ?? 0;
        $Date = $_POST['Date'] ?? '';
        $type = $_POST['structure'] ?? '';
        $structure = $_POST['structure'] ?? '';
        $classification = $_POST['classification'] ?? '';
        $assoc = trim($_POST['assoc'] ?? '');
        $activite_principale = $_POST['act_principale'] ?? '';
        $activiteSec = $_POST['act_sec'] ?? [];
        $nom_contact = $_POST['nom_contact'] ?? '';
        $prenom_contact = $_POST['prenom_contact'] ?? '';
        $horsDep = $_POST['activite'] ?? '';
        $employeur = $_POST['employeur'] ?? '';
        $horsDep = $_POST['mail'] ?? null;
        $civilite = $_POST['genre'] ?? '';
        $themeG = $_POST['themeG'] ?? '';
        $ressources = $_POST['ressources'] ?? [];
        $themes = $_POST['theme'] ?? [];
        $horsDep = $_POST['autreChamp'] ?? '';
        $dossierprovenantde = $_POST['Reponsepartagee'] ?? '';
        $dossiertransmisa = $_POST['Reponsepartagee1'] ?? '';
        $temps = $_POST['Temps'] ?? 00;
        $recherche = $_POST['recherche'] ?? null;
        $occurence = $_POST['occurence'] ?? 0;
        $autreT = $_POST['autreTheme'] ?? '';
        $autreChamp = $_POST['autreChamp3'] ?? '';
        $commune = $_POST['commune'] ?? '';
        $projet_asso = $_POST['projetAssoCheckbox'] ?? '';
        $non_comm = $_POST['nonCommuniqueCheckbox'] ?? '';
        $BN = $_POST['BN'] ?? '';
        $horsDep = $_POST['horsDepartementCheckbox'] ?? '';
        $numeroDepartement = $_POST['numeroDepartement'] ?? '';
        $reponse = $_POST['reponse'] ?? ''; 
        $question = $_POST['question'] ?? '';
        $permanence = $_POST['permanence'] ?? null;
        $m = $_POST['minutes'] ?? 0;
        $h = $_POST['heures'] ?? 0;
       
        $CDC = $_POST['CDC'] ?? null;
        $CP = $_POST['CP'] ?? null;
        $INSEE = "";
        $CP = "";
        $Arrondissement = "";
        $EPCI = "";

        // Type questionnaire remplie
        $TypeQuestionnaire = '';
        if($QR) $TypeQuestionnaire ='QR'; 
        if($RDV) $TypeQuestionnaire = 'RV';
        if($reseau) $TypeQuestionnaire ='RS';
        if($longsuivi) $TypeQuestionnaire = 'LS';
        if($anonyme) $TypeQuestionnaire ='AN';
        if($rechercheForm) $TypeQuestionnaire ='RR'; 
        if($evenementForm) $TypeQuestionnaire = 'EV';


        // Gestion des activités secondaires et thèmes multiples pour RDV
        $actS = is_array($activiteSec) ? implode(", ", $activiteSec) : "";
        $theme = is_array($themes) ? implode(", ", $themes) : "";
        if (!empty($autreChamp)) {
            $theme .= !empty($theme) ? ", " . $autreChamp : $autreChamp;
        }
        if ($themeG === "Autre") {
            $themeG = $autreT;
        }


        // Traitement des données pour stockage
        $theme = !empty($themes) ? implode(", ", $themes) : "";
        if (!empty($autreChamp3)) {
            $theme .= !empty($theme) ? ", " . $autreChamp3 : $autreChamp3;
        }

        if (empty($_POST['ReponsepartageeLS'])) {
            $missingFields[] = "Provenant de";
        }
        if (empty($_POST['ReponsepartageeLS1'])) {
            $missingFields[] = "Partagé avec";
        }

        if ($themeG == "Autre") {
            $themeG = $autreT;
        }
        
        $nom_association='';
        if($RDV || $QR || $longsuivi){
            //Gestion du nom de l'association en cas de projet d'association ou de nom non communiqué
            $choix = $_POST['choixAsso'] ?? null;
            if (!empty($assoc) && !$choix) {
                $nom_association = $assoc;
            } elseif (empty($assoc) && $choix === "projet") {
                $nom_association = "Projet d'association";
            } elseif (empty($assoc) && $choix === "noncommunique") {
                $nom_association = "Nom non communiqué";
            }
        }else $nom_association = $assoc;
    

        $h = !empty($h) ? (int)$h : 0; // Convertit en entier, ou 0 si vide/null
        $m = (int)$m; // Assure que $m est un entier
        $temps = ($h * 60) + $m; // Conversion en minutes

        //Gerer type si longsuivi
        if(!$QR){
            $actS = !empty($activiteSec) ? implode(", ", $activiteSec) : "";
            $typeS = is_array($type) ? implode(", ", $type) : "";

            // Vérification ou insertion du contact
            $sql_contact = "SELECT IDCONTACT FROM CONTACT WHERE NOMCONTACT = :nom_contact AND PRENOMCONTACT = :prenom_contact";
            $stmt_contact = $pdo->prepare($sql_contact);
            $stmt_contact->execute([':nom_contact' => $nom_contact, ':prenom_contact' => $prenom_contact]);
            $row_contact = $stmt_contact->fetch(PDO::FETCH_ASSOC);

            if ($row_contact) {
                $id_contact = $row_contact['IDCONTACT'];
            } else {
                $sql_insert_contact = "INSERT INTO CONTACT (NOMCONTACT, PRENOMCONTACT, CIVILITE) VALUES (:nom_contact, :prenom_contact, :civilite)";
                $stmt_insert_contact = $pdo->prepare($sql_insert_contact);
                $stmt_insert_contact->execute([
                    ':nom_contact' => $nom_contact,
                    ':prenom_contact' => $prenom_contact,
                    ':civilite' => $civilite
                ]);
                $id_contact = $pdo->lastInsertId();
            }
        } else{
            $sql_insert_contact = "INSERT INTO CONTACT (NOMCONTACT, PRENOMCONTACT, CIVILITE) VALUES (:nom_contact, :prenom_contact, :civilite)";
                $stmt_insert_contact = $pdo->prepare($sql_insert_contact);
                $stmt_insert_contact->execute([
                    ':nom_contact' => "", ':prenom_contact' => "", ':civilite' => ""
                ]);
                $id_contact = $pdo->lastInsertId();
        }
        
        // Vérification si "Hors département" est coché
       // On récupère la valeur de la commune et de la case cochée
       
        $horsDept = isset($_POST['horsDepartementCheckbox']) && $_POST['horsDepartementCheckbox'] === "on";

        // Validation : un seul des deux doit être rempli
        if($longsuivi || $RDV || $reseau || $anonyme || $evenementForm){
            if (empty($commune) && !$horsDept) {
                throw new Exception("Veuillez renseigner une commune ou cocher 'Hors département'.");
            }
            // Cas : Hors département
            elseif ($horsDept) {
                $commune = "Hors département";
                $INSEE = 0;
                $CP = $Arrondissement = $EPCI = "N/A";
            }
            // Cas : commune saisie
            else {
               // Requête de récupération des infos
                $stmt = $pdo->prepare("SELECT * FROM COMMUNE WHERE Commune = :commune");
                $stmt->execute([':commune' => $commune]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$result) {
                    throw new Exception("Commune inconnue : $commune.");
                }
                $INSEE = $result['Code_INSEE'];
                $CP = $result['Code_Postal'];
                $Arrondissement = $result['Arrondissement'];
                $EPCI = $result['EPCI'];
            }           
        }


        // Vérification ou insertion de l'association
        $stmt_assoc = $pdo->prepare("SELECT NOMASSO FROM ASSOCIATION WHERE NOMASSO = :nom_association");
        $stmt_assoc->execute([':nom_association' => $nom_association]);
        $row_assoc = $stmt_assoc->fetch(PDO::FETCH_ASSOC);

        if (!$row_assoc) {
            $sql_insert_assoc = "INSERT INTO ASSOCIATION (NOMASSO, CODEPOSTAL, EPCI, ACTIVITEPRINCIPALEASSO, ACTIVITESECONDAIRE) 
                                VALUES (:nom_association, :cp, :epci, :activite, :activiteSec)";
            $stmt_insert_assoc = $pdo->prepare($sql_insert_assoc);
            $stmt_insert_assoc->execute([
                ':nom_association' => $nom_association,
                ':cp' => $CP,
                ':epci' => $EPCI,
                ':activite' => $activite_principale,
                ':activiteSec' => $actS
            ]);
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
            $stmt_insert_assoc->bindParam(':activite', $activite_principale);
            $stmt_insert_assoc->bindParam(':activiteSec', $actS);
            $stmt_insert_assoc->execute();
        }

        //Isertion contact pour RDV
        // Vérification si le contact existe
        $stmt_contact = $pdo->prepare("SELECT IDCONTACT FROM CONTACT WHERE NOMCONTACT = :nom_contact AND PRENOMCONTACT = :prenom_contact");
        $stmt_contact->bindParam(':nom_contact', $nom_contact);
        $stmt_contact->bindParam(':prenom_contact', $prenom_contact);
        $stmt_contact->execute();
        $row_contact = $stmt_contact->fetch(PDO::FETCH_ASSOC);

        if ($row_contact > 0) {
            $id_contact = $row_contact['IDCONTACT'];
        } else {
            // Insérer le contact s'il n'existe pas
            $stmt_insert_contact = $pdo->prepare("INSERT INTO CONTACT (NOMCONTACT, PRENOMCONTACT, CIVILITE, EMAILCORRESPONDANT) 
                                                VALUES (:nom_contact, :prenom_contact, :civilite, :email)");
            $stmt_insert_contact->bindParam(':nom_contact', $nom_contact);
            $stmt_insert_contact->bindParam(':prenom_contact', $prenom_contact);
            $stmt_insert_contact->bindParam(':civilite', $civilite);
            $stmt_insert_contact->bindParam(':email', $email);
            $stmt_insert_contact->execute();
            $id_contact = $pdo->lastInsertId();
        }
        
        //RESEAU
        if($reseau){
             // Vérification si la structure existe
            $stmt_structure = $pdo->prepare("SELECT NOMSTRUCTURE FROM STRUCTURE WHERE NOMSTRUCTURE = :structure");
            $stmt_structure->bindParam(':structure', $structure);
            $stmt_structure->execute();
            $row_structure = $stmt_structure->fetch(PDO::FETCH_ASSOC);

            if (!$row_structure) {
                // Insérer une nouvelle structure
                $stmt_insert_structure = $pdo->prepare("INSERT INTO STRUCTURE (NOMSTRUCTURE, CODEPOSTAL, EPCI, ACTIVITE) 
                    VALUES (:structure, :cp, :epci, :activite)");
                $stmt_insert_structure->bindParam(':structure', $structure);
                $stmt_insert_structure->bindParam(':cp', $CP);
                $stmt_insert_structure->bindParam(':epci', $EPCI);
                $stmt_insert_structure->bindParam(':activite', $activite);
                $stmt_insert_structure->execute();
            }
        }

        // Insertion dans la table QUESTIONNAIRE
        $sql = "INSERT INTO QUESTIONNAIRE (IDCONTACT, NOMSTRUCTURE, ACTIVITESTRUCTURE, NOMASSO, ACTIVITEPRINCIPALEASSO, ACTIVITESECONDAIRE, PERMANENCE, CLASSIFICATIONGUIDASSO, TRANSMISPAR, TRANSMISA, NATUREECHANGE, NBRDV, QUESTION, REPONSE, TEMPSPASSE, THEMEGENERAL, AUTRETHEMATIQUE, RESSOURCE, RECHERCHE, MAILGUIDASSO, NOMEVENEMENT, TITREEVENEMENT, AUDIENCE, DATEEVE, COMMUNE, CODE_INSEE, CODE_POSTAL, ARRONDISSEMENT, EPCI, FILE, BLOCNOTE, PJBLOCNOTE, EMPLOYEUR, TYPEQUESTIONNAIRE, NUMERODEPARTEMENT) 
        VALUES (:id_contact, :nom_structure, :activite_structure, :nom_association, :activite_principale, :activite_secondaire, :permanence, :classification, :dossier_transmis_par, :dossier_transmis_a, :nature_echange, :nbrdv, :question, :reponse, :temps_passe, :theme_general, :autre_thematique, :ressources, :recherche, :mail_guidasso, :evenement, :titre_ev, :personne, :date_ev, :commune, :code_insee, :code_postal, :arrondissement, :epci, :file, :bloc_note, :pj_bloc_note, :employeur, :TypeQuestionnaire, :numeroDepartement)";

        $stmt = $pdo->prepare($sql);

        // Ressource est un type aray et doit etre convertie en string
        if (is_array($ressources)) {
            $ressources = implode(", ", $ressources); // Transforme les valeurs en une chaîne séparée par des virgules
        } elseif (empty($ressources)) {
            $ressources = null; // S'assure que la valeur reste correcte si elle est vide
        }

        if($QR || $anonyme || $rechercheForm || $reseau || $evenementForm){
            $occurence = 1;
        }

        if($QR){
             //Metre le temps a 10 automatiquemet pour Q/R
            $temps = 10; 
        }

        $stmt->execute([
            ':id_contact' => $id_contact,
            ':nom_structure' => $structure,  
            ':activite_structure' => $activite_principale,  
            ':nom_association' => $nom_association,
            ':activite_principale' => $activite_principale,
            ':activite_secondaire' => $actS,
            ':permanence' => $permanence,
            ':classification' => $classification,
            ':dossier_transmis_par' => $dossiertransmisa,
            ':dossier_transmis_a' => $dossierprovenantde,
            ':nature_echange' => $type,
            ':nbrdv' => $occurence,
            ':question' => $question,
            ':reponse' => $reponse,
            ':temps_passe' => $temps,
            ':theme_general' => $themeG,
            ':autre_thematique' => $theme,
            ':ressources' => $ressources,
            ':recherche' => $recherche,
            ':mail_guidasso' => $adresse_utilisateur,
            ':evenement' => $evenement,
            ':titre_ev' => $titre_ev,
            ':personne' => $personne,
            ':date_ev' => $Date,
            ':commune' => $commune,
            ':code_insee' => $INSEE,
            ':code_postal' => $CP,
            ':arrondissement' => $Arrondissement,
            ':epci' => $EPCI,
            ':file' => $uploadFile,
            ':bloc_note' => $BN,
            ':pj_bloc_note' => $PJ,
            ':employeur' => $employeur,
            ':TypeQuestionnaire' => $TypeQuestionnaire,
            ':numeroDepartement' => $numeroDepartement
        ]);


        $_SESSION['success_message'] = "Les données ont été enregistrées avec succès !";
       
        header("Location: /../../views/questionnaire.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message_longsuivi'] = $e->getMessage();
       
        header("Location: /../../views/questionnaire.php");
        exit();
    }
}
?>
