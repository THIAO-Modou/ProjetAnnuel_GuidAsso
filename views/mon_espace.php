<?php 
session_start();
//Vider la saission des boutons
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_GET)) {
    unset($_SESSION['bouton_monEspace_actif']);
}


error_log(" Email stocké en session: " . ($_SESSION['MAIL'] ?? 'Aucun'));


error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/../config/BD.php';
include_once __DIR__ . '/../config/session.php';
include_once __DIR__ . '/../controllers/user/get_user_info.php';

if (!isset($_SESSION['MAIL'])) {
    header("Location: /views/pageconnexion.php");
    exit();
}
$email = $_SESSION['MAIL'];
// Recuperation des informations de l'utilisateur connecté
$user = getUserInfoByEmail($_SESSION['MAIL']);

$boutonClique = $_SESSION['bouton_monEspace_actif'] ?? null;

if ($user) {
    $nom = $user['NOMPERSONNE'];
    $prenom = $user['PRENOMPERSONNE'];
    $idFonction = $user['IDFONCTION'];
    $classification = $user['CLASSIFICATION'];
    $role = $user['NAMEFONCTION'];
    // Information à envoyer au fichier JS
    $data = [
        "NOMPERSONNE" => $user['NOMPERSONNE'],
        "PRENOMPERSONNE" => $user['PRENOMPERSONNE'],
        "IDFONCTION" => $user['IDFONCTION'],
        "CLASSIFICATION" => $user['CLASSIFICATION']
    ];
} else {
    $prenom = "Utilisateur inconnu";
}
//  Envoi des infos sous forme de JSON accessible en JavaScript
echo "<script>var userData = " . json_encode($data) . ";</script>";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mon espace Guid'Asso 86</title>

    <!--Pour la responsivité -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!------------------- Biblioteque d'exportation en pdf ----------------->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>


    <script src="/../public/js/script_mon_espace.js" defer></script>
    <script src="/../public/js/script_commun_admin_monEspace.js"></script>
    <script src="/../public/js/ecouteur_activite.js"></script>

    <link rel="icon" href="/../public/img/logo_page.png" type="image/png">

    <link rel="stylesheet" href="/../public/css/style_mon_espace.css">
    <link rel="stylesheet" href="/../public/css/style_visionneuses.css">
    <link rel="stylesheet" href="/../public/css/responsive_home.css">
    

</head>


<body onClick="<?= isset($_SESSION['show_form']) ? $_SESSION['show_form'] : '' ?>">

    <div class="image-container">
        <img src="/../public/img/LogoRéseau1.png">
        <img src="/../public/img/LogoInformation.png">
        <img src="/../public/img/LogoOrientation1.png">
        <img src="/../public/img/LogoAccompagnementG1.png">
    </div>

    <div class="setting-button-container">
        <a href="questionnaire.php" class="setting-button">Questionnaire</a>
        <a href="/../config/deconnexion.php" class="setting-button">Se déconnecter</a>

    </div>

    <section id="dashboard" class="dashboard">
        <h1 class="dashboard-title">
            Bonjour <span style="color: blue;"><?= $prenom ?></span> ! Que souhaitez-vous faire ?
        </h1>
        <div id="buttons-container" class="buttons-container">
            <button class="dashboard-button-green" onclick="showForm('mes-infos')">Informations personnelles</button> 
            <button class="dashboard-button-blue" onclick="showForm('modif-mdp')">Changer mot de passe</button>
            <button class="dashboard-button-pink" onclick="showForm('pieces-jointes-container')">Accès aux pièces jointes</button>
            <button class="dashboard-button-blue-bn" data-target="blocs-notes-container">Accès aux blocs-notes </button>
            <!--<button class="dashboard-button-purple" onclick="toggleImportExport(', .pieces-jointes-container')">Gestion des documents</button> -->
             <button class="dashboard-button-green-export" onclick="toggleImportExport()">Gestion BDD</button>  
        </div>

    </section>

    <div id="main-content"> <!-- Contenu principal -->

    <!------------------------------------------------------------------->
    <!-----------------------MESSAGES DE SUCCES ------------------------->
    <!------------------------------------------------------------------->

        <!---------------------- message succès si mdp bien changé ou infos --------------------->
        <?php if (!empty($_SESSION['success_message'])): ?>
            <div id="success-container" class="success-container" style="display: block;">
                <div class="success-message-form">
                    <span class="success-icon">&#10004;</span>
                    <span id="success-text"><?= htmlspecialchars($_SESSION['success_message']); ?></span>
                </div>
            </div>
            <?php unset($_SESSION['success_message']); // Efface après affichage ?>
        <?php endif; ?>

    <!------------------------------------------------------------------->
    <!------------------VISIONNEUSE ACCUEIL PAGE ------------------------>
    <!------------------------------------------------------------------->
    
        <div id="visionneuse-container_admin_monEspace">
            <h2>Vos dernières réponses aux questionnaires</h2>
            <div id="visionneuse_admin_monEspace" style="overflow-x: auto;">
                <!-- Le tableau sera inséré ici -->
            </div>
            <div id="filtre-container">
                <label for="nbLignes">Nombre de lignes :</label>
                <select id="nbLignes">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>

                <button id="prevPage" disabled> ◀ </button>
                <span id="pageInfo">Page 1</span>
                <button id="nextPage"> ▶ </button>
            </div>
            <button id="btnExportPdf">Exporter en PDF</button>
        </div>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="/../public/js/script_questionnaire.js"></script>
        

        <!------------------------------------------------------------------->
        <!-----------------------MES INFOS  ------------------------->
        <!------------------------------------------------------------------->
        <div class="formulaire-container-vert" id="mes-infos" 
        style="display: <?= ($show_form === 'mes-infos' || !empty($error_message_update)) ? 'block' : 'none'; ?>;">
                
                <!-- Message d'erreur --> 
                <?php if (!empty($_SESSION['error_message_update'])): ?>
                    <div id="error-container" class="error-container" style="display: block;">
                        <div class="error-message-form">
                            <span class="error-icon">&#9888;</span>
                            <span id="error-text"><?= htmlspecialchars($_SESSION['error_message_update']); ?></span>
                        </div>
                    </div>
                    <?php unset($_SESSION['error_message_update']); // Efface après affichage ?>
                <?php endif; ?>
        
                <section id="mes-infos-form" class="mes-infos-form">
                    <h3>Vos Informations Personnelles</h3>
                    <form method="post" action="/../controllers/user/update_info.php">

                        <div class="email-form-info"> 
                            <div class="espace-form-group-info">
                                <label class="bold" for="email">Email :</label>
                                <input type="email" id="email" name="email" value="<?= $email ?>" readonly>
                            </div>
                        </div>

                        <div class="espace-form-group-info">
                            <label class="bold" for="nom">Nom :</label>
                            <input type="text" id="nom" name="nom" value="<?= $nom ?>" required>
                        </div>

                        <div class="espace-form-group-info">
                            <label class="bold" for="prenom">Prénom :</label>
                            <input type="text" id="prenom" name="prenom" value="<?= $prenom ?>" required>
                        </div>

                        <div class="email-form-info">
                            <div class="espace-form-group-info">
                                <label class="bold" for="role">Rôle :</label>
                                <input type="text" id="role" name="role" value="<?= $role ?>" readonly>
                            </div>
                        </div>

                        <div class="classification-group">
                            <label for="classification" class="classification-label">Classification Guid'Asso :</label>
                            <div class="classification-options">
                                <label>
                                    <input type="radio" class="classification" name="classification" value="orientation" <?= ($classification === "orientation") ? "checked" : "" ?>> Orientation
                                </label>
                                <label>
                                    <input type="radio" class="classification" name="classification" value="info" <?= ($classification === "info") ? "checked" : "" ?>> Information
                                </label>
                                <label>
                                    <input type="radio" class="classification" name="classification" value="accompagnementG" <?= ($classification === "accompagnementG") ? "checked" : "" ?>> Accompagnement Généraliste
                                </label>
                                <label>
                                    <input type="radio" class="classification" name="classification" value="accompagnementSpe" <?= ($classification === "accompagnementSpe") ? "checked" : "" ?>> Accompagnement Spécialiste
                                </label>
                                <label>
                                    <input type="radio" class="classification" name="classification" value="autre" <?= ($classification === "autre") ? "checked" : "" ?>> Autre
                                </label>
                            </div>
                        </div>  
                        <? if($idFonction ==1 ){
                            echo'<button class="button-vert" type="submit">Mettre à jour mes infos</button>';
                        }    
                        elseif($idFonction ==4){
                            echo'<span class="tooltip">
                            <button style="background-color: #c0c0c0;" disabled>Mettre à jour mes infos</button>
                            <span class="tooltiptext">Vous n\'avez pas le droit de mettre à jour vos infos.</span>
                            </span>';
                        }?>
                    </form>
                </section>
            </div>

                <!------------------------------------------------------------------->
                <!-----------------------CHANGEMENT DE MDP  ------------------------->
                <!------------------------------------------------------------------->

                <div class="formulaire-container-bleu" id="modif-mdp" 
                    style="display: <?= ($show_form === 'modif-mdp' || !empty($error_message_modif_mdp)) ? 'block' : 'none'; ?>;">

                <!-- Message d'erreur --> 
                <?php if (!empty($_SESSION['error_message_modif_mdp'])): ?>
                    <div id="error-container" class="error-container" style="display: block;">
                        <div class="error-message-form">
                            <span class="error-icon">&#9888;</span>
                            <span id="error-text"><?= htmlspecialchars($_SESSION['error_message_modif_mdp']); ?></span>
                        </div>
                    </div>
                    <?php unset($_SESSION['error_message_modif_mdp']); // Efface après affichage ?>
                <?php endif; ?>

                    <section id="mes-infos-form" class="mes-infos-form">
                        <h3 class="bold">Modification mot de passe</h3>
                        <form method="post" action="/../controllers/user/modif_mdp.php" enctype="multipart/form-data">
                
                            <!-- Champ Email (Prérempli) -->
                            <div class="email-form"> 
                                <div class="espace-form-group">
                                    <label class="bold" for="email">Adresse mail :</label>
                                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['MAIL'] ?? '') ?>" readonly style="width: 40%;">
                                </div>
                            </div>

                            <!-- Ancien mot de passe -->
                            <div class="espace-form-group">
                                <label  class="bold" for="ancien_mdp">Ancien mot de passe :</label>
                                <input type="password" id="password_past" name="ancien_mdp" required>
                                <label class="show-password">
                                    <input type="checkbox" onclick="togglePasswordVisibility('password_past')"> Afficher le mot de passe
                                </label>
                            </div>

                            <!-- Nouveau mot de passe -->
                            <div class="espace-form-group">
                                <label class="bold" for="nouveau_mdp">Nouveau mot de passe :</label>
                                <input type="password" id="password_new" name="nouveau_mdp" required>
                                <label class="show-password">
                                    <input type="checkbox" onclick="togglePasswordVisibility('password_new')"> Afficher le mot de passe
                                </label>
                            </div>

                            <!-- Confirmation du mot de passe -->
                            <div class="espace-form-group">
                                <label  class="bold" for="confirm_mdp">Confirmation du mot de passe :</label>
                                <input type="password" id="password_confirm" name="confirm_mdp" required>
                                <label class="show-password">
                                    <input type="checkbox" onclick="togglePasswordVisibility('password_confirm')"> Afficher le mot de passe
                                </label>
                            </div>

                            <!-- Bouton de soumission -->
                            
                            <? if($idFonction ==1 ){
                                echo'<button class="button-bleu" type="submit">Modifier le mot de passe</button>';
                            }    
                            elseif($idFonction ==4){
                                echo'<span class="tooltip">
                                <button style="background-color: #c0c0c0;" disabled>Modifier le mot de passe</button>
                                <span class="tooltiptext">Vous n\'avez pas le droit de modifier vos informations.</span>
                                </span>';
                            }?>

                        </form>
                    </section>
                </div>

                <!------------------------------------------------------------------->
                <!------------------------- PIECES JOINTES  ------------------------->
                <!------------------------------------------------------------------->
                <div id="pieces-jointes-container">
                    <section class="pieces-jointes-form">
                        <h3>Liste de mes pièces jointes déposées</h3>
                        <table id="fileTable" style="overflow-x: auto;">
                        <thead>
                            <tr>
                            <th>Horodateur</th>
                            <th>Nom événement</th>
                            <th>Nom du fichier</th>
                            <th class="file-actions">Aperçu</th>
                            <th class="file-actions">Télécharger</th>
                            <th>Supprimer</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        </table>

                        <div id="filtre-container">
                        <label for="nbLignes-pj">Nombre de lignes :</label>
                        <select id="nbLignes-pj">
                            <option value="5" selected>5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <button id="prevPage-pj" disabled> ◀ </button>
                        <span id="pageInfo-pj">Page 1</span>
                        <button id="nextPage-pj"> ▶ </button>
                        </div>
                    </section>
                </div>
            <!---------------------------------------------------------------------------->
            <!------------------------------- BlOCS - NOTES  ----------------------------->
            <!---------------------------------------------------------------------------->
            

            <div id="blocs-notes-container" class="admin-section">
                    <h3>Liste des blocs notes</h3>
                    <div id="table-blocnotes-wrapper">
                    <table id="blocsNotesTable" style="overflow-x: auto;">
                        <thead>
                        <tr>
                            <th>Horodateur</th>
                            <th>Nom Association</th>
                            <th>Thématique</th>
                            <th>Bloc note</th>
                            <th>Nom du fichier</th>
                            <th>Déposé par</th>
                            <th>Aperçu</th>
                            <th>Télécharger</th>
                            <th>Supprimer</th>
                        </tr>
                        </thead>
                        <tbody>
                            <!-- Les fichiers seront chargés via le script JS -->
                        </tbody>
                    </table>
                    </div>

                    <div id="filtre-container">
                    <label for="nbLignes-bloc">Nombre de lignes :</label>
                    <select id="nbLignes-bloc" style="width: 70px; height: 44px; margin-top: 15px;">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>

                    <button id="prevPage-bloc" disabled> ◀ </button>
                    <span id="pageInfo-bloc">Page 1</span>
                    <button id="nextPage-bloc"> ▶ </button>
                    </div>
                </section>
            </div>

            
            <!---------------------------------------------------------------------------------------->
            <!--------------------------------- IMPORT/EXPORT BDD ------------------------------------>
            <!---------------------------------------------------------------------------->

            <div id="import-export-form-container" class="admin-section">
                <section id="import-export-form" class="import-export-form">
                    <h3>Import / Export des Données</h3>
                    
                        <? if($idFonction ==1 ){?>
                                <span class="tooltip">
                                    <button class="import_button" id="importButton" disabled style="background-color: #c0c0c0;">Importer</button>
                                    <span class="tooltiptext">Vous n'avez pas le droit d'importer des données.</span>
                                </span>
                                <span class="tooltip">
                                    <button class="export_button" id="exportButton">Exporter</button>
                                </span>
                        <? }    
                        else {?>
                            <span class="tooltip">
                                <button class="btn-desactive" style="background-color: #c0c0c0;" disabled>Import</button>
                                <span class="tooltiptext">Vous n'avez pas le droit d'importer des données.</span>
                            </span>
                            <span class="tooltip">
                                <button class="btn-desactive" style="background-color: #c0c0c0;" disabled>Export</button>
                                <span class="tooltiptext">Vous n'avez pas le droit d'exporter des données.</span>
                            </span>
                        <?}?>
                        
                        <form id="importForm" action="/../database/import.php" method="post" enctype="multipart/form-data">
                            <input type="file" name="csv_file" id="csvFileInput" accept=".csv" required style="display: none;">
                            <button type="submit" name="import" style="display: none;"></button>
                        </form>
                    
                </section>
                <!---------------------------------------------------------------->
                <!------------------PERIODE D'EXPORTATION------------------------->
                <label><input type="checkbox" name="date-select" value="export-date" onclick='setupCheckboxToggle("export-date", "export-date-container")'> Définir une période d’exportation </label>

                <div id="export-date-container" class="export-date-container" style="display:none;">
                    <label for="start">Date de début :</label>
                    <input type="date" id="start" name="start">

                    <label for="end">Date de fin :</label>
                    <input type="date" id="end" name="end">
                </div>
            </div>
        </div>

            <!--BANDEROLE FIN DE PAGE-->
            <section id="footer" class="footer">
            <div class="footer-content">
                <div class="footer-text">
                    <p>Guid'Asso</p>
                    <p>Patrice Mancino : 06 07 08 09 10</p>
                    <p>Assistance client : vieasso86@guidasso86.fr</p>
                </div>
                <div class="footer-image">
                    <a href="/../views/mon_espace.php" class="btn-home" title="Retour à l'accueil">
                        <img src="/../public/img/home.png" alt="Accueil" />
                    </a>
                    <img src="/../public/img/CRAIG.png" alt="Logo">
                </div>
            </div>
        </section>

<script>
// Cet Script attend que toute la page soit complètement chargée avant d'exécuter le script        
document.addEventListener("DOMContentLoaded", function() {

    /**
     * Fonction utilitaire pour afficher ou masquer dynamiquement un champ associé à une checkbox
     * @param {string} checkboxValue - La valeur attendue de la checkbox à cibler
     * @param {string} autreChampId - L'identifiant du champ à afficher/masquer
     */
function setupCheckboxToggle(checkboxValue, autreChampId) {
    // Sélectionne la checkbox avec le nom 'date-select' et la valeur correspondante
    const checkbox = document.querySelector(`input[name='date-select'][value='${checkboxValue}']`);

     // Sélectionne le champ supplémentaire lié (ex: un conteneur de date)
    const autreChamp = document.getElementById(autreChampId);

    // Vérifie que les deux éléments existent dans le DOM
    if (!checkbox || !autreChamp) {
        console.warn("Un des éléments nécessaires n'a pas été trouvé pour la valeur:", checkboxValue);
        return; // On sort si l’un des deux éléments est absent
    }
    // Ajoute un écouteur d'événement sur la checkbox
    checkbox.addEventListener("change", function() {
        if (checkbox.checked) {
            autreChamp.style.display = "block";
            checkbox.value = ""; // On efface la valeur de la checkbox pour signaler une sélection personnalisée
        } else {
            autreChamp.style.display = "none";  // Si la checkbox est décochée, on cache le champ associé
            autreChamp.value = "";     // Et on réinitialise son contenu
        }
    });
}
// Initialise le comportement avec les bons paramètres
// Affiche ou masque l'élément #export-date-container en fonction de la checkbox "export-date"
setupCheckboxToggle("export-date", "export-date-container"); 
});
</script>
</body>

</html>
