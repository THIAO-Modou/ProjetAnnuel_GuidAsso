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

function resolveActiveFormForRedirect(array $post, bool $RDV, bool $reseau, bool $QR, bool $longsuivi, bool $evenementForm, bool $rechercheForm, bool $anonyme): string
{
    $allowed = ['QR', 'RDV', 'reseau', 'longsuivi', 'evenement', 'recherche', 'anonyme'];
    $fromPost = trim((string)($post['formulaire_actif'] ?? ''));
    if (in_array($fromPost, $allowed, true)) {
        return $fromPost;
    }

    if ($RDV) return 'RDV';
    if ($reseau) return 'reseau';
    if ($QR) return 'QR';
    if ($longsuivi) return 'longsuivi';
    if ($evenementForm) return 'evenement';
    if ($rechercheForm) return 'recherche';
    if ($anonyme) return 'anonyme';

    return '';
}

$redirectForm = resolveActiveFormForRedirect($_POST, $RDV, $reseau, $QR, $longsuivi, $evenementForm, $rechercheForm, $anonyme);
$redirectUrl = "/../../views/questionnaire.php" . ($redirectForm !== '' ? "?formulaire=" . urlencode($redirectForm) : "");

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
        $types = $_POST['type'] ?? [];
        $typeRadio = $_POST['type_radio'] ?? '';
        $evenement = $_POST['evenement'] ?? '';
        $titre_ev = $_POST['titre_ev'] ?? '';
        $personne = $_POST['personne'] ?? 0;
        $Date = $_POST['Date'] ?? '';
        $structure = $_POST['structure'] ?? '';
        $classification = $_POST['classification'] ?? '';
        $assoc = trim($_POST['assoc'] ?? '');
        $rna = trim($_POST['rna_id'] ?? '');
        $activite_principale = $_POST['act_principale'] ?? '';
        $autreActivitePrincipale = $_POST['autreActivitePrincipale'] ?? '';
        $autreActiviteSecondaire = $_POST['autreActiviteSecondaire'] ?? '';
        $activiteSec = $_POST['act_sec'] ?? [];
        $nom_contact = $_POST['nom_contact'] ?? '';
        $prenom_contact = $_POST['prenom_contact'] ?? '';
        $activite = $_POST['activite'] ?? '';
        $employeur = $_POST['employeur'] ?? '';
        $email = $_POST['mail'] ?? null;
        $civilite = $_POST['genre'] ?? '';
        $themeG = $_POST['themeG'] ?? '';
        $ressources = $_POST['ressources'] ?? [];
        $themes = $_POST['theme'] ?? [];
        $dossierprovenantde = $_POST['Reponsepartagee'] ?? '';
        $dossiertransmisa = $_POST['Reponsepartagee1'] ?? '';
        $provenance_autre = trim($_POST['provenance_autre'] ?? '');
        $partage_autre = trim($_POST['partage_autre'] ?? '');
        $temps = $_POST['Temps'] ?? 00;
        $recherche = $_POST['recherche'] ?? null;
        $occurence = $_POST['occurence'] ?? 0;
        $autreT = $_POST['autreTheme'] ?? '';
        $autreChamp = $_POST['autreChamp'] ?? '';
        $commune = $_POST['commune'] ?? '';
        $projet_asso = $_POST['projetAssoCheckbox'] ?? '';
        $non_comm = $_POST['nonCommuniqueCheckbox'] ?? '';
        $BN = $_POST['BN'] ?? '';
        $commentaire = $_POST['saisie_libre'] ?? '';
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


        // Gestion des activites secondaires et themes multiples
        if ($activite_principale === "Autre" && trim($autreActivitePrincipale) !== "") {
            $activite_principale = trim($autreActivitePrincipale);
        }

        if (is_array($activiteSec)) {
            if (trim($autreActiviteSecondaire) !== "") {
                $activiteSec = array_values(array_filter($activiteSec, function ($v) {
                    return $v !== "Autre";
                }));
                $activiteSec[] = trim($autreActiviteSecondaire);
            }
            $actS = implode(", ", $activiteSec);
        } else {
            $actS = trim($autreActiviteSecondaire) !== "" ? trim($autreActiviteSecondaire) : "";
        }
        $theme = is_array($themes) ? implode(", ", $themes) : "";
        if (!empty($autreChamp)) {
            $theme .= !empty($theme) ? ", " . $autreChamp : $autreChamp;
        }

        if ($themeG == "Autre") {
            $themeG = $autreT;
        }

        if ($dossierprovenantde === "Autre" && $provenance_autre !== '') {
            $dossierprovenantde = $provenance_autre;
        }
        if ($dossiertransmisa === "Autre" && $partage_autre !== '') {
            $dossiertransmisa = $partage_autre;
        }
        
        $nom_association = '';
        $choix = $_POST['choixAsso'] ?? null;

        if ($RDV || $QR || $longsuivi) {

            if ($choix === "oui") {
                // L'utilisateur dit qu'il a une association → on prend le nom
                $nom_association = $assoc;

            } elseif ($choix === "projet") {
                $nom_association = "Projet d'association";

            } elseif ($choix === "non" || $choix === "nom non communiqué") {
                $nom_association = "Nom non communiqué";
            }

        } else {
            // Pour les autres types de questionnaire
            $nom_association = $assoc;
        }

        if ($nom_association === '' || $nom_association === "Projet d'association" || $nom_association === "Nom non communiqué") {
            $rna = "";
        }

    

        if ($longsuivi) {
            $temps = !empty($temps) ? (int)$temps : 0;
        } else {
            $h = !empty($h) ? (int)$h : 0; // Convertit en entier, ou 0 si vide/null
            $m = (int)$m; // Assure que $m est un entier
            $temps = ($h * 60) + $m; // Conversion en minutes
        }

        //Gerer type si longsuivi
        if(!$QR){
            $actS = !empty($activiteSec) ? implode(", ", $activiteSec) : "";
            $typeS = is_array($types) ? implode(", ", $types) : "";

            // Vérification ou insertion du contact
            $sql_contact = "SELECT IDCONTACT FROM CONTACT WHERE NOMCONTACT = :nom_contact AND PRENOMCONTACT = :prenom_contact";
            $stmt_contact = $pdo->prepare($sql_contact);
            $stmt_contact->execute([':nom_contact' => $nom_contact, ':prenom_contact' => $prenom_contact]);
            $row_contact = $stmt_contact->fetch(PDO::FETCH_ASSOC);

            if ($row_contact) {
                $id_contact = $row_contact['IDCONTACT'];
            } else {
            $sql_insert_contact = "INSERT INTO CONTACT (NOMCONTACT, PRENOMCONTACT, CIVILITE, EMAILCORRESPONDANT) VALUES (:nom_contact, :prenom_contact, :civilite, :email)";
            $stmt_insert_contact = $pdo->prepare($sql_insert_contact);
            $stmt_insert_contact->execute([
                ':nom_contact' => $nom_contact,
                ':prenom_contact' => $prenom_contact,
                ':civilite' => $civilite,
                ':email' => $email
            ]);
            $id_contact = $pdo->lastInsertId();
        }
        if (!empty($email) && !empty($id_contact)) {
            $stmt_update_email = $pdo->prepare("UPDATE CONTACT SET EMAILCORRESPONDANT = :email WHERE IDCONTACT = :id_contact");
            $stmt_update_email->execute([
                ':email' => $email,
                ':id_contact' => $id_contact
            ]);
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


        // Vérification si l'association existe
        $stmt_assoc = $pdo->prepare("SELECT NOMASSO, RNA FROM ASSOCIATION WHERE NOMASSO = :nom_association");
        $stmt_assoc->bindParam(':nom_association', $nom_association);
        $stmt_assoc->execute();
        $row_assoc = $stmt_assoc->fetch(PDO::FETCH_ASSOC);

        if (!$row_assoc) {
            // Insérer l'association si elle n'existe pas
            $stmt_insert_assoc = $pdo->prepare("INSERT INTO ASSOCIATION (NOMASSO, RNA, CODEPOSTAL, EPCI, ACTIVITEPRINCIPALEASSO, ACTIVITESECONDAIRE)
                                                VALUES (:nom_association, :rna, :cp, :epci, :activite, :activiteSec)");
            $stmt_insert_assoc->bindParam(':nom_association', $nom_association);
            $stmt_insert_assoc->bindParam(':rna', $rna);
            $stmt_insert_assoc->bindParam(':cp', $CP);
            $stmt_insert_assoc->bindParam(':epci', $EPCI);
            $stmt_insert_assoc->bindParam(':activite', $activite_principale);
            $stmt_insert_assoc->bindParam(':activiteSec', $actS);
            $stmt_insert_assoc->execute();
        } elseif (!empty($rna) && empty($row_assoc['RNA'])) {
            $stmt_update_assoc = $pdo->prepare("UPDATE ASSOCIATION SET RNA = :rna WHERE NOMASSO = :nom_association");
            $stmt_update_assoc->execute([
                ':rna' => $rna,
                ':nom_association' => $nom_association
            ]);
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
        if (!empty($email) && !empty($id_contact) && $row_contact) {
            $stmt_update_email = $pdo->prepare("UPDATE CONTACT SET EMAILCORRESPONDANT = :email WHERE IDCONTACT = :id_contact");
            $stmt_update_email->execute([
                ':email' => $email,
                ':id_contact' => $id_contact
            ]);
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
        $sql = "INSERT INTO QUESTIONNAIRE (IDCONTACT, NOMSTRUCTURE, ACTIVITESTRUCTURE, NOMASSO, RNA, ACTIVITEPRINCIPALEASSO, ACTIVITESECONDAIRE, PERMANENCE, CLASSIFICATIONGUIDASSO, TRANSMISPAR, TRANSMISA, NATUREECHANGE, NBRDV, QUESTION, REPONSE, TEMPSPASSE, THEMEGENERAL, AUTRETHEMATIQUE, RESSOURCE, RECHERCHE, MAILGUIDASSO, NOMEVENEMENT, TITREEVENEMENT, AUDIENCE, DATEEVE, COMMUNE, CODE_INSEE, CODE_POSTAL, ARRONDISSEMENT, EPCI, FILE, BLOCNOTE, PJBLOCNOTE, EMPLOYEUR, TYPEQUESTIONNAIRE, NUMERODEPARTEMENT) 
        VALUES (:id_contact, :nom_structure, :activite_structure, :nom_association, :rna, :activite_principale, :activite_secondaire, :permanence, :classification, :dossier_transmis_par, :dossier_transmis_a, :nature_echange, :nbrdv, :question, :reponse, :temps_passe, :theme_general, :autre_thematique, :ressources, :recherche, :mail_guidasso, :evenement, :titre_ev, :personne, :date_ev, :commune, :code_insee, :code_postal, :arrondissement, :epci, :file, :bloc_note, :pj_bloc_note, :employeur, :TypeQuestionnaire, :numeroDepartement)";

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
            ':activite_structure' => $activite,  
            ':nom_association' => $nom_association,
            ':rna' => $rna !== '' ? $rna : null,
            ':activite_principale' => $activite_principale,
            ':activite_secondaire' => $actS,
            ':permanence' => $permanence,
            ':classification' => $classification,
            ':dossier_transmis_par' => $dossierprovenantde,
            ':dossier_transmis_a' => $dossiertransmisa,
            ':nature_echange' => $typeRadio !== '' ? $typeRadio : (is_array($types) ? implode(", ", $types) : $types),
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
       
        header("Location: " . $redirectUrl);
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message_longsuivi'] = $e->getMessage();
       
        header("Location: " . $redirectUrl);
        exit();
    }
}
?>


