<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

//Vider la saission des boutons
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_GET)) {
    unset($_SESSION['bouton_admin_actif']);
}

// pour accéder à tous les fichiers php pour la connexion, session et inscription
include_once __DIR__ . '/../config/BD.php';
include_once __DIR__ . '/../config/session.php';
include_once __DIR__ . '/../controllers/admin/inscription.php';

if (!isset($_SESSION['MAIL'])) {
    header("Location: /../views/pageconnexion.php");
    exit();
}
$user = getUserInfoByEmail($_SESSION['MAIL']);
$email = $_SESSION['MAIL'];
// Session pour stocker l'etat des boutons
$boutonClique = $_SESSION['bouton_admin_actif'] ?? null;

if ($user) {
    $idFonction = $user['IDFONCTION'];
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

<!------------------------------------------------------------------------------------------------>
<!------------------------------------------- PARTIE HTML ---------------------------------------->
<!------------------------------------------------------------------------------------------------>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Guid'Asso 86</title>

    <link rel="icon" href="/../public/img/logo_page.png" type="image/png">

    <link rel="stylesheet" href="/../public/css/style_admin.css">
    <link rel="stylesheet" href="/../public/css/style_visionneuses.css">
    <link rel="stylesheet" href="/../public/css/style_mon_espace.css">
    <link rel="stylesheet" href="/../public/css/responsive_home.css">

    <!--Pour la responsivité -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!------------------- Biblioteque d'exportation en pdf ----------------->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <script src="/../public/js/scriptJSAdmin.js" defer></script>
    <script src="/../public/js/script_commun_admin_monEspace.js"></script>
    <script src="/../public/js/ecouteur_activite.js"></script>

    <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



</head>
<body>
  <div class="image-container">
	<a href="/../views/pageadmin.php" title="Retour à l'accueil">
    	<img src="/../public/img/LogoRéseau1.png" alt="Logo Réseau">
	</a>
	<a href="/../views/pageadmin.php" title="Retour à l'accueil">
    	<img src="/../public/img/LogoInformation.png" alt="Logo Information">
	</a>
	<a href="/../views/pageadmin.php" title="Retour à l'accueil">
    	<img src="/../public/img/LogoOrientation1.png" alt="Logo Orientation">
	</a>
	<a href="/../views/pageadmin.php" title="Retour à l'accueil">
    	<img src="/../public/img/LogoAccompagnementG1.png" alt="Logo Accompagnement">
	</a>
</div>

    <div class="setting-button-container">
        <a href="/../views/questionnaire.php" class="setting-button">Questionnaire
        </a>
        <a href="/../config/deconnexion.php" class="setting-button">Se déconnecter</a>
    </div>

<section id="dashboard" class="dashboard">
    <h1 class="dashboard-title">Bonjour ! Que souhaitez-vous faire ?</h1>
    <div id="buttons-container" class="buttons-container">

    <!-- Supprimer contact + cartographie, boutons à activer quand ce sera développé -->
        <button class="dashboard-button-green" data-target="add-user-form-container">Ajouter un contact</button>
        <button class="dashboard-button-green" data-target="modif-user-form-container">Modifier un contact</button>
        <button class="dashboard-button-purple" id="GestionUsersBtn">Gestion utilisateurs</button>
        <!--<button class="dashboard-button-green" data-target="delete-user-form-container">Supprimer un contact</button> -->
        <button class="dashboard-button-pink" data-target="pieces-jointes-container">Accès aux pièces jointes</button>
        <button class="dashboard-button-blue" data-target="blocs-notes-container">Accès aux blocs-notes </button>
        <button class="dashboard-button-green-export" onclick="toggleImportExport()">Gestion BDD</button>
        <button class="dashboard-button-purple" onclick="window.location.href='/../statistique/page_stat.php'">Statistiques</button>
        <!--<button class="dashboard-button-green" onclick="generateMap()">Cartographie</button>-->
    </div>
</section>

<div id="main-content"> <!-- Contenu principal -->


    <!------------------------------------------------------------------->
    <!-----------------------MESSAGES DE SUCCES ET ERREUR ------------------------->
    <!------------------------------------------------------------------->

    <!---------------------- message succès pour tous les questionnaires --------------------->
        <!-- Message de succès -->
        <?php if (!empty($_SESSION['success_message'])): ?>
            <div class="success-container" style="display: block;">
                <div class="success-message-form">
                    <span class="success-icon">&#10004;</span>
                    <span><?= htmlspecialchars($_SESSION['success_message']); ?></span>
                </div>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

    <!------------------------------------------------------------------->
    <!------------------VISIONNEUSE ACCUEIL PAGE ------------------------>
    <!------------------------------------------------------------------->
    <?php if (!$boutonClique): ?>
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
        <?php endif ?>


    <!------------------------------------------------------------------->
    <!----------------------- INSCRIPTION ------------------------->
    <!------------------------------------------------------------------->

    <!------------------------------- Formulaire d'ajout de personne --------------------------------->

    
    <div id="add-user-form-container" class="admin-section" 
        style="display: <?= isset($_SESSION['error_message_inscription']) ? 'block' : 'none'; ?>;">
        
        <!-- Message d'erreur --> 
        <?php if (!empty($_SESSION['error_message_inscription'])): ?>
            <div id="error-container" class="error-container" style="display: block;">
                <div class="error-message-form">
                    <span class="error-icon">&#9888;</span>
                    <span id="error-text"><?= htmlspecialchars($_SESSION['error_message_inscription']); ?></span>
                </div>
            </div>
            <?php unset($_SESSION['error_message_inscription']); // Efface après affichage ?>
        <?php endif; ?>
    
        <section id="add-user-form" class="add-user-form">
        <h3 class="dashboard-title">Ajouter une personne :</h3>
            <form  id="user-form" method="post" action="/../controllers/admin/inscription.php" enctype="multipart/form-data">
                <input type="hidden" name="form_type" value="add_user">
                <div class="user-details2">

                    <div class="admin-form-group">
                        <label for="nom">Nom :</label>
                        <input type="text" id="nom" name="nom" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="prenom">Prénom :</label>
                        <input type="text" id="prenom" name="prenom" required>
                    </div>
                    <div class="admin-form-group">
                        <label for="email">Adresse mail :</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="admin-form-group">
                        <div class="password-container">
                            <label for="motdepasse">Mot de passe :</label>
                            <input type="password" id="password1" name="mdp" required> <!-- Ne pas oublier d'ajouter required --> 
                            <label class="show-password"> <!-- Afficher mdp si on coche case -->
                                <input type="checkbox" onclick="togglePasswordVisibility('password1')"> Afficher le mot de passe 
                            </label>
                        </div>
                    </div>
                    <div class="admin-confirm-form-group">
                        <label for="motdepasse">Confirmation de mot passe :</label>
                        <input type="password" id="password2" name="confirm_mdp" required > <!-- Ne pas oublier d'ajouter required --> 
                
                    </div>
                    <div class="admin-form-group">
                        <label for="role">Rôle :</label>
                        <select id="role" name="role">
                            <option value="1">Utilisateur</option>
                            <option value="2">Administrateur</option>
                            <option value="3">Visiteur (admin)</option>
                            <option value="4">Visiteur (utilisateur)</option>
                        </select>
                    </div>
                    <div class="classification-group">
                        <label for="classification" class="classification-label">Classification Guid'Asso :</label>
                        <div class="classification-options">
                            <label>
                                <input type="radio" class="classification" name="classification" value="orientation"> Orientation
                            </label>
                            <label>
                                <input type="radio" class="classification" name="classification" value="info"> Information
                            </label>
                            <label>
                                <input type="radio" class="classification" name="classification" value="accompagnementG"> Accompagnement Généraliste
                            </label>
                            <label>
                                <input type="radio" class="classification" name="classification" value="accompagnementSpe"> Accompagnement Spécialiste
                            </label>
                            <label>
                                <input type="radio" class="classification" name="classification" value="autre"> Autre
                            </label>
                        </div>
                    </div>
                    
                    <? if($idFonction ==2){
                        echo'<button class="button-vert" type="submit">Envoyer</button>';
                    }    
                    elseif($idFonction ==3) {
                        echo'<span class="tooltip" style="margin-left:40% ;">
                            <button class="button-vert" style="background-color: #c0c0c0; width:100px;" disabled>Envoyer</button>
                            <span class="tooltiptext">Vous n\'avez pas le droit d\'ajouter une personne.</span>
                            </span>';
                    }?>
                </div>
            </form>
        </section>
    </div>

<!------------------------------- Formulaire MODIFICATION de personne ---------------------------->


    <div id="modif-user-form-container" class="admin-section" 
        style="display: <?= isset($_SESSION['error_message_modification']) ? 'block' : 'none'; ?>;">
            
        <!-- Message d'erreur --> 
        <?php if (!empty($_SESSION['error_message_modification'])): ?>
            <div id="error-container" class="error-container" style="display: block;">
                <div class="error-message-form">
                    <span class="error-icon">&#9888;</span>
                    <span id="error-text"><?= htmlspecialchars($_SESSION['error_message_modification']); ?></span>
                </div>
            </div>
            <?php unset($_SESSION['error_message_modification']); // Efface après affichage ?>
        <?php endif; ?>
        
        <section id="modif-user-form" class="modif-user-form">
            <br><h3 class="dashboard-title">Modifier un contact :</h3>
            

            <!-- 🔹 Boutons pour choisir entre Email et Nom -->
            <div id="search-buttons-container" style="display: flex; gap: 10px; margin-bottom: 20px;">
                <button type="button-modif" id="search-by-email" class="search-btn">E-mail</button>
                <button type="button-modif" id="search-by-name" class="search-btn">Nom</button>
            </div>


            <!-- FORMULAIRE AFFICHE PAR L'EMAIL -------------------------------->
            <form id="email-form" method="post" action="/../controllers/admin/modification.php" enctype="multipart/form-data">
                <input type="hidden" name="form_type" value="update_user_email">
                <div class="admin-form-group">
                    <div class="email-form-info"> 
                        <label for="email1">E-mail (non modifiable) :</label>
                        <input type="email" id="email1" name="email1" autocomplete="off" required>
                    </div>
                    <div class="suggestions-box" id="emailSuggestions"></div>
                </div>

                <div id="details-container" style="display:none">
                    <div class="admin-form-group">
                        <label for="nom1">Nom :</label>
                        <input type="text" id="nom1" name="nom1">
                    </div>

                    <div class="admin-form-group">
                        <label for="prenom1">Prénom :</label>
                        <input type="text" id="prenom1" name="prenom1">
                    </div>

                    <div class="admin-form-group">
                        <label for="role">Rôle :</label>
                        <select id="role" name="role">
                            <option value="1">Utilisateur</option>
                            <option value="2">Administrateur</option>
                            <option value="3">Visiteur (admin)</option>
                            <option value="4">Visiteur (utilisateur)</option>
                        </select>
                    </div>

                    <div class="admin-form-group">
                        <div class="classification-group">
                            <label for="classification" class="classification-label">Classification :</label>
                            <div class="classification-options">
                                <label><input type="radio" class="classification" name="classification" value="orientation"> Orientation</label>
                                <label><input type="radio" class="classification" name="classification" value="info"> Information</label>
                                <label><input type="radio" class="classification" name="classification" value="accompagnementG"> Accompagnement Généraliste</label>
                                <label><input type="radio" class="classification" name="classification" value="accompagnementSpe"> Accompagnement Spécialiste</label>
                                <label><input type="radio" class="classification" name="classification" value="autre"> Autre</label>
                            </div>
                        </div>
                    </div>
                </div>

                
                <? if($idFonction !=3 ){
                    echo'<button id="btn-mjr" class="button-purple" type="submit">Mettre à jour</button>';
                    }    
                    else {
                    echo'<span class="tooltip" style="margin-left:40% ;">
                        <button style="background-color: #c0c0c0;" disabled>Mettre à jour</button>
                        <span class="tooltiptext">Vous n\'avez pas le droit de modifier des informations.</span>
                        </span>';
                    }?>
               </form>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="/../public/js/scriptJSAdmin.js"></script>
            



        <!-------------- FORMULAIRE AFFICHE PAR LE NOM ----------------------->

            <form id="nom-form" method="post" action="/../controllers/admin/modification.php" enctype="multipart/form-data">
                <input type="hidden" name="form_type" value="update_user_name">
                <div class="admin-form-group">
                    <label for="nom2">Nom :</label>
                    <input type="text" id="nom2" name="nom2" autocomplete="off" required>

                    <div class="suggestions-box" id="nomSuggestions"></div>
                </div>

                <div id="details-container2" style="display:none">
                    <div class="admin-form-group">
                        <label for="prenom2">Prénom :</label>
                        <input type="text" id="prenom2" name="prenom2">
                    </div>

                    <div class="admin-form-group">
                        <div class="email-form-info"> 
                            <label for="email2">Email (non modifiable) :</label>
                            <input type="email" id="email2" name="email2" readonly>
                        </div>
                    </div>

                    <div class="admin-form-group">
                        <label for="role">Rôle :</label>
                        <select id="role" name="role">
                            <option value="1">Utilisateur</option>
                            <option value="2">Administrateur</option>
                            <option value="3">Visiteur (admin)</option>
                            <option value="4">Visiteur (utilisateur)</option>
                        </select>
                    </div>

                    <div class="admin-form-group">
                        <div class="classification-group">
                            <label for="classification" class="classification-label">Classification :</label>
                            <div class="classification-options">
                                <label><input type="radio" class="classification" name="classification" value="orientation"> Orientation</label>
                                <label><input type="radio" class="classification" name="classification" value="info"> Information</label>
                                <label><input type="radio" class="classification" name="classification" value="accompagnementG"> Accompagnement Généraliste</label>
                                <label><input type="radio" class="classification" name="classification" value="accompagnementSpe"> Accompagnement Spécialiste</label>
                                <label><input type="radio" class="classification" name="classification" value="autre"> Autre</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                 <? if($idFonction !=3 ){
                    echo'<button id="btn-mjr" class="button-vert" type="submit">Mettre à jour</button>';
                }    
                 else {
                 echo'<span class="tooltip" style="margin-left:40% ;">
                        <button style="background-color: #c0c0c0;" disabled>Mettre à jour</button>
                        <span class="tooltiptext">Vous n\'avez pas le droit de modifier des informations.</span>
                        </span>';
                 }?>
            </form>

        </section>
    </div>


    <!------------------------------- PIECES JOINTES ----------------------------->
    <?php if (!$boutonClique): ?>
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
                <th style="width: 10px;">Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <!-- Les fichiers seront chargés via le script JS -->
            </tbody>
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
    <?php endif ?>


    <!---------------------------------------------------------------------------->
    <!---------------------------------------------------------------------------->
    <!------------------------------- BlOCS - NOTES  ----------------------------->
    <!---------------------------------------------------------------------------->
    
    <?php if (!$boutonClique): ?>
    <div id="blocs-notes-container" class="admin-section">
        <section class="blocs-notes-form">
            <h3>Liste des blocs notes</h3>
            <div id="table-blocnotes-wrapper" style="overflow-x: auto;">
            <table id="blocsNotesTable">
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
<?php endif ?>

 <!--------------------------------- IMPORT/EXPORT BDD ------------------------------------>
<?php if (!$boutonClique): ?>
    <div id="import-export-form-container" class="admin-section" style="display: none;">
        <section id="import-export-form" class="import-export-form">
            <h3>Import / Export des Données</h3>
                
                <? if($idFonction ==2 ){ ?>
                    <button class="import_button" id="importButton" style="background: #27a49e;">Importer</button>
                    <button class="export_button" id="exportButton">Exporter</button>
                    
                <?}    
                 else {
                 echo'<span class="tooltip">
                        <button style="background-color: #c0c0c0;" disabled>Import</button>
                        <span class="tooltiptext">Vous n\'avez pas le droit d\'importer des données.</span>
                        </span>
                        <span class="tooltip">
                        <button style="background-color: #c0c0c0;" disabled>Export</button>
                        <span class="tooltiptext">Vous n\'avez pas le droit d\'exporter des données.</span>
                        </span>';
                 }?>

                <form id="importForm" action="/../database/import.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="csv_file" id="csvFileInput" accept=".csv" required style="display: none;">
                    <button type="submit" name="import" style="display: none;"></button>
                </form>
                
           
                <!---------------------------------------------------------------->
                <!------------------PERIODE D'EXPORTATION------------------------->
                <label style="margin-top: 20px;"><input type="checkbox" name="date-select" value="export-date" onclick='setupCheckboxToggle("export-date", "export-date-container")'> Définir une période d’exportation </label>

                    <div id="export-date-container" class="export-date-container" style="display:none;">
                        <label for="start">Date de début :</label>
                        <input type="date" id="start" name="start">

                        <label for="end">Date de fin :</label>
                        <input type="date" id="end" name="end">
                    </div>
                <!---------------------------------------------------------------->
            <!------------------EXPORT PAR UTILISATEURS----------------------->
            <?php if ($idFonction == 2) { ?> 
                <label style="margin-top: 20px;">
                    <input type="checkbox" name="user-select" value="export-user" 
                        onclick='setupCheckboxToggle("export-user", "export-user-container")'> 
                    Exporter par utilisateur(s)
                </label>

                <div id="export-user-container" style="display:none; margin-top:10px;">
                    <p>Sélectionner un ou plusieurs utilisateurs :</p>

                    <div id="checkbox-list" style="max-height:200px; overflow-y:auto; padding-left:10px; text-align:left;">
                        <?php
                            $stmtUsers = $pdo->query("
                                SELECT MAIL, NOMPERSONNE, PRENOMPERSONNE 
                                FROM GUIDASSO 
                                ORDER BY NOMPERSONNE ASC
                            ");

                            while ($user = $stmtUsers->fetch(PDO::FETCH_ASSOC)) {
                                echo "<label style='display:block; margin-bottom:5px;'>
                                        <input type='checkbox' name='users[]' value='".$user['MAIL']."'>
                                        ".$user['NOMPERSONNE']." ".$user['PRENOMPERSONNE']."
                                    </label>"; 
                            }
                        ?>
                    </div>
                    <p style="font-size:12px;color:gray;">(Vous pouvez cocher plusieurs utilisateurs)</p>
                </div>
            <?php } ?>

        </section>

        <script>
        // ----------------------------------------------------------
        // ------- GESTION DE L'IMPORTATION DE FICHIER CSV ----------
        // ----------------------------------------------------------
        // Lorsqu'on clique sur le bouton "Importer"
        document.getElementById("importButton").addEventListener("click", function() {
            document.getElementById("csvFileInput").click(); // Simule un clic sur le champ de sélection de fichier CSV
        });
        document.getElementById("csvFileInput").addEventListener("change", function() {
            // Dès qu’un fichier est sélectionné
            if (confirm("Voulez-vous importer ce fichier ?")) {
                document.getElementById("importForm").submit(); // Soumet le formulaire pour déclencher l’import
            }
        });

        // -----------------------------------------------
        // ----- COMPORTEMENT DE LA CASE "export-date" ---
        // -----------------------------------------------
        document.addEventListener("DOMContentLoaded", function () {
            /**
             * Fonction utilitaire pour afficher/masquer dynamiquement une section liée à une checkbox
             * @param {string} checkboxValue - valeur ciblée de la case
             * @param {string} autreChampId - identifiant du bloc à montrer/cacher
             */
            function setupCheckboxToggle(checkboxValue, autreChampId) {
                // Sélectionne la checkbox avec le bon nom et la bonne valeur
                const checkbox = document.querySelector(`input[name='date-select'][value='${checkboxValue}']`);
                const autreChamp = document.getElementById(autreChampId); // Sélectionne le champ secondaire à afficher

                if (!checkbox || !autreChamp) {
                    console.warn("Un des éléments nécessaires n'a pas été trouvé pour la valeur:", checkboxValue);
                    return;
                }
                // Ajoute un écouteur sur changement d’état de la checkbox
                checkbox.addEventListener("change", function () {
                    if (checkbox.checked) { // Si cochée
                        autreChamp.style.display = "block";     // Affiche la section date quand la case est cochée
                        checkbox.value = "";                    // Réinitialise la valeur (peut être utile pour des soumissions)
                    } else {
                        autreChamp.style.display = "none";      // Cache le champ date si la case est décochée
                        autreChamp.value = "";                  // Réinitialise son contenu
                    }
                });
            }

            // Initialise le comportement pour la case “export-date”
            setupCheckboxToggle("export-date", "export-date-container");
        });

       
    </script>
    </div>
    <?php endif ?>

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
            <a href="/../views/pageadmin.php" class="btn-home" title="Retour à l'accueil">
                <img src="/../public/img/home.png" alt="Accueil" />
            </a>
            <img class="logo-CRAIG" src="/../public/img/CRAIG.png" alt="Logo" style="max-width: 100px; border-radius: 50%;">
        </div>
    </div>
</section>

<script>
   
   document.addEventListener("DOMContentLoaded", function () {
    console.log("Script chargé");

    // Visionneuse principale
    const visionneuseContainer = document.getElementById('visionneuse-container_admin_monEspace');
    if (!visionneuseContainer) {
        console.error(" ERREUR : visionneuse-container_admin_monEspace introuvable !");
        return;
    }

    // Boutons qui déclenchent l’affichage de contenu spécifique
    const buttons = document.querySelectorAll(
        '.dashboard-button-green, .dashboard-button-purple, .dashboard-button-pink, .dashboard-button-blue, .dashboard-button-green-export'
    );

    // Conteneurs de message (erreur ou succès)
    const successContainer = document.querySelector('.success-container');
    const errorContainer = document.querySelector('.error-container');

    // Fonction qui vérifie s’il y a un message affiché
    const hasMessage = () => {
        const successVisible = successContainer && successContainer.innerText.trim() !== '';
        const errorVisible = errorContainer && errorContainer.innerText.trim() !== '';
        return successVisible || errorVisible;
    };

    // Affiche la visionneuse au chargement **uniquement si aucun message n’est présent**
    if (!hasMessage()) {
        visionneuseContainer.style.display = "block";
    } else {
        visionneuseContainer.style.display = "none";
        console.log(" Message détecté → visionneuse masquée");
    }

    // Clic sur un bouton → masquer la visionneuse
    buttons.forEach(button => {
        button.addEventListener("click", function () {
            visionneuseContainer.style.display = "none";
            console.log(" Visionneuse masquée suite au clic bouton");
        });
    });
});


//--------------------------------------------------------------------------------------
//------------------------------- PAGE UTILISATEURS ------------------------------------
//--------------------------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", function () {
    // Récupère le bouton "Gestion des utilisateurs"
    const GestionUsersBtn = document.getElementById("GestionUsersBtn");

    if (GestionUsersBtn) {
        // Écouteur sur le clic du bouton
        GestionUsersBtn.addEventListener("click", function () {
            // URL et spécification des dimensions et options de la nouvelle fenêtre
            const url = "https://guide-asso-m2.geniephy.net/views/Gestion_utilisateurs.php";
            const nomFenetre = "GestionUtilisateurs"; // Nom interne de la fenêtre
            const options = "width=1000,height=700,resizable=yes,scrollbars=yes";

            //Ouvre la page dans une nouvelle fenêtre
            window.open(url, nomFenetre, options);
        });
    } else {
        console.error(" Bouton Gestion utilisateurs introuvable !");
    }
});



document.addEventListener("DOMContentLoaded", function() {
/**
     * Fonction utilitaire pour afficher ou masquer un champ selon la sélection d’une checkbox
     * @param {string} checkboxValue - valeur ciblée de la case à cocher
     * @param {string} autreChampId - identifiant du champ à afficher ou cacher
     */

function setupCheckboxToggle(checkboxValue, autreChampId) {
    // Sélectionne la checkbox correspondante
    const checkbox = document.querySelector(`input[name='date-select'][value='${checkboxValue}']`);

    // Sélectionne le champ cible à afficher/masquer
    const autreChamp = document.getElementById(autreChampId);

    if (!checkbox || !autreChamp) {
        console.warn("Un des éléments nécessaires n'a pas été trouvé pour la valeur:", checkboxValue);
        return;
    }

    // Ajoute un écouteur d’événement sur la case à cocher
    checkbox.addEventListener("change", function() {
        if (checkbox.checked) { // Si cochée, on affiche le champ lié
            autreChamp.style.display = "block";
            checkbox.value = "";  // Réinitialise éventuellement la valeur
        } else { // Si décochée → on masque le champ
            autreChamp.style.display = "none";
            autreChamp.value = "";
        }
    });
}
//  Initialise le comportement de la case "export-date"
setupCheckboxToggle("export-date", "export-date-container"); 
});

</script>



</body>

</html>
