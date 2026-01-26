<?php 
session_start(); 
//Supprimer les indicateurs en session dès l’arrivée sur la page
unset(
    $_SESSION['QR'],
    $_SESSION['RDV'],
    $_SESSION['reseau'],
    $_SESSION['longsuivi'],
    $_SESSION['evenement'],
    $_SESSION['recherche'],
    $_SESSION['anonyme']
);

include_once __DIR__ . '/../config/BD.php';
include_once __DIR__ . '/../config/session.php';

if (!isset($_SESSION['MAIL'])) {
    header("Location: /views/pageconnexion.php");
    exit();
}
$user = getUserInfoByEmail($_SESSION['MAIL']);

if ($user) {
    $nom = $user['NOMPERSONNE'];
    $prenom = $user['PRENOMPERSONNE'];
    $idFonction = $user['IDFONCTION'];
    $classification = $user['CLASSIFICATION'];
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
    <title>Questionnaire Guid'Asso 86</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!--script pour prédiction -->
    <script src="/../public/js/assoc_prediction.js"></script> <!-- prédiction association-->
    <script src="/../public/js/commune_prediction.js"></script>
    <script src="/../public/js/nom_prediction.js"></script>
     
    <script src="/../public/js/prenom_prediction.js"></script> 
    <script src="/../public/js/structure_prediction.js"></script> 

    <script src="/../public/js/script_questionnaire.js" defer></script>
    <script src="/../public/js/ecouteur_activite.js"></script>


    <link rel="icon" href="/../public/img/logo_page.png" type="image/png">

    <link rel="stylesheet" href="/../public/css/style_questionnaire.css">

    <link rel="stylesheet" href="/../public/css/responsive_questionnaire.css">
    <!-- <link rel="stylesheet" href="/../public/css/responsive_home.css"> -->
    
</head>
<body data-show-form="<?= isset($_SESSION['show_form']) ? $_SESSION['show_form'] : '' ?>">
   <script>
    document.addEventListener("DOMContentLoaded", function () {
        //  Supprimer l'état actif visuel et dans le stockage local
        document.querySelectorAll(".dashboard-button-green, .dashboard-button-blue, .dashboard-button-red").forEach(btn => {
            btn.classList.remove("active");
        });
        localStorage.removeItem("activeButton");

        //  Lire le formulaire actif dans l'URL (s'il existe)
        const urlParams = new URLSearchParams(window.location.search);
        const formulaireActif = urlParams.get("formulaire");

        if (formulaireActif) {
            //  Trouver le bouton correspondant au formulaire
            const button = document.querySelector(`[onclick*="${formulaireActif}"]`);
            if (button) {
                button.classList.add("active");
                localStorage.setItem("activeButton", button.id);
            }

            //  Faire défiler jusqu'au formulaire
            setTimeout(() => {
                const targetForm = document.getElementById(formulaireActif);
                if (targetForm) {
                    window.scrollTo({
                        top: targetForm.offsetTop - 50,
                        behavior: "smooth"
                    });
                }
            }, 300);
        }
    });

    function setActiveButton(button, formType) {
        // Réinitialiser tous les boutons
        document.querySelectorAll(".dashboard-button-green, .dashboard-button-blue, .dashboard-button-red").forEach(btn => {
            btn.classList.remove("active");
        });

        // Activer visuellement le bouton cliqué
        button.classList.add("active");

        // Sauvegarder l'état dans localStorage
        localStorage.setItem("activeButton", button.id);

        // Rediriger avec le formulaire sélectionné
        window.location.href = window.location.pathname + "?formulaire=" + formType;
    }

</script>

    <h3>
        <!--image en haut à droite-->
        <!--img src="guid'asso.jpg" class="top-right-image"-->
        <div class="image-container">
	<a href="/../views/pageadmin.php">
    	<img src="/../public/img/LogoRéseau1.png" alt="Logo Réseau">
	</a>
	<a href="/../views/pageadmin.php">
    	<img src="/../public/img/LogoInformation.png" alt="Logo Information">
	</a>
	<a href="/../views/pageadmin.php">
    	<img src="/../public/img/LogoOrientation1.png" alt="Logo Orientation">
	</a>
	<a href="/../views/pageadmin.php">
    	<img src="/../public/img/LogoAccompagnementG1.png" alt="Logo Accompagnement">
	</a>
</div>
        <br>
        <div class="setting-button-container">
            <?php if ($idFonction == 2 || $idFonction == 3) { ?>
                <a href="https://guide-asso-m2.geniephy.net/views/pageadmin.php" class="setting-button"> Page Admin</a>
            
            <?php }if ($idFonction == 1) { ?>
                <a href="https://guide-asso-m2.geniephy.net/views/mon_espace.php" class="setting-button">Mon espace</a>
            <?php } if ($idFonction == 4) { ?>
                <a href="https://guide-asso-m2.geniephy.net/views/mon_espace.php" class="setting-button">Mon espace</a>
            <?php } ?>
            

           <a href="/../config/deconnexion.php" class="setting-button">Se déconnecter</a>

        </div>
    </h3>
    
    <!------ tableau de bord ---->
    <section id="dashboard" class="dashboard">
        <h1 class="dashboard-title">Questionnaire Guid'Asso</h1>
        <div id="buttons-container" class="buttons-container">
            <button id="btnQR" class="dashboard-button dashboard-button-green <?= ($_SESSION['QR'] ?? false) ? 'active' : ''; ?>" onclick="setActiveButton(this, 'QR')">Question/Réponse rapide</button>
            <button id="btnRDV" class="dashboard-button dashboard-button-green <?= ($_SESSION['RDV'] ?? false) ? 'active' : ''; ?>" onclick="setActiveButton(this, 'RDV')">Rendez-vous / Question écrite</button>
            <button id="btnLongsuivi" class="dashboard-button dashboard-button-green <?= ($_SESSION['longsuivi'] ?? false) ? 'active' : ''; ?>" onclick="setActiveButton(this, 'longsuivi')">Long suivi</button>
            <button id="btnReseau" class="dashboard-button dashboard-button-green <?= ($_SESSION['reseau'] ?? false) ? 'active' : ''; ?>" onclick="setActiveButton(this, 'reseau')">Réseau</button>

             <button id="btnAnonyme" class="dashboard-button dashboard-button-red <?= ($_SESSION['anonyme'] ?? false) ? 'active' : ''; ?>" onclick="setActiveButton(this, 'anonyme')">Anonym. / Confid.</button>
             
            <button id="btnRecherche" class="dashboard-button dashboard-button-blue <?= ($_SESSION['recherche'] ?? false) ? 'active' : ''; ?>" onclick="setActiveButton(this, 'recherche')">Recherche & Ressources</button>
            <button id="btnEvenement" class="dashboard-button dashboard-button-blue <?= ($_SESSION['evenement'] ?? false) ? 'active' : ''; ?>" onclick="setActiveButton(this, 'evenement')">Évènement</button>

            <button id="ficheAssocBtn" class="dashboard-button dashboard-button-purple">Fiche association</button>
        </div>
    </section>

    <div id="main-content">

    <!------------------------------------------------------------------->
    <!-----------------------MESSAGES DE SUCCES ------------------------->
    <!------------------------------------------------------------------->

    <!---------------------- message succès pour tous les questionnaires --------------------->
    <?php if (!empty($_SESSION['success_message'])): ?>
        <div id="success-container" class="success-container" style="display: block;">
            <div class="success-message-form">
                <span class="success-icon">&#10004;</span>
                <span id="success-text"><?= htmlspecialchars($_SESSION['success_message']); ?></span>
            </div>
        </div>
        <?php unset($_SESSION['success_message']); // Efface après affichage ?>
    <?php endif; ?>
    

    <?php
        // VARIABLE POUR RECUPERER LE FORMULAIR ACTIF
        $formulaireActif = $_GET['formulaire'] ?? null;
        
        // Definition et stockage des états des boutons en session pour un ussage dans d'autres pages
        $_SESSION['RDV'] = ($formulaireActif === 'RDV'); $RDV = ($formulaireActif ==='RDV');
        $_SESSION['reseau'] = ($formulaireActif === 'reseau');$reseau = ($formulaireActif ==='reseau');
        $_SESSION['QR'] = ($formulaireActif === 'QR'); $QR = ($formulaireActif ==='QR');
        $_SESSION['longsuivi'] = ($formulaireActif === 'longsuivi'); $longsuivi = ($formulaireActif ==='longsuivi');
        $_SESSION['evenement'] = ($formulaireActif === 'evenement'); $evenementform = ($formulaireActif ==='evenement');
        $_SESSION['recherche'] = ($formulaireActif === 'recherche'); $rechercheform = ($formulaireActif ==='recherche');
        $_SESSION['anonyme'] = ($formulaireActif === 'anonyme'); $anonyme = ($formulaireActif ==='anonyme');
        
        if($evenementform || $rechercheform):
            $couleurCadre = "#3a7ee2";
        elseif($QR || $RDV || $longsuivi || $reseau):
            $couleurCadre = "#66c282";
        elseif($anonyme):
            $couleurCadre = "#ae161f";
        endif;

        
        // Liste des formulaires
        $formulaires = ['QR', 'RDV', 'reseau', 'longsuivi', 'evenement', 'recherche', 'anonyme'];

        // Récupérer les formulaires qui sont définis comme true
        $formulaires_actifs = array_filter($formulaires, function ($f) {
            return isset($_SESSION[$f]) && $_SESSION[$f] === true;
        });

        // Si plus d’un est actif, on signale une erreur ou on réinitialise
        if (count($formulaires_actifs) > 1) {
            // désactiver tous les autres formulaires sauf le plus récemment activé
            $dernier = end($formulaires_actifs);
            foreach ($formulaires as $f) {
                $_SESSION[$f] = ($f === $dernier);
            }
        }

    ?>

     <!-- Message d'erreur --> 
            <?php if (!empty($_SESSION['error_message_longsuivi'])): ?>
                <div id="error-container" class="error-container" style="display: block;">
                    <div class="error-message-form">
                        <span class="error-icon">&#9888;</span>
                        <span id="error-text"><?= htmlspecialchars($_SESSION['error_message_longsuivi']); ?></span>
                    </div>
                </div>
                <?php unset($_SESSION['error_message_longsuivi']); // Efface après affichage ?>
            <?php endif; ?>


    <!------------------------------------------------------------------->
    <!------------------VISIONNEUSE ACCUEIL PAGE ------------------------>
    <!------------------------------------------------------------------->
    <?php if(!$formulaireActif) : ?>
        <div id="visionneuse-container">
            <h2>Dernières réponses aux questionnaires</h2>
            <div id="visionneuse">
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
        </div>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="/../public/js/script_questionnaire.js"></script>


    <?php endif; ?>

    <?php
        // Définition des champs à afficher selon le formulaire
        $champsFormulaires = [
            'QR' => ["type_rdv_radio", "assoc_div", "commune_div", "activites-themes-container", "thematique_generale", "classification_guidasso", "bloc_note_div"],
            'RDV' => ["type_rdv_radio", "assoc_div", "commune_div", "activites-themes-container", "employeur", "contact", "email", "thematique_generale", "question_utile", "reponse", "dossier_provenant", "temps_h_mn", "bloc_note_div", "piece_jointe_div","classification_guidasso"],
            'longsuivi' => ["type_rdv", "assoc_div", "commune_div", "activites-themes-container", "employeur", "contact", "email", "thematique_generale", "dossier_provenant",  "temps_consacre", "nombre_rdv", "classification_guidasso", "accompagnement_recherche", "bloc_note_div", "piece_jointe_div"],
            'reseau' => ["type_rdv_radio", "nom_structure", "commune_div", "activite_reseau", "contact", "email", "thematique_generale", "dossier_provenant",  "temps_h_mn", "classification_guidasso", "accompagnement_recherche", "bloc_note_div", "piece_jointe_div"],
            'evenement' => ["type_evenement", "thematique_generale", "titre_evenement", "nombre_personne", "date","commune_div", "temps_h_mn", "feuille_evenement", "commentaire", "bloc_note_div", "piece_jointe_div", "classification_guidasso", "bloc_note_div", "piece_jointe_div"],
            'recherche' => ["thematique_generale", "ressource", "temps_h_mn", "commentaire", "dossier_provenant", "fiche_synthese"],
            'anonyme' => ["type_rdv_radio", "assoc_div", "commune_div", "activites-themes-container", "employeur", "contact", "email", "thematique_generale", "question_utile", "reponse", "temps_h_mn", "dossier_provenant",  "accompagnement_recherche", "bloc_note_div", "piece_jointe_div", "classification_guidasso"]
        ];

    // Vérifier si le formulaire sélectionné existe, sinon mettre un tableau vide
        $champsActifs = $formulaireActif ? ($champsFormulaires[$formulaireActif] ?? []) : [];
        $formulaireActif = $_GET['formulaire'] ?? null;
    ?>
        <div class="formulaire-container-vert" 
            id="<?= htmlspecialchars($formulaireActif) ?>" 
            style="display: <?= (!empty($formulaireActif)) ? 'block' : 'none'; ?>; border: 1px solid <?= htmlspecialchars($couleurCadre) ?>;">

            <!------------------------------------------------->
            <!---------------DEBUT DU FORMULAIRE--------------->
            <!------------------------------------------------->
            <section id="QR-form" class="QR-form">
                <form id="longsuivi" method="post" action="/../controllers/questionnaire/Recupformulaire.php" enctype="multipart/form-data">
                    <?php if ($formulaireActif && !empty($champsActifs) && in_array('type_rdv', $champsActifs)): ?>    
                        <div id="type_rdv" style="display: flex; align-items: center; gap: 15px;">
                            <label class="bold-checkbox" for="type">Type(s) de rendez-vous : <span class="etoile">*</span>
                                <!-- bulle aide -->
                                <span class="tooltip">
                                    <span class="tooltiptext">Plusieurs choix possibles</span>
                                    <img class="tooltip-icon-img" src="/../public/img/info-icon.png" alt="info">
                                </span>
                                <!-- fin bulle aide -->
                            </label>
                            
                            <span>
                                <label><input type="checkbox" class="type" name="type[]" value="Par mail"> Par mail</label>
                                <label><input type="checkbox" class="type" name="type[]" value="Par téléphone/visio"> Par téléphone/visio </label>
                                <label><input type="checkbox" class="type" name="type[]" value="En présentiel"> En présentiel</label>
                            </span>
                        </div><br>

                    <?php endif; ?>
                    <?php if ($formulaireActif && !empty($champsActifs) && in_array('type_rdv_radio', $champsActifs)): ?>    
                        <div id="type_rdv_radio" style="display: flex; align-items: center; gap: 7%px; flex-wrap: wrap;">
                            <label class="bold" for="type" style="margin-right: 7%;">Type de rendez-vous <span class="etoile">*</span>:</label>
                            <div class="radio-group">
                                <label><input type="radio" class="type" name="type_radio" value="Par mail"required> Par mail</label>
                                <label><input type="radio" class="type" name="type_radio" value="Par téléphone/visio"> Par téléphone/visio </label>
                                <label><input type="radio" class="type" name="type_radio" value="En présentiel"> En présentiel </label>
                            </div>
                        </div><br>
                    <?php endif; ?>

                    <!-- Nom de la structure -->
                    <?php if ($formulaireActif && !empty($champsActifs) && in_array('nom_structure', $champsActifs)): ?>
                        <div id="nom_structure" class="form-group">
                            <label class="bold" for="structure">Nom de la structure <span class="etoile">*</span> :</label>
                            <input type="text" placeholder="Nom de la structure" id="structure" name="structure" required>
                            <div class="suggestions-box" id="structureSuggestions"></div>
                        </div>
                    <?php endif; ?> 

                    <?php if ($formulaireActif && !empty($champsActifs) && in_array('assoc_div', $champsActifs)): ?>
                    <div id="assoc_div">
                        <!-- Nom de l'association avec prédiction --> 
                        <div id="assoFieldLS" class="form-group">
                            <label class="bold" for="assoc">Nom de l'association <span class="etoile"><? if(!$anonyme) echo'*'; ?></span> :</label>
                            <input type="text" placeholder="Nom de l'association" id="association" name="assoc" <? if(!$anonyme): ?> required <?endif ?>; >
                            <div class="suggestions-box" id="associationSuggestions"></div>
                        </div> 
                        <!-- Projet asso -->
                       <div class="projetAsso">
                         <label>
                              <input type="radio" name="choixAsso" value="communique">
                                     Nom communiqué
                         </label>

                         <label>
                             <input type="radio" name="choixAsso" value="noncommunique">
                                     Nom non communiqué
                         </label>

                         <label>
                             <input type="radio" name="choixAsso" value="projet">
                                     Projet d'association
                         </label>
                    </div>

                    </div>
                    <?php endif; ?>

                    <?php if ($formulaireActif && !empty($champsActifs) && in_array('type_evenement', $champsActifs)): ?>
                        <div id="type_evenement" class="form-group">
                            <label id="bold-left" class="bold" for="evenement">Quel événement ? <span class="etoile">*</span>:</label>
                                <select id="evenement" name="evenement" class="longueurraccourcie" required>
                                    <option value="">...</option>
                                    <option value="Tour de la Vienne">Tour de la Vienne</option>
                                    <option value="Formation CFGA">Formation CFGA</option>
                                    <option value="Formation (autre)">Formation (autre)</option>
                                    <option value="Atelier Guid'Asso">Atelier Guid'Asso</option>
                                    <option value="Atelier (autre)">Atelier (autre)</option>
                                    <option value="Café Guid'Asso">Café Guid'Asso</option>
                                    <option value="Réunion thématique">Réunion thématique publique Guid'Asso</option>
                                    <option value="Rencontres associatives">Rencontres associatives, assises, forums, etc...</option>
                                </select>
                        </div>
                    <?php endif; ?> 

                     <?php if ($formulaireActif && !empty($champsActifs) && in_array('titre_evenement', $champsActifs)): ?>
                        <div id="titre_evenement" class="form-group">   
                        <label class="bold" for="atelier">Titre de l'évènement : </label>
                        <input type="text" id="atelier" name="titre_ev">
                        </div>
                        <ul id="predictionsTheme"></ul>
                    <?php endif; ?>

                    
                    <?php if ($formulaireActif && !empty($champsActifs) && in_array('nombre_personne', $champsActifs)): ?>
                        <div id="nombre_personne" class="form-group"> 
                            <label class="bold" for="compteur">Nombre de personnes <span class="etoile">*</span> : </label>
                            <input type="number" id="champIncremental" oninput="incrementerChamp(this)" name="personne" required>
                        </div>
                    <?php endif; ?>

                    <?php if ($formulaireActif && !empty($champsActifs) && in_array('date', $champsActifs)): ?>
                        <div id="date" class="form-group"> 
                        <label class="bold" for="Date">Date de l'évènement <span class="etoile">*</span> :</label>
                        <input type="date" placeholder="Date" id="Date" name="Date" required >   
                        <div style="margin-bottom: 20px;"></div> 
                    </div>
                    <?php endif; ?>
                    

                    <!-------------------COMMUNE------------------>
                    <?php if ($formulaireActif && !empty($champsActifs) && in_array('commune_div', $champsActifs)): ?>

                    <div id="commune_div"> 
                        <!-- Nom de la commune  avec prédiction -->
                        <div id="communeFieldLongsuivi" class="form-group">
                            <label class="bold" for="commune">Commune<span class="etoile"> <?php if(!$QR) echo'*'; ?> </span> : </label>
                            <input type="text" placeholder="Commune" id="commune" name="commune" >
                            <div id="communeSuggestions" class="suggestions-box"></div>
                        </div>
                        <!--  HORS DEPARTEMENT -->
                        <div class="horsDep">
                            <input class="checkbox" type="checkbox" id="horsDepartement" name="horsDepartementCheckbox">
                            <label class="bold" for="horsDepartementCheckbox">: Hors département</label>
                        </div>
                    </div><br>
                    <?php endif; ?>

                    <?php if ($formulaireActif && !empty($champsActifs) && in_array('activite_reseau', $champsActifs)): ?>
                        <div id="activite_reseau" class="activites-themes-container">
                            <label class="bold" for="activite"> Activité au sein du réseau <span class="etoile">*</span> :  </label>
                            <select id="activites" name="activite" class="longueurraccourcie" required><br><br>
                                <option value="">...</option>
                                <option value="Orientation">Orientation</option>
                                <option value="Information">Information</option>
                                <option value="Acc Généraliste">Accompagnement généraliste</option>
                                <option value="Acc Spécialiste">Accompagnement Spécialiste</option>
                                <option value="Greffe des associations">Greffe des associations</option>
                                <option value="DLA">DLA</option>
                                <option value="Tête de réseau">Tête de réseau</option>
                                <option value="Mairie, ComCom, EPCI,...">Mairie, ComCom, EPCI,...</option>
                                <option value="Département, Région,...">Département, Région,...</option>
                                <option value="Structure autre">Structure autre</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if ($formulaireActif && !empty($champsActifs) && in_array('activites-themes-container', $champsActifs)): ?> 
                        <div id="activites-themes-container" class="colonne_div">
                            <div class="colonne">
                                <label class="bold" for="act_principale"> Activité principale de l'association <span class="etoile">*</span> : </label>
                                <select id="act_principale" name="act_principale" class="longueurraccourcie" required>
                                    <option value="">...</option>
                                    <option value="Culture, loisirs">Culture, loisirs</option>
                                    <option value="Sport, activités indoor et plein-air">Sport, activités indoor et plein-air</option>
                                    <option value="Lien social, éducation, insertion, logement">Lien social, éducation, insertion, logement</option>
                                    <option value="ducation populaire, Jeunesse">Education populaire, Jeunesse</option>
                                    <option value="Caritatif et solidarité">Caritatif et solidarité</option>
                                    <option value="Service aux personnes, santé et handicap">Service aux personnes, santé et handicap</option>
                                    <option value="Environnement, écologie et développement durable">Environnement, écologie et développement durable</option>
                                    <option value="Patrimoine, tourisme">Patrimoine, tourisme</option>
                                    <option value="Science, recherche, technologies">Science, recherche, technologies</option>
                                    <option value="Emploi, économie, ESS">Emploi, économie, ESS</option>
                                    <option value="Sécurité, secours, défense">Sécurité, secours, défense</option>
                                </select>
                            </div>

                            <div class="colonne">
                            <label class="bold" for="sujet">Autres activités de l'association :</label>
                                <div class="theme-group">
                                    <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Culture, loisirs"> Culture, loisirs</label>
                                    <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Sport, activités indoor et plein-air"> Sport, activités indoor et plein-air</label>
                                    <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Lien social, éducation, insertion, logement"> Lien social, éducation, insertion, logement</label>
                                    <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Education populaire, Jeunesse"> Education populaire, Jeunesse</label>
                                    <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Caritatif et solidarité"> Caritatif et solidarité</label>
                                    <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Service aux personnes, santé et handicap"> Service aux personnes, santé et handicap</label>
                                    <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Environnement, écologie et développement durable"> Environnement, écologie et développement durable</label>
                                    <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Patrimoine, tourisme"> Patrimoine, tourisme</label>
                                    <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Science, recherche, technologies"> Science, recherche, technologies</label>
                                    <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Emploi, économie, ESS"> Emploi, économie, ESS</label>
                                    <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Sécurité, secours, défense"> Sécurité, secours, défense</label>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($formulaireActif && !empty($champsActifs) && in_array('employeur', $champsActifs)): ?>    
                        <div id="employeur">
                        <label class="bold" for="employeur">Association employeuse ? :</label>
                        <div class="radio-group-employeur">
                            <label><input type="radio" class="type" name="employeur" value="Oui"> Oui</label>
                            <label><input type="radio" class="type" name="employeur" value="Non"> Non</label>
                            <label><input type="radio" class="type" name="employeur" value="N/A"> N/A</label>
                        </div>
                        </div>

                    <?php endif; ?>

                    <div id="contact">
                    <?php if ($formulaireActif && !empty($champsActifs) && in_array('contact', $champsActifs)): ?>
                    <div id="contact">
                        <div id="nom_contact" class="form-group" >
                        <label class="bold" for="Nom Contact">Nom du Contact <span class="etoile"><? if($longsuivi || $evenementform || $rechercheform || $RDV):?>*<? endif?></span> :</label>
                        <input type="text" placeholder="Nom Contact" id="NcontactInput3" name="nom_contact" <? if($longsuivi || $evenementform || $rechercheform || $RDV): ?>required <? endif ?>>
                        <div class="suggestions-box" id="contactSuggestions3"></div>
                    </div>
        
                    <div id="prenom_contact" class="form-group" >
                        <label class="bold" for="Prénom Contact">Prénom du Contact : </label>
                        <input type="text" placeholder="Prénom Contact" id="PcontactInput3" name="prenom_contact">
                        <div class="suggestions-box" id="PcontactSuggestions3"></div>
                    </div>
                
                    <div id="civilite" >
                        <label class="bold" for="type" required>Civilité <span class="etoile"><? if(!$anonyme) echo'*' ?></span> : </label>
                        <div class="radio-group">
                            <label><input type="radio" class="genre" name="genre" value="Madame" required> Madame</label>
                            <label><input type="radio" class="genre" name="genre" value="Monsieur" > Monsieur </label>
                            <label><input type="radio" class="genre" name="genre" value="Non genré" > Non genré </label>
                        </div>
                    </div>
                    </div>
                    
                <?php endif; ?>
                </div>

                <?php if ($formulaireActif && !empty($champsActifs) && in_array('email', $champsActifs)): ?>
                    <div id="email" class="form-group" >
                        <label class="bold" for="mail">Adresse mail :</label>
                        <input type="email" placeholder="Mail" id="mail" name="mail">
                    </div>
                <?php endif; ?>

                <?php if ($formulaireActif && !empty($champsActifs) && in_array('thematique_generale', $champsActifs)): ?>
                    <? if(!$rechercheform){ ?><br><? }?> <div id="thematique_generale" class="colonne_div">
                        <div class="colonne" style="width: 10px;">
                            <label class="bold" for="sujet">Thématique générale de <? if($evenementform) echo"l'evenement";?> <? if(!$evenementform) echo "la question";?> <span class="etoile">*</span> :</label>
                            <select id="themeG3" name="themeG" class="longueurraccourcie" required>
                                <option value="">...</option>
                                <option value="Aide aux déclarations">Aide aux déclarations</option>
                                <option value="Statuts/ag & projet & gouvernance">Statuts/ag & projet & gouvernance</option>
                                <option value="Engagement bénévole">Engagement bénévole</option>
                                <option value="Réglementation & juridique">Réglementation & juridique</option>
                                <option value="Evènementiel">Evènementiel</option>
                                <option value="Emploi & CCN">Emploi & CCN</option>
                                <option value="Comptabilité">Comptabilité</option>
                                <option value="Mecenat & financement">Mecenat & financement</option>
                                <option value="Fiscalité">Fiscalité</option>
                                <option value="Formation">Formation</option>
                                <option value="Dissolution">Dissolution</option>
                                <option value="Médiation/Crise">Médiation/Crise</option>
                                <option value="Autre">Autre</option>
                            </select>
                            <div id="autreThemeContainer3" style="display:none;">
                                <label for="autreTheme">Précisez autre :</label><input type="text" id="autreTheme3" name="autreTheme" placeholder="Précisez autre">
                            </div>
                        </div>
                        <div class="colonne">
                        <label class="bold" for="sujet">Autres thématiques de <? if($evenementform) echo"l'evenement";?> <? if(!$evenementform ) echo "la question";?> (si utile) :</label>
                            <div class="theme-group">
                                <label><input type="checkbox" class="theme" name="theme[]" value="Aide aux déclarations"> Aide aux déclarations</label>
                                <label><input type="checkbox" class="theme" name="theme[]" value="Statuts/ag & projet & gouvernance"> Statuts/ag & projet & gouvernance</label>
                                <label><input type="checkbox" class="theme" name="theme[]" value="Engagement bénévole"> Engagement bénévole</label>
                                <label><input type="checkbox" class="theme" name="theme[]" value="Réglementation & juridique"> Réglementation & juridique</label>
                                <label><input type="checkbox" class="theme" name="theme[]" value="Evenementiel"> Evenementiel</label>
                                <label><input type="checkbox" class="theme" name="theme[]" value="Emploi & CCN"> Emploi & CCN</label>
                                <label><input type="checkbox" class="theme" name="theme[]" value="Comptabilité"> Comptabilité</label>
                                <label><input type="checkbox" class="theme" name="theme[]" value="Mecenat & financement"> Mecenat & financement</label>
                                <label><input type="checkbox" class="theme" name="theme[]" value="Fiscalité"> Fiscalité</label>
                                <label><input type="checkbox" class="theme" name="theme[]" value="Formation"> Formation</label>
                                <label><input type="checkbox" class="theme" name="theme[]" value="Disolution"> Dissolution</label>
                                <label><input type="checkbox" class="theme" name="theme[]" value="Médiation/Crise"> Médiation/Crise</label>
                                <label><input type="checkbox" class="theme" name="theme[]" value="Autre3" onclick="afficherChampAutre3()"> Autre </label>
                                <input type="text" id="autreChamp3" name="autreChamp" style="display:none;" placeholder="Précisez autre">
                            </div>
                        </div>
                    </div> 
                <?php endif;?> 

                <?php if ($formulaireActif && !empty($champsActifs) && in_array('ressource', $champsActifs)): ?>
                    <!-- Ressources -->
                <div id="ressource" class="activites-themes-container">   
                    <div > 
                    <label class="bold" for="Ressources">Ressource(s) <span class="etoile">*</span> : </label>
                        <div class="theme-group" name="Ressources" id="ressourcesGroup" required>
                            <label><input type="checkbox" class="ressources" name="ressources[]" value="associations.gouv.fr">associations.gouv.fr</label>
                            <label><input type="checkbox" class="ressources" name="ressources[]" value="legifrance / service-public / urssaf">legifrance / service-public / urssaf</label>
                            <label><input type="checkbox" class="ressources" name="ressources[]" value="mémento pratique / guide pratique de l'association (Ligue) / mallette associative">mémento pratique / guide pratique de l'association (Ligue) / mallette associative</label>
                            <label><input type="checkbox" class="ressources" name="ressources[]" value="sites tiers (assoconnect, associathèque, MAIF, ...)">sites tiers (assoconnect, associathèque, MAIF, ...)</label>
                            <label><input type="checkbox" class="ressources" name="ressources[]" value="membre labellisé Guid'Asso">membre labellisé Guid'Asso</label>
                            <label><input type="checkbox" class="ressources" name="ressources[]" value="partenaire Guid'Asso et/ou administration">partenaire Guid'Asso et/ou administration</label>
                            <label><input type="checkbox" class="ressources" name="ressources[]" value="Drive Guid'Asso">Drive Guid'Asso</label>
                            <label><input type="checkbox" class="ressources" name="ressources[]" value="Recherche internet">Recherche internet</label>
                            <label><input type="checkbox" class="ressources" name="ressources[]" value="Autre">Autre</label>
                        </div>
                        <p class="error" id="ressourcesError" style="display:none;">
                            <span class="error-icon"></span>
                            <span class="error-text">Veuillez sélectionner au moins une ressource.</p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($formulaireActif && !empty($champsActifs) && in_array('question_utile', $champsActifs)): ?>             
                    <div id="question_utile" class="form-group" >
                        <label class="bold" for="question">Quelle était la question si utile :</label>
                        <input type="text" placeholder="Question" id="question" name="question">
                    </div>
                <?php endif; ?>  

                <?php if ($formulaireActif && !empty($champsActifs) && in_array('reponse', $champsActifs)): ?>
                    <div id="reponse" class="form-group" > 
                        <label class="bold" for="Réponse">Réponse si besoin :</label>
                        <input type="text" placeholder="Réponse" id="Réponse" name="reponse">
                    </div>
                <?php endif; ?>

                <?php if ($formulaireActif && !empty($champsActifs) && in_array('temps_consacre', $champsActifs)): ?>
                    <div id="temps_consacre" class="form-group" >
                    <label class="bold" for="Temps">Temps consacré ETP ouvrés <span class="etoile">*</span>:
                        <div class="tooltip">
                            <span class="tooltiptext">
                                ETP équivalent temps total approximatif
                                <br>1 jour>>> (env. 7h total ou 2x1/2journées ou soirées)
                                <br> 2 jours>>> (equiv. 12-14h ou 4x1/2journées ou soirées)
                                <br> 3 jours>>> (equiv. 18-20h ou 6x1/2journées ou soirées)
                                <br> 4 jours>>> (equiv. total 25-30h)
                                <br> 5 jours>>> (equiv. total 35h ou semaine entière)
                                <br> 6 jours>>> (equiv. cumulé de 6 journées dispersées)
                                <br> 1 sem>>> (equiv. 7 jours cumulés dispersés)
                                <br> 2 sem>>> (equiv. 10-14 jours dispersés ou consécutifs)
                                <br> 1 mois>>> (jours cumulés ou immersion)</span>
                                <img class="tooltip-icon-img" src="/../public/img/info-icon.png" alt="info">
                        </div> </label>
                            <select id="Temps" name="Temps" class="longueurraccourcie2" required>
                                <option value="">...</option>
                                <option value="420">1 Jour</option>
                                <option value="840">2 Jours</option>
                                <option value="1260">3 Jours</option>
                                <option value="1680">4 Jours</option>
                                <option value="2100">5 Jours</option>
                                <option value="2520">6 Jours</option>
                                <option value="2940">1 Semaine</option>
                                <option value="5880">2 Semaines</option>
                                <option value="13020">1 Mois</option> <!-- je suis partie du principe ou 1 mois = 4 semaines -->
                            </select>
                    </div>
                <?php endif; ?>

                <?php if ($formulaireActif && !empty($champsActifs) && in_array('nombre_rdv', $champsActifs)): ?>
                    <div id="nombre_rdv" class="form-group" >
                        <label class="bold" for="occurence">Nombre de rendez-vous <span class="etoile">*</span> :</label>
                        <input type="number" id="champIncremental" name="occurence" oninput="incrementerChamp(this)" required/>
                    </div>
                <?php endif; ?>

                <!------------------------------------------------------------------------------->
                <!------------------------ A active avec le id au besoin ------------------------>
                <!------------------------------------------------------------------------------->
                <?php if ($formulaireActif && !empty($champsActifs) && in_array('rdv_dans_la_cadre', $champsActifs)): ?>   
                    <div id = "rdv_dans_la_cadre" class="form-group-radio-bouton">
                        <label class="bold" for="permanence"> Rendez-vous dans le cadre d'une permanence ? :</label>
                        <div class="radio-group">
                            <label><input type="radio" id="oui" name="permanence" value="Oui"> Oui</label></label>
                            <label><input type="radio" id="non" name="permanence" value="Non"> Non</label></label>
                        </div>
                    </div><br>
                <?php endif; ?>

                <?php if ($formulaireActif && !empty($champsActifs) && in_array('temps_h_mn', $champsActifs)): ?>
                    <div id="temps_h_mn" class="time-input-container">
                        <label class="bold">Temps consacré  <span class="etoile">*</span>:
                            
                        </label>
                        <label class="bold" for="heures">Heures : </label>
                            <input type="text" id="heures" name="heures" pattern="^\d*$" title="Entrez un nombre entier positif">

                        <label class="bold" for="minutes">Minutes : </label>
                            <input type="text" id="minutes" name="minutes" pattern="^[0-5]?[0-9]$" maxlength="2" title="Entrez un nombre entier entre 0 et 59" required>
                    </div>
                <?php endif; ?>

                <!-- Classification guid'Asso-->
                <?php if ($formulaireActif && !empty($champsActifs) && in_array('classification_guidasso', $champsActifs)): ?>
                    <div id="classification_guidasso">
                            <label class="bold" for="classification" required>Quelle classification de Guid'Asso ? <span class="etoile">*</span>:</label>
                            <div class="radio-group">
                                <label><input type="radio" class="classification" name="classification" value="Orientation" required> Orientation</label>
                                <label><input type="radio" class="classification" name="classification" value="Information"> Information</label>
                                <label><input type="radio" class="classification" name="classification" value="Accompagnement Généraliste"> Accompagnement Généraliste</label>
                                <label><input type="radio" class="classification" name="classification" value="Accompagnement Spécialiste"> Accompagnement Spécialiste</label>
                            </div>
                        </div><br>
                <?php endif; ?>

                <!-- Dossier provenant de... -->
                 
                <?php if ($formulaireActif && !empty($champsActifs) && in_array('dossier_provenant', $champsActifs)): ?>
                <br><div id="dossier_provenant" class="colonne_div">
                        <div class="colonne">
                                <label class="bold" for="provenantde" > <?php if($longsuivi || $reseau || $RDV || $QR || $anonyme) echo"Dossier provenant de / orienté par";?>
                                                                <?php if($rechercheform) echo"Recherche provenant de / orienté par";?> :</label>
                            <select id="Reponsepartagee" name="Reponsepartagee" class="longueurraccourcie" data-target="autre_provenance">
                                <option value="">...</option>
                                <option value="Préfecture / Sous-Préfecture">Préfecture / Sous-Préfecture</option>
                                <option value="Mairie / ComCom / EPCI">Mairie / ComCom / EPCI</option>
                                <option value="SDJES">SDJES</option>
                                <option value="ORIENTATION - Point Labellisé">ORIENTATION - Point Labellisé</option>
                                <option value="INFORMATION - Référent Guid'Asso 86">INFORMATION - Référent Guid'Asso 86</option>
                                <option value="ACCOMPAGNEMENT - Guid'Asso">ACCOMPAGNEMENT - Guid'Asso</option>
                                <option value="SPECIALISTE - DLA">SPECIALISTE - DLA</option>
                                <option value="DREETS, ect">DREETS, etc ...</option>
                                <option value="Département">Département</option>
                                <option value="Région">Région</option>
                                <option value="Autre">Autre</option>
                           </select>
                        
                            <div id="autre_provenance" class="autre-field" style="display:none;">
                             <label class="bold">Précisez :</label>
                                 <input type="text" name="provenance_autre" class="longueurraccourcie">
                            </div>
                        </div>
                        


                        <!-- Dossier partagé avec... -->
                        <div class="colonne">
                            <label class="bold" for="transmisa"> <?php if($longsuivi || $reseau || $RDV || $QR || $anonyme) echo"Dossier partagé avec / transmis à";?>
                                                                <?php if($rechercheform) echo"Recherche partagé avec / transmis à";?> :</label>
                            <select id="Reponsepartagee1" name="Reponsepartagee1" class="longueurraccourcie" data-target="autre_partage">
                                <option value="">...</option>
                                <option value="Préfecture / Sous-Préfecture">Préfecture / Sous-Préfecture</option>
                                <option value="Mairie / ComCom / EPCI">Mairie / ComCom / EPCI</option>
                                <option value="SDJES">SDJES</option>
                                <option value="CDOS"> CDOS</option>
                                <option value="Ligue de l'Enseignement">Ligue de l'Enseignement</option>
                                <option value="Environnement, écologie et développement durable">Environnement, écologie et développement durable</option>
                                <option value="ORIENTATION - Point Labellisé">ORIENTATION - Point Labellisé</option>
                                <option value="INFORMATION - Référent Guid'Asso 86">INFORMATION - Référent Guid'Asso 86</option>
                                <option value="ACCOMPAGNEMENT - Guid'Asso">ACCOMPAGNEMENT - Guid'Asso</option>
                                <option value="SPECIALISTE - DLA">SPECIALISTE - DLA</option>
                                <option value="DREETS, ect">DREETS, ect</option>
                                <option value="Département">Département</option>
                                <option value="Région">Région</option>
                                <option value="Autre">Autre</option>
                            </select>
                            <div id="autre_partage" class="autre-field" style="display:none;">
                                 <label class="bold">Précisez :</label>
                                    <input type="text" name="partage_autre" class="longueurraccourcie">
                            </div>
                        </div>
                        
                    </div>


                <?php endif; ?>

                <?php if ($formulaireActif && !empty($champsActifs) && in_array('feuille_evenement', $champsActifs)): ?>
                    <div id="feuille_evenement" class="form-group">
                        <label class="bold" for="emargement">Feuille d'émargement :</label>
                        <input type="file" id="emargement" name="emargement" accept=".pdf, .doc, .docx">
                        <div style="margin-bottom: 20px;"></div>
                    </div>
                <?php endif; ?>
                
                <?php if ($formulaireActif && !empty($champsActifs) && in_array('commentaire', $champsActifs)): ?>
                    <div id="commentaire" class="form-group">
                        <label class="bold" for="saisie_libre">Commentaire(s) / Détail(s) supplémentaire(s) (si besoin) :</label>
                        <input type="text" id="saisie_libre" name="saisie_libre">
                    </div>
                <?php endif; ?>

                <?php if ($formulaireActif && !empty($champsActifs) && in_array('bloc_note_div', $champsActifs)): ?>
                    <div id="bloc_note_div" > 
                        <!-- Bloc note -->   
                         <div class="form-group"> 
                            <label class="bold" for="BN">Bloc note personnel :</label>
                            <textarea type="text" placeholder="Bloc note" id="BN" name="BN" rows="4" cols="50"></textarea>
                        </div> 
                          <!-- Piece jointe -->
                        <? if(!$QR){ ?>
                             <div class="form-group" style="margin-left: 15%;">
                                    <label class="bold" for="PJ">Pièce jointe bloc-note :</label>
                                    <input type="file" id="PJ" name="PJ" accept=".pdf, .doc, .docx">

                                    <div id="pj-error" style="display:none; margin-top:8px; color:#b00020;">
                                            ❌ Le fichier dépasse <strong>2,5 Mo</strong>.
                                                         Veuillez le compresser via
                                                     <a href="https://www.ilovepdf.com/compress_pdf" target="_blank" rel="noopener noreferrer"> iLovePDF </a>.
                                    </div>

                                <div style="margin-bottom: 20px;"></div>
                            </div>
                        <? } ?>

                    </div>
                <?php endif; ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const inputPJ = document.getElementById("PJ");
    const errorPJ = document.getElementById("pj-error");

    if (!inputPJ || !errorPJ) return;

    const MAX_SIZE = 2.5 * 1024 * 1024; // 2,5 Mo

    inputPJ.addEventListener("change", function () {
        const file = this.files[0];
        if (!file) return;

        if (file.size > MAX_SIZE) {
            errorPJ.style.display = "block";
            this.value = ""; // bloque le fichier
        } else {
            errorPJ.style.display = "none";
        }
    });
});
</script>



                    <!-- Piece jointe -->
                <?php if ($formulaireActif && !empty($champsActifs) && in_array('piece_jointe_div', $champsActifs)): ?>
                    
                <?php endif; ?>

                <?php if ($formulaireActif && !empty($champsActifs) && in_array('accompagnement_recherche', $champsActifs)): ?>
                    <div id="accompagnement_recherche">
                        <label class="bold">Cet accompagnement a-t-il nécessité des recherches ? :</label>
                            <input type="radio" id="accompagnement" name="recherche" value="Oui"> Oui
                            <input type="radio" id="non" name="recherche" value="Non"> Non
                    </div>
                <?php endif; ?>

                <?php if ($formulaireActif && !empty($champsActifs) && in_array('fiche_synthese', $champsActifs)): ?>
                    <!-- Transmission -->
                    <div id="fiche_synthese" class="form-groupRecherche">
                        <label class="bold-2">Cette recherche a donné lieu à la création/modification d'une fiche de synthèse :
                            
                        </label>
                        <div class="radio-group" >
                            <label><input type="radio" id="oui" name="temps" value="Oui"> Oui </label>
                            <label><input type="radio" id="non" name="temps" value="Non"> Non </label>
                        </div>
                    </div>
                <?php endif; ?>

                <? if($idFonction ==1 || $idFonction == 2){
                    echo'<button class="button-vert" type="submit" class="center-button">Envoyer</button>';
                }    
                 else {
                 echo'<span class="tooltip">
                    <button class="button-desactive" style="background-color: #c0c0c0;" disabled>Envoyer</button>
                    <span class="tooltiptext">Cette option n\'est pas disponible pour vous.</span>
                    </span>';
                 }?>
                </form>
            </section>         
        </div>
    </div>




<!------------------------------------------------------------------------------->
<!--------------------------------- FOOTER -------------------------------------->
<!------------------------------------------------------------------------------->

    <section id="footer" class="footer">
    <div class="footer-content">
        <div class="footer-text">
            <p>Guid'Asso</p>
            <p>Patrice Mancino : 06 07 08 09 10</p>
            <p>Assistance client : vieasso86@guidasso86.fr</p>
        </div>
        <div class="footer-image">
             
            <a 
            <?php if ($idFonction == 2 || $idFonction == 3)  echo'href="/../views/pageadmin.php"' ?>
            <?php if ($idFonction == 1 || $idFonction == 4)  echo'href="/../views/mon_espace.php"' ?>
            class="btn-home" title="Retour à l'accueil">
                <img src="/../public/img/home.png" alt="Accueil" />
            </a>
            <img src="/../public/img/CRAIG.png" alt="Logo" style="max-width: 100px; border-radius: 50%;">
        </div>
    </div>
</section>
<br>
<br>



</body>

</html>