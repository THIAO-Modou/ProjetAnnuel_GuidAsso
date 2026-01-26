<?php 
session_start(); 
include_once __DIR__ . '/../config/BD.php';
include_once __DIR__ . '/../config/session.php';

if (!isset($_SESSION['MAIL'])) {
    header("Location: pageconnexion.php");
    exit();
}


?> 

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Fiche Association 86</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <!--script pour prédiction -->
    <script src="/../public/js/assoc_prediction.js"></script> <!-- prédiction association-->
    <script src="/../public/js/nom_prenom_asso_prediction.js"></script> <!-- prédiction association-->
    <script src="/../public/js/script_fiche_asso.js" defer></script> <!-- pour le reste -->

    <link rel="icon" href="/../public/img/logo_page.png" type="image/png">

    <link rel="stylesheet" href="/../public/css/style_ficheasso.css">
    <link rel="stylesheet" href="/../public/css/responsive_home.css">
</head>

<body data-show-form="<?= isset($_SESSION['show_form']) ? $_SESSION['show_form'] : '' ?>" >


    <h3>
        <!--image en haut à droite-->
        <!--img src="guid'asso.jpg" class="top-right-image"-->
        <div class="image-container">
            <img src="/../public/img/LogoRéseau1.png">
            <img src="/../public/img/LogoInformation.png">
            <img src="/../public/img/LogoOrientation1.png">
            <img src="/../public/img/LogoAccompagnementG1.png">
        </div>
        <div class="setting-button-container">
            <?php if ($_SESSION['IDFONCTION'] == 2) { ?>
                <a href="https://guide-asso-m2.geniephy.net/views/pageadmin.php" class="setting-button">Page Admin</a>
            
            <?php } elseif ($_SESSION['IDFONCTION'] == 1) { ?>
                <a href="https://guide-asso-m2.geniephy.net/views/mon_espace.php" class="setting-button">Mon espace</a>
            <?php } ?>

            <a href="https://guide-asso-m2.geniephy.net/views/questionnaire.php" class="setting-button">Questionnaire</a>

           <a href="/../config/deconnexion.php" class="setting-button">Se déconnecter</a>

        </div>
        <br>
    </h3>
    
    <!------ tableau de bord ---->
    <section id="dashboard" class="dashboard">
    <h1 class="dashboard-title">Effectuer une recherche</h1>
    <div id="buttons-container" class="buttons-container">
        <button class="dashboard-button-purple" onclick="showForm('association')">Avec le nom de l'association</button>
        <button class="dashboard-button-purple" onclick="showForm('nom')">Avec le nom du contact</button>
    </div>
    </section>

<div id="main-content">

<!---------------------------------------------------------------------------------------->
<!--------------- Recherche avec le nom de l'associsation  ------------------------------->
<!---------------------------------------------------------------------------------------->

<div class="formulaire-container-purple" id="association" 
            style="display: <?= ($show_form === 'association' || !empty($error_message)) ? 'block' : 'none'; ?>;">

        <!-- Message d'erreur --> 
        <?php if (!empty($_SESSION['error_message_FicheAsso'])): ?>
            <div id="error-container" class="error-container" style="display: block;">
                <div class="error-message-form">
                    <span class="error-icon">&#9888;</span>
                    <span id="error-text"><?= htmlspecialchars($_SESSION['error_message_FicheAsso']); ?></span>
                </div>
            </div>
            <?php unset($_SESSION['error_message_FicheAsso']); // Efface après affichage ?>
        <?php endif; ?>

    <!--------------------------- DEBUT DU FORM AVEC ASSOC -------------------------------->
    <section id="QR-form" class="QR-form">
        <form method="post">

        <!-- Nom de l'association avec prédiction --> 
            <div class="form-group">
                <label class="bold" for="assoc">Nom de l'association :</label>
                <input type="text" placeholder="Nom de l'association" id="association_fiche" name="assoc" required>
                <div class="suggestions-box" id="associationSuggestions_fiche"></div>
            </div>  

            <button class="button-purple" type="submit">Rechercher</button>
        </form>
        <br>
        <br>

        <div id="visionneuse-container-fiche" >
                <!-- Tableau des résultats de la recherche -->
            <div id="results-container" >
                <h2>Résultats de la recherche</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Horodateur</th>
                            <th>Thématique générale</th>
                            <th>Thématique secondaire</th>
                            <th>Référent Guid'Asso</th>
                            <th>Orienté par</th>
                            <th>Transmis/Orienté vers</th>
                        </tr>
                    </thead>
                    <tbody id="results-body-assoc">
                        <!-- Les résultats seront insérés ici dynamiquement -->
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<!---------------------------------------------------------------------------------------->
<!------------------------- Recherche avec contact --------------------------------------->
<!---------------------------------------------------------------------------------------->

<div class="formulaire-container-purple " id="nom" 
     style="display: <?= (isset($_SESSION['show_form']) && $_SESSION['show_form'] === 'nom') ? 'block' : 'none'; ?>;">

    <!-- Message d'erreur --> 
    <?php if (!empty($_SESSION['error_message_nom'])): ?>
        <div id="error-container" class="error-container" style="display: block;">
            <div class="error-message-form">
                <span class="error-icon">&#9888;</span>
                <span id="error-text"><?= htmlspecialchars($_SESSION['error_message_nom']); ?></span>
            </div>
        </div>
        <?php unset($_SESSION['error_message_nom']); // Efface après affichage ?>
    <?php endif; ?>

    <!--------------------------- DEBUT DU FORM AVEC NOM CONTACT -------------------------------->
    <section id="QR-form" class="QR-form">
        <form method="post">

            <div class="form-group">
                <label class="bold" for="Email">Nom du contact :</label>
                <input type="text" placeholder="Nom du contact" id="NcontactInput_fiche" name="nom_contact" required>
                <div class="suggestions-box" id="contactSuggestions_fiche"></div>
            </div>

            <button class="button-purple" type="submit" name="Soumettre">Rechercher</button>
        </form>

        <br>
        <br>
            <!-- Tableau des résultats de la recherche -->
        <div id="visionneuse-container-fiche" >
            <div id="results-container1">
                <h2>Résultats de la recherche</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Horodateur</th>
                            <th>Thématique générale</th>
                            <th>Thématique secondaire</th>
                            <th>Référent Guid'Asso</th>
                            <th>Orienté par</th>
                            <th>Transmis/Orienté vers</th>
                        </tr>
                    </thead>
                    <tbody id="results-body-nom">
                            <!-- Les résultats seront insérés ici dynamiquement -->
                    </tbody>
                </table>
            </div>
        </div>
    </section>
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
            <a href="/../views/pageadmin.php" class="btn-home" title="Retour à l'accueil">
                <img src="/../public/img/home.png" alt="Accueil" />
            </a>
            <img src="/../public/img/CRAIG.png" alt="Logo" style="max-width: 100px; border-radius: 50%;">
        </div>
    </div>
</section>


</body>

</html>