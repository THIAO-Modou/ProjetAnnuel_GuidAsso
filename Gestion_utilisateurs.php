<?php 
session_start(); 
include_once __DIR__ . '/../config/BD.php';
include_once __DIR__ . '/../config/session.php';

if (!isset($_SESSION['MAIL'])) {
    header("Location: pageconnexion.php");
    exit();
}

//  Récupération des infos utilisateur
$user = getUserInfoByEmail($_SESSION['MAIL']);

if ($user) {
    $data = [
        "NOMPERSONNE" => $user['NOMPERSONNE'],
        "PRENOMPERSONNE" => $user['PRENOMPERSONNE'],
        "IDFONCTION" => $user['IDFONCTION'],
        "CLASSIFICATION" => $user['CLASSIFICATION']
    ];
} else {
    $data = ["error" => "Utilisateur non trouvé"];
}

//  Envoi des infos sous forme de JSON accessible en JavaScript
echo "<script>var userData = " . json_encode($data) . ";</script>";
?> 

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Utilisateurs Guid'Asso</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <!--script pour prédiction -->
   
    <script src="/../public/js/script_utilisateurs.js" defer></script> <!-- pour le reste -->
    <script src="/../pulic/js/ecouteur_activite.js"></script>

    <link rel="icon" href="/../public/img/logo_page.png" type="image/png">

    <link rel="stylesheet" href="/../public/css/style_utilisateurs.css">
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
                <a href="https://guide-asso-m2.geniephy.net/views//mon_espace.php" class="setting-button">Mon espace</a>
            <?php } ?>

            <a href="https://guide-asso-m2.geniephy.net/views//questionnaire.php" class="setting-button">Questionnaire</a>

           <a href="/../config/deconnexion.php" class="setting-button">Se déconnecter</a>

        </div>
        <br>
    </h3>
    
   

<div id="main-content">

    <!------------------------------------------------------------------->
    <!----------------------- VISIONNEUSE UTILISATEURS ------------------->
    <!------------------------------------------------------------------->


    <div id="visionneuse-container">
        <h2>Liste des utilisateurs de Guid'Asso</h2>
        <div id="Users_visionneuse">
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
            <img src="/../public/img/vienneA.png" alt="Logo" style="max-width: 100px; border-radius: 50%;">
        </div>
    </div>
</section>


</body>

</html>