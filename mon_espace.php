<?php 
session_start();

error_log("📌 Email stocké en session: " . ($_SESSION['MAIL'] ?? 'Aucun'));


error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['MAIL'])) {
    header("Location: /views/pageconnexion.php");
    exit();
}

include_once __DIR__ . '/../config/BD.php';
include_once __DIR__ . '/../config/session.php';
include_once __DIR__ . '/../controllers/user/get_user_info.php';

    $user="";
    // récupère les infos de l'utilisateur pour le mettre sur le form "mes infos"
    $user = getUserInfo($_SESSION['MAIL']);

    $nom = htmlspecialchars($user['NOMPERSONNE'] ?? 'Non renseigné');
    $prenom = htmlspecialchars($user['PRENOMPERSONNE'] ?? 'Non renseigné');
    $email = htmlspecialchars($user['MAIL'] ?? '');
    $classification = htmlspecialchars($user['CLASSIFICATION'] ?? 'Non classifié');
    $role = isset($user['IDFONCTION']) ? ($user['IDFONCTION'] == 1 ? 'Utilisateur' : 'Administrateur') : 'Non défini';
    
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion Utilisateur</title>

    <!--Pour la responsivité -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="/../public/js/script_mon_espace.js" defer></script>

    <link rel="stylesheet" href="/../public/css/style_mon_espace.css">

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
            Bonjour <span style="color: blue;"><?= htmlspecialchars($_SESSION['MAIL'] ?? 'Utilisateur') ?></span> ! Que souhaitez-vous faire ?
        </h1>
        <div id="buttons-container" class="buttons-container">
            <button class="dashboard-button-green" onclick="showForm('mes-infos')">Informations personnelles</button> 
            <button class="dashboard-button-blue" onclick="showForm('modif-mdp')">Changer mot de passe</button>
            <button class="dashboard-button-purple" onclick="toggleImportExport()">Gestion des documents</button>    
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
            <div id="visionneuse-container">
                <h2>Vos dernières réponses aux questionnaires</h2>
                <div id="visionneuse">
                    <!-- Le tableau sera inséré ici -->
                </div>
            </div>

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
                <form method="post" action="/../../controllers/user/update_info.php">

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

                    <button class="button-vert" type="submit">Mettre à jour mes infos</button>
                </form>
            </section>
        </div>
<!------------------------------------------------------------------->
<!-----------------------CHANGEMENT DE MDP  ------------------------->
<!------------------------------------------------------------------->

        <div class="formulaire-container-bleu" id="modif-mdp" 
            style="display: <?= ($show_form === 'modif-mdp' || !empty($error_message_modif_mdp)) ? 'block' : 'none'; ?>;">
            <section id="mes-infos-form" class="mes-infos-form">
                <h3 class="bold">Modification mot de passe</h3>
                <form method="post" action="/mon_espace/modif_mdp.php" enctype="multipart/form-data">
          
                        <!-- Champ Email (Prérempli) -->
                    <div class="email-form"> 
                        <div class="espace-form-group">
                            <label class="bold" for="email">Adresse mail :</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['MAIL'] ?? '') ?>" readonly>
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
                    <button class="button-bleu" type="submit">Modifier le mot de passe</button>

                </form>
            </section>
        </div>



 <!--------------------------------- IMPORT/EXPORT BDD ------------------------------------>

 <div id="import-export-form-container" class="admin-section" style="display: none;">
        <section id="import-export-form" class="import-export-form">
            <button class="dashboard-button" id="importButton">Import</button>
            <button class="dashboard-button" id="exportButton">Export</button>

    <h1>Liste des pièces jointes associées à des événements</h1>
    <table style="border: 1px solid black; border-collapse: collapse;" cellpadding="10">
        <thead>
            <tr>
            <th>Horodateur</th>    
            <th>Nom du fichier</th>
                <th>Aperçu</th>
            </tr>
        </thead>
        <tbody>
            <?php
            try {
                // Récupérer les fichiers ayant une pièce jointe (FILE non NULL)
                $stmt = $pdo->prepare("SELECT IDQUESTIONNAIRE, FILE, HORODATEUR FROM QUESTIONNAIRE WHERE FILE IS NOT NULL");
                $stmt->execute();
                $files = $stmt->fetchAll();

                // Vérifier si des fichiers existent
                if ($files) {
                    foreach ($files as $file) {
                        $filePath = htmlspecialchars($file['FILE']);
                        $fileName = basename($filePath);
                        $horodateur = htmlspecialchars($file['HORODATEUR']);

                        // Vérifiez que le fichier existe réellement sur le serveur
                        if (file_exists($filePath)) {
                            echo "<tr>
                                <td>$horodateur</td>
                                <td>$fileName</td>
                                <td><a href='$filePath' target='_blank'>Afficher</a></td>
                            </tr>";
                        }
                    }
                } else {
                    echo "<tr><td colspan='3'>Aucun fichier enregistré.</td></tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='3'>Erreur : " . $e->getMessage() . "</td></tr>";
            }
            ?>
        </tbody>
    </table>
        </section>
  </div>


    <script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('exportButton').addEventListener('click', () => {
            fetch('/../database/export.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Erreur HTTP : ${response.status}`);
                    }
                    return response.blob();
                })
                .then(blob => {
                    const link = document.createElement("a");
                    link.href = URL.createObjectURL(blob);
                    link.download = "BDD_GuidAsso86.csv";
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                })
                .catch(error => {
                    console.error("Erreur lors de l'exportation :", error);
                    alert("Une erreur est survenue lors de l'exportation.");
                });
        });
    });
    </script>
    

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
            <img src="/../public/img/vienneA.png" alt="Logo" style="max-width: 100px; border-radius: 50%;">
        </div>
    </div>
</section>


</body>

</html>
