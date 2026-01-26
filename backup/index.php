<?php 
session_start(); 

if (!isset($_SESSION['MAIL'])) {
    header("Location: pageconnexion.php");
    exit();
}

include("/config/BD.phpsession.php");
?> 

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Questionnaire Guid'Asso</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <h3>
        <!--image en haut à droite-->
        <!--img src="guid'asso.jpg" class="top-right-image"-->
        <div class="image-container">
            <img src="LogoRéseau1.png">
            <img src="LogoInformation.png">
            <img src="LogoOrientation1.png">
            <img src="LogoAccompagnementG1.png">
        </div>
        <div class="admin-button-container">
            <?php if ($_SESSION['IDFONCTION']==2) {
             echo '<a href="https://guidasso86.fr/GuidAsso/pageadmin.php" class="dashboard-button">Page Admin</a>';
            }
             ?>
            <a href="https://guidasso86.fr/GuidAsso/deconnexion.php" class="dashboard-button">Se déconnecter</a>
        </div>
        <br>
    </h3>
    
    <div class="bouton-container">
        <h2 >Questionnaire Guid'Asso</h2>
        <div class="button-group">
            <button class="dashboard-button button-label" data-questionnaire="Q&R">Question/Réponse rapide</button>
            <button class="dashboard-button button-label" data-questionnaire="RDV">Rendez-vous / Question écrite</button>
            <button class="dashboard-button button-label" data-questionnaire="evenement">Évènement</button>
            <button class="dashboard-button button-label" data-questionnaire="recherche">Recherche</button>
            <button class="dashboard-button button-label" data-questionnaire="longsuivi">Long suivi</button>
        </div>
    </div>
</div>
    <!-- Questionnaire pour Question / Réponse rapide -->
    <div class="formulaire-container" id="Q&R">
        <form method="post" action="Q&R.php">
            <div class="form-group1">
                <label class="bold" for="type">Type de rendez-vous :</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" class="type" name="type" value="Par mail"> Par mail
                    </label>
                    <label>
                        <input type="radio" class="type" name="type" value="Par téléphone/visio"> Par téléphone/visio 
                    </label>
                    <label>
                        <input type="radio" class="type" name="type" value="En présentiel"> En présentiel 
                    </label>
                </div>
            </div><br>
    
    <div class="form-group">
    <label class="bold" for="assoc">Nom de l'association <span class="etoile">*</span> :</label>
    <input type="text" placeholder="Nom de l'association" id="association1" name="assoc" required>
    </div>  
    <div class="suggestions-box" id="associationSuggestions1"></div>
    
<script>
$(document).ready(function(){
    $('#association1').keyup(function(){
        var query = $(this).val();
        if(query != ''){
            $.ajax({
                url:"recupassoc.php",
                method:"POST",
                data:{query:query},
                success:function(data){
                    $('#associationSuggestions1').fadeIn();
                    $('#associationSuggestions1').html(data);
                }
            });
        }
    });

    // Lorsqu'on clique sur une suggestion
    $(document).on('click', '#associationSuggestions1 ul', function(){
        var selectedAssociation = $(this).text();
        $('#association1').val(selectedAssociation);
        $('#associationSuggestions1').fadeOut();
    });

    // Lorsqu'on survole une suggestion
    $(document).on('mouseover', '#associationSuggestions1 ul', function(){
        $(this).css('background-color', '#abcdf0'); // Changez la couleur de fond ici
    });

    // Lorsqu'on quitte une suggestion
    $(document).on('mouseout', '#associationSuggestions1 ul', function(){
        $(this).css('background-color', ''); // Réinitialise la couleur de fond à celle définie par défaut
    });

    // Lorsqu'on sort du champ de texte
    $('#association1').focusout(function(){
        $('#associationSuggestions1').fadeOut();
    });
});
</script>

<!--          <label class="bold" for="CDC">Communauté de communes <span class="etoile">*</span> : </label>
            <select id="CDC" name="CDC" required>
                <option value="...">...</option>
                <option value="Civraisien-en-poitou">Civraisien-en-Poitou</option>
                <option value="Grand-Châtellerault">Grand-Châtellerault</option>
                <option value="Grand-Poitiers">Grand-Poitiers</option>
                <option value="Haut-Poitou">Haut-Poitou</option>
                <option value="Pays Loudunais">Pays Loudunais</option>
                <option value="Vallées du Clain">Vallées du Clain</option>
                <option value="Vienne-et-Gartempe">Vienne-et-Gartempe</option>
                <option value="Autre">Autre (ex : hors département, ou structure inter EPCI, ou interdépartementale)</option>
            </select>

            <label class="bold" for="CP">Code Postal : </label>
            <input type="text" placeholder="CP" id="CP" name="CP">
-->
    <div class="form-group">
        <label class="bold" for="commune">Commune : </label>
            <input type="text" placeholder="Commune" id="commune" name="commune">
    </div>
    <div id="communeSuggestions" class="suggestions-box"></div>

<script>
$(document).ready(function(){
    $('#commune').keyup(function(){
        var query = $(this).val();
        if(query != ''){
            $.ajax({
                url:"recupcom.php",
                method:"POST",
                data:{query:query},
                success:function(data){
                    $('#communeSuggestions').fadeIn();
                    $('#communeSuggestions').html(data);
                }
            });
        }
    });

   // Lorsque vous cliquez sur une suggestion
   $(document).on('click', '#communeSuggestions ul', function(){
        var selectedCommune = $(this).text();
        $('#commune').val(selectedCommune);
        $('#communeSuggestions').fadeOut();
    });

    // Lorsque la souris survole une suggestion
    $(document).on('mouseover', '#communeSuggestions ul', function(){
        $(this).css('background-color', '#abcdf0'); // Changez la couleur de fond ici
    });

    // Lorsque la souris quitte une suggestion
    $(document).on('mouseout', '#communeSuggestions ul', function(){
        $(this).css('background-color', ''); // Réinitialise la couleur de fond à celle définie par défaut
    });

    // Lorsque vous sortez du champ de texte
    $('#commune').focusout(function(){
        $('#communeSuggestions').fadeOut();
    });
});

</script>
<div class="activites-themes-container">
    <div class="colonne">
    <label class="bold" for="activites"> Activité principale de l'association <span class="etoile">*</span> : </label><br>
        <select id="activites" name="activites" class="longueurraccourcie" required>
            <option value="...">...</option>
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
    <label class="bold" for="sujet">Autres activités de l'association (si besoin):</label>
        <div class="theme-group">
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Culture, loisirs"> Culture, loisirs</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Sport, activités indoor et plein-air"> Sport, activités indoor et plein-air</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Lien social, éducation, insertion, logement"> Lien social, éducation, insertion, logement</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Education populaire, Jeunesse"> Education populaire, Jeunesse</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Caritatif et solidarité"> Caritatif et solidarité</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Service aux personnes, santé et handicap"> Service aux personnes, santé et handicap</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Environnement, écologie et développement durable"> Environnement, écologie et développement durable</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Patrimoine, tourisme"> Patrimoine, tourisme</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Science, recherche, technologies"> Science, recherche, technologies</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Emploi, économie, ESS"> Emploi, économie, ESS</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Sécurité, secours, défense"> Sécurité, secours, défense</label><br>
            </div>
    </div>
</div>
<div class="activites-themes-container">
<div class="colonne">
    <label class="bold" for="sujet">Thématique générale de la question <span class="etoile">*</span> :</label><br>
        <select id="themeG" name="themeG" class="longueurraccourcie" required>
            <option value="...">...</option>
            <option value="Aide aux déclarations">Aide aux déclarations</option>
            <option value="Statuts/ag & projet & gouvernance">Statuts/ag & projet & gouvernance</option>
            <option value="Réglementation & juridique">Réglementation & juridique</option>
            <option value="Evènementiel">Evènementiel</option>
            <option value="Emploi & CCN">Emploi & CCN</option>
            <option value="Comptabilité">Comptabilité</option>
            <option value="Mecenat & financement">Mecenat & financement</option>
            <option value="Fiscalité">Fiscalité</option>
            <option value="Formation">Formation</option>
            <option value="Médiation/Crise">Médiation/Crise</option>
            <option value="Autre">Autre</option>
        </select>
        

    <div id="autreThemeContainer" style="display:none;">
        <label for="autreTheme">Précisez autre :</label>
        <input type="text" id="autreTheme" name="autreTheme" placeholder="Précisez autre">
    </div>

<script>
    document.getElementById("themeG").addEventListener("change", function() {
        var autreThemeContainer = document.getElementById("autreThemeContainer");
        var select = document.getElementById("themeG");

        if (select.value === "Autre") {
            autreThemeContainer.style.display = "block";
        } else {
            autreThemeContainer.style.display = "none";
        }
    });
</script>

</div>
        <div class="colonne">
    <label class="bold" for="sujet">Autres thématiques de la question (si utile) :</label>
        <div class="theme-group">
            <label><input type="checkbox" class="theme" name="theme[]" value="Aide aux déclarations"> Aide aux déclarations</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Statuts/ag & projet & gouvernance"> Statuts/ag & projet & gouvernance</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Réglementation & juridique"> Réglementation & juridique</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Evenementiel"> Evenementiel</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Emploi & CCN"> Emploi & CCN</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Comptabilité"> Comptabilité</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Mecenat & financement"> Mecenat & financement</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Fiscalité"> Fiscalité</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Formation"> Formation</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Médiation/Crise"> Médiation/Crise</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Autre" onclick="afficherChampAutre()"> Autre </label><br>
            <input type="text" id="autreChamp" name="autreChamp" style="display:none;" placeholder="Précisez autre">
            </div>
    </div>
</div>  
        
<script>
                
function afficherChampAutre() {
    console.log("La fonction afficherChampAutre() est appelée.");
    var checkbox = document.querySelector('input[name="theme[]"][value="Autre"]');
    var champ = document.getElementById("autreChamp");
    if (checkbox.checked) {
        champ.style.display = "block";
        checkbox.value="";
    } else {
        champ.style.display = "none";
    }
}

</script>
<div class="form-group">
    <label class="bold" for="Reponsepartagee ">Dossier provenant de / partagé avec / transmis à ? <span class="etoile">*</span>:</label>
        <select id="Reponsepartagee" name="Reponsepartagee" class="longueurraccourcie" required>
            <option value="...">...</option>
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
        </div>
        <div class="form-group">      
    <label class="bold" for="Réponse">Réponse :</label>
    <input type="text" placeholder="Réponse" id="rep" name="rep">
    </div>  
    <button type="submit">Envoyer</button>
</form>
</div>


<!-- Questionnaire pour RDV -->
<!--<div class="questionnaire-container">-->
<div class="formulaire-container" id="RDV">
    <form method="post" action="RDV.php">

    <div class="form-group1">
                <label class="bold" for="type">Type de rendez-vous :</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" class="type" name="type" value="Par mail"> Par mail
                    </label>
                    <label>
                        <input type="radio" class="type" name="type" value="Par téléphone/visio"> Par téléphone/visio 
                    </label>
                    <label>
                        <input type="radio" class="type" name="type" value="En présentiel"> En présentiel 
                    </label>
                </div>
            </div><br>
    <div class="form-group">
    <label class="bold" for="association">Nom de l'association <span class="etoile">*</span> :</label>
    <input type="text" placeholder="Nom de l'association" id="association2" name="association" required>
    </div>  
    <div class="suggestions-box" id="associationSuggestions2"></div>
<script>
$(document).ready(function(){
    $('#association2').keyup(function(){
        var query = $(this).val();
        if(query != ''){
            $.ajax({
                url:"recupassoc.php",
                method:"POST",
                data:{query:query},
                success:function(data){
                    $('#associationSuggestions2').fadeIn();
                    $('#associationSuggestions2').html(data);
                }
            });
        }
    });

    // Lorsqu'on clique sur une suggestion
    $(document).on('click', '#associationSuggestions2 ul', function(){
        var selectedAssociation = $(this).text();
        $('#association2').val(selectedAssociation);
        $('#associationSuggestions2').fadeOut();
    });

    // Lorsqu'on survole une suggestion
    $(document).on('mouseover', '#associationSuggestions2 ul', function(){
        $(this).css('background-color', '#abcdf0'); // Changez la couleur de fond ici
    });

    // Lorsqu'on quitte une suggestion
    $(document).on('mouseout', '#associationSuggestions2 ul', function(){
        $(this).css('background-color', ''); // Réinitialise la couleur de fond à celle définie par défaut
    });

    // Lorsqu'on sort du champ de texte
    $('#association2').focusout(function(){
        $('#associationSuggestions2').fadeOut();
    });
});
</script>

<!--   <label class="bold" for="CDC">Communauté de communes <span class="etoile">*</span> : </label>
    <select id="CDC" name="CDC" required>
        <option value="...">...</option>
        <option value="Civraisien-en-poitou">Civraisien-en-Poitou</option>
        <option value="Grand-Châtellerault">Grand-Châtellerault</option>
        <option value="Grand-Poitiers">Grand-Poitiers</option>
        <option value="Haut-Poitou">Haut-Poitou</option>
        <option value="Pays Loudunais">Pays Loudunais</option>
        <option value="Vallées du Clain">Vallées du Clain</option>
        <option value="Vienne-et-Gartempe">Vienne-et-Gartempe</option>
        <option value="Autre">Autre (ex : hors département, ou structure inter EPCI, ou interdépartementale)</option>
    </select>

    <label class="bold" for="CP">Code postal* :</label>
    <input type="text" placeholder="Code postal" id="CP" name="CP" required>
-->

<div class="form-group">
        <label class="bold" for="commune">Commune : </label>
            <input type="text" placeholder="Commune" id="commune1" name="commune">
    </div>
    <div id="communeSuggestions1" class="suggestions-box"></div>

<script>
$(document).ready(function(){
    $('#commune1').keyup(function(){
        var query = $(this).val();
        if(query != ''){
            $.ajax({
                url:"recupcom.php",
                method:"POST",
                data:{query:query},
                success:function(data){
                    $('#communeSuggestions1').fadeIn();
                    $('#communeSuggestions1').html(data);
                }
            });
        }
    });

    // Lorsque vous cliquez sur une suggestion
    $(document).on('click', '#communeSuggestions1 ul', function(){
        var selectedCommune = $(this).text();
        $('#commune1').val(selectedCommune);
        $('#communeSuggestions1').fadeOut();
    });

    // Lorsque vous survolez une suggestion
    $(document).on('mouseover', '#communeSuggestions1 ul', function(){
        $(this).css('background-color', '#abcdf0'); // Changez la couleur de fond ici
    });

    // Lorsque vous quittez une suggestion
    $(document).on('mouseout', '#communeSuggestions1 ul', function(){
        $(this).css('background-color', ''); // Réinitialise la couleur de fond à celle définie par défaut
    });

    // Lorsque vous sortez du champ de texte
    $('#commune1').focusout(function(){
        $('#communeSuggestions1').fadeOut();
    });
});

</script>
<div class="activites-themes-container">
    <div class="colonne">
    <label class="bold" for="activites"> Activité principale de l'association <span class="etoile">*</span> :  </label>
        <select id="activites" name="activites" class="longueurraccourcie" required><br><br>
            <option value="...">...</option>
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
    <label class="bold" for="sujet">Autres activités de l'association (si besoin) :</label>
        <div class="theme-group">
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Culture, loisirs"> Culture, loisirs</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Sport, activités indoor et plein-air"> Sport, activités indoor et plein-air</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Lien social, éducation, insertion, logement"> Lien social, éducation, insertion, logement</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Education populaire, Jeunesse"> Education populaire, Jeunesse</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Caritatif et solidarité"> Caritatif et solidarité</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Service aux personnes, santé et handicap"> Service aux personnes, santé et handicap</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Environnement, écologie et développement durable"> Environnement, écologie et développement durable</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Patrimoine, tourisme"> Patrimoine, tourisme</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Science, recherche, technologies"> Science, recherche, technologies</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Emploi, économie, ESS"> Emploi, économie, ESS</label><br>
            <label><input type="checkbox" class="act_sec" name="act_sec[]" value="Sécurité, secours, défense"> Sécurité, secours, défense</label><br>
        </div>
        </div>
</div>
<div class="form-group">
    <label class="bold" for="Nom Contact">Nom du Contact <span class="etoile">*</span> :</label>
    <input type="text" placeholder="Nom Contact" id="NcontactInput1" name="nom_contact" required>
    </div>
    <div class="suggestions-box" id="contactSuggestions1"></div>

<script>
$(document).ready(function(){
    $('#NcontactInput1').keyup(function(){
        var query = $(this).val();
        if(query != ''){
            $.ajax({
                url:"recupcontact.php",
                method:"POST",
                data:{query:query},
                success:function(data){
                    $('#contactSuggestions1').fadeIn();
                    $('#contactSuggestions1').html(data);
                }
            });
        }
    });

    // Lorsque vous cliquez sur une suggestion
    $(document).on('click', '#contactSuggestions1 ul', function(){
        var selectedContact = $(this).text();
        $('#NcontactInput1').val(selectedContact);
        $('#contactSuggestions1').fadeOut();
    });

    // Lorsque vous survolez une suggestion
    $(document).on('mouseover', '#contactSuggestions1 ul', function(){
        $(this).css('background-color', '#abcdf0'); // Changez la couleur de fond ici
    });

    // Lorsque vous quittez une suggestion
    $(document).on('mouseout', '#contactSuggestions1 ul', function(){
        $(this).css('background-color', ''); // Réinitialise la couleur de fond à celle définie par défaut
    });

    // Lorsque vous sortez du champ de texte
    $('#NcontactInput1').focusout(function(){
        $('#contactSuggestions1').fadeOut();
    });
});
</script>
<div class="form-group">
    <label class="bold" for="Prénom Contact">Prénom du Contact <span class="etoile">*</span> : </label>
    <input type="text" placeholder="Prénom Contact" id="PcontactInput1" name="prenom_contact" required>
    </div>
    <div class="suggestions-box" id="PcontactSuggestions1"></div>
    
<script>
$(document).ready(function(){
    $('#PcontactInput1').keyup(function(){
        var query = $(this).val();
        if(query != ''){
            $.ajax({
                url:"recupPcontact.php",
                method:"POST",
                data:{query:query},
                success:function(data){
                    $('#PcontactSuggestions1').fadeIn();
                    $('#PcontactSuggestions1').html(data);
                }
            });
        }
    });

    // Lorsque vous cliquez sur une suggestion
    $(document).on('click', '#PcontactSuggestions1 ul', function(){
        var selectedPrenomContact = $(this).text();
        $('#PcontactInput1').val(selectedPrenomContact);
        $('#PcontactSuggestions1').fadeOut();
    });

    // Lorsque vous survolez une suggestion
    $(document).on('mouseover', '#PcontactSuggestions1 ul', function(){
        $(this).css('background-color', '#abcdf0'); // Changez la couleur de fond ici
    });

    // Lorsque vous quittez une suggestion
    $(document).on('mouseout', '#PcontactSuggestions1 ul', function(){
        $(this).css('background-color', ''); // Réinitialise la couleur de fond à celle définie par défaut
    });

    // Lorsque vous sortez du champ de texte
    $('#PcontactInput1').focusout(function(){
        $('#PcontactSuggestions1').fadeOut();
    });
});
</script>
<div class="form-group1">
    <label class="bold" for="type" required>Civilité <span class="etoile">*</span> : </label>
    <div class="radio-group">
    <label>
        <input type="radio" class="genre" name="genre" value="Madame"> Madame
</label>
<label>
        <input type="radio" class="genre" name="genre" value="Monsieur"> Monsieur 
</label>
<label>
        <input type="radio" class="genre" name="genre" value="Nongenre"> Non genré 
 </label>
</div>
</div><br>
      
    <div class="form-group">
    <label class="bold" for="mail">Adresse mail :</label>
    <input type="email" placeholder="Mail" id="mail" name="mail">
    </div>   
<div class="activites-themes-container">
<div class="colonne">
    <label class="bold" for="sujet">Thématique générale de la question <span class="etoile">*</span> :</label>
        <select id="themeG1" name="themeG" class="longueurraccourcie" required><br>
           <option value="...">...</option>
            <option value="Aide aux déclarations">Aide aux déclarations</option>
            <option value="Statuts/ag & projet & gouvernance">Statuts/ag & projet & gouvernance</option>
            <option value="Réglementation & juridique">Réglementation & juridique</option>
            <option value="Evènementiel">Evènementiel</option>
            <option value="Emploi & CCN">Emploi & CCN</option>
            <option value="Comptabilité">Comptabilité</option>
            <option value="Mecenat & financement">Mecenat & financement</option>
            <option value="Fiscalité">Fiscalité</option>
            <option value="Formation">Formation</option>
            <option value="Médiation/Crise">Médiation/Crise</option>
            <option value="Autre">Autre</option>
        </select>

    <div id="autreThemeContainer1" style="display:none;">
        <label for="autreTheme1">Précisez autre :</label>
            <input type="text" id="autreTheme1" name="autreTheme1" placeholder="Précisez autre">
    </div>

<script>
    document.getElementById("themeG1").addEventListener("change", function() {
        var autreThemeContainer = document.getElementById("autreThemeContainer1");
        var select = document.getElementById("themeG1");

        if (select.value === "Autre") {
            autreThemeContainer.style.display = "block";
        } else {
            autreThemeContainer.style.display = "none";
        }
    });
</script>

</div>
        <div class="colonne">
    <label class="bold" for="sujet">Autres thématiques de la question (si utile) :</label>
        <div class="theme-group">
            <label><input type="checkbox" class="theme" name="theme[]" value="Aide aux déclarations"> Aide aux déclarations</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Statuts/ag & projet & gouvernance"> Statuts/ag & projet & gouvernance</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Réglementation & juridique"> Réglementation & juridique</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Evenementiel"> Evenementiel</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Emploi & CCN"> Emploi & CCN</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Comptabilité"> Comptabilité</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Mecenat & financement"> Mecenat & financement</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Fiscalité"> Fiscalité</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Formation"> Formation</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Médiation/Crise"> Médiation/Crise</label><br>
            <label><input type="checkbox" class="theme" name="theme[]" value="Autre1" onclick="afficherChampAutre1()"> Autre </label><br>
            <input type="text" id="autreChamp1" name="autreChamp1" style="display:none;" placeholder="Précisez autre">
        </div>      
        </div>
</div>     
<script>
function afficherChampAutre1() {
    console.log("La fonction afficherChampAutre1() est appelée.");
    var checkbox = document.querySelector('input[name="theme[]"][value="Autre1"]');
    var champ = document.getElementById("autreChamp1");
    if (checkbox.checked) {
        champ.style.display = "block";
        checkbox.value="";
    } else {
        champ.style.display = "none";
    }
}
</script>
<div class="form-group">
    <label class="bold" for="question">Quelle était la question si utile :</label>
    <input type="text" placeholder="Question" id="question" name="question">
    </div>  
    <div class="form-group"> 
    <label class="bold" for="Réponse">Réponse si besoin :</label>
    <input type="text" placeholder="Réponse" id="Réponse" name="reponse">
    </div>   
    <div class="time-input-container">
    <label class="bold">Temps consacré  <span class="etoile">*</span>:</label>
<table>
    <tr>
        <td>
            <label class="bold" for="heures">Heures : </label>
            <input type="text" id="heures" name="heures" pattern="[0-9]+([0-9]*)?" title="Entrez un nombre entier" maxlength="2">
        </td>
        <td>
            <label class="bold" for="minutes">Minutes : </label>
            <input type="text" id="minutes" name="minutes" pattern="[0-9]+([0-9]*)?" title="Entrez un nombre entier entre 0 et 59" maxlength="2" required>
        </td>
    </tr>
</table>
    </div>
    <div class="form-group">
    <label class="bold" for="Recherchepartagee ">Dossier provenant de / partagé avec / transmis à ?<span class="etoile">*</span>:</label>
        <select id="Recherchepartagee" name="Recherchepartagee" class="longueurraccourcie2" required>
            <option value="...">...</option>
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
            <option value="Drive interne / Fiche de synthèse / Ressources">Drive interne / Fiche de synthèse / Ressources</option>
            <option value="Autre">Autre</option>
        </select>
        </div>
        <div class="form-group1">
    <label class="bold" for="permanence"> Rendez-vous dans le cadre d'une permanence ? :</label>
    <div class="radio-group">
        <label>
           <input type="radio" id="oui" name="permanence" value="Oui"> Oui</label>
        </label>
        <label>
           <input type="radio" id="non" name="permanence" value="Non"> Non</label>
           </label>
           </div>
        </div><br>
        <div class="form-group1">
    <label class="bold" for="classification" required>Quelle classification de Guid'Asso ? <span class="etoile">*</span>:</label>
    <div class="radio-group">
            <label>
                <input type="radio" class="classification" name="classification" value="Orientation"> Orientation
                </label>
                    <label>
                <input type="radio" class="classification" name="classification" value="Information"> Information 
                </label>
                    <label>
                <input type="radio" class="classification" name="classification" value="Accompagnement Généraliste"> Accompagnement Généraliste
                </label>
                    <label>
                <input type="radio" class="classification" name="classification" value="Accompagnement Spécialiste"> Accompagnement Spécialiste
                </label>
                </div>
            </div>
    <button type="submit" name="Soumettre">Envoyer</button>
</form>
</div>


<!-- Questionnaire pour Évènement -->
    <!--<div class="questionnaire-container">-->
<div class="formulaire-container" id="evenement">
    <form method="post" action="Evenement.php">
    <div class="form-group">
    <label class="bold" for="evenement">Quel événement ? <span class="etoile">*</span>:</label>
        <select id="evenement" name="evenement" class="longueurraccourcie" required>
            <option value="...">...</option>
            <option value="TourVienne">Tour de la Vienne</option>
            <option value="FormtionCFGA">Formation CFGA</option>
            <option value="Formation">Formation (autre)</option>
            <option value="AtelierGA">Atelier Guid'Asso</option>
            <option value="Atelier">Atelier (autre)</option>
            <option value="Café">Café Guid'Asso</option>
            <option value="ReuTheme">Réunion thématique publique Guid'Asso</option>
            <option value="Rencontres associatives, assises, forums, ect...">Rencontres associatives, assises, forums, ect...</option>
        </select>
        </div> 
        <div class="form-group">   
    <label class="bold" for="sujet">Thématiques abordées <span class="etoile">*</span>:</label>
        <div class="theme-group" required>
            <label><input type="checkbox" class="theme" name="theme[]" value="Aide aux déclarations"> Aide aux déclarations</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Statuts/ag & projet & gouvernance"> Statuts/ag & projet & gouvernance</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Réglementation & juridique"> Réglementation & juridique</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Evenementiel"> Evenementiel</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Emploi & CCN"> Emploi & CCN</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Comptabilité"> Comptabilité</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Mecenat & financement"> Mecenat & financement</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Fiscalité"> Fiscalité</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Formation"> Formation</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Médiation/Crise"> Médiation/Crise</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Autre"> Autre</label><br>
        </div>
        </div>
           <!--<div style="margin-bottom: 20px;"></div>-->
    <div class="form-group">   
    <label class="bold" for="atelier">Titre de l'évènement : </label>
        <input type="text" id="atelier" name="atelier">
        </div>
        <ul id="predictionsTheme"></ul>
        <div class="form-group"> 
    <label class="bold" for="compteur">Nombre de personnes <span class="etoile">*</span> : </label>
        <input type="number" id="champIncremental" oninput="incrementerChamp(this)" name="personne" required>
        </div>
        <div class="form-group"> 
    <label class="bold" for="Date">Date de l'évènement :</label>
        <input type="date" placeholder="Date" id="Date" name="Date" >   
        <div style="margin-bottom: 20px;"></div> 
        </div>
<!--       <label class="bold" for="CDC">Communauté de communes <span class="etoile">*</span> : </label>
           <select id="CDC" name="CDC" required>
               <option value="...">...</option>
               <option value="Civraisien-en-poitou">Civraisien-en-Poitou</option>
               <option value="Grand-Châtellerault">Grand-Châtellerault</option>
               <option value="Grand-Poitiers">Grand-Poitiers</option>
               <option value="Haut-Poitou">Haut-Poitou</option>
               <option value="Pays Loudunais">Pays Loudunais</option>
               <option value="Vallées du Clain">Vallées du Clain</option>
               <option value="Vienne-et-Gartempe">Vienne-et-Gartempe</option>
               <option value="Autre">Autre (ex : hors département, ou structure inter EPCI, ou interdépartementale)</option>
           </select>

           <label class="bold" for="CP">Code postal :</label>
           <input type="text" placeholder="Code postal" id="CP" name="CP">
   
           <label class="bold" for="Lieu">Commune de l'évènement <span class="etoile">*</span> : </label>
           <input type="text" placeholder="Lieu" id="Lieu" name="Lieu" required>
-->
<div class="form-group">
    <label class="bold" for="commune">Commune de l'événement : </label>
        <input type="text" placeholder="Commune" id="commune2" name="commune">
        </div>
        <div id="communeSuggestions2" class="suggestions-box"></div>

<script>
$(document).ready(function(){
    $('#commune2').keyup(function(){
        var query = $(this).val();
        if(query != ''){
            $.ajax({
                url:"recupcom.php",
                method:"POST",
                data:{query:query},
                success:function(data){
                    $('#communeSuggestions2').fadeIn();
                    $('#communeSuggestions2').html(data);
                }
            });
        }
    });

    // Lorsque vous cliquez sur une suggestion
    $(document).on('click', '#communeSuggestions2 ul', function(){
        var selectedCommune = $(this).text();
        $('#commune2').val(selectedCommune);
        $('#communeSuggestions2').fadeOut();
    });

    // Lorsque vous survolez une suggestion
    $(document).on('mouseover', '#communeSuggestions2 ul', function(){
        $(this).css('background-color', '#abcdf0'); // Changez la couleur de fond ici
    });

    // Lorsque vous quittez une suggestion
    $(document).on('mouseout', '#communeSuggestions2 ul', function(){
        $(this).css('background-color', ''); // Réinitialise la couleur de fond à celle définie par défaut
    });

    // Lorsque vous sortez du champ de texte
    $('#commune2').focusout(function(){
        $('#communeSuggestions2').fadeOut();
    });
});
</script>

    <div class="time-input-container">
        <label class="bold">Temps consacré  <span class="etoile">*</span>:</label>
        <table>
    <tr>
        <td>
            <label class="bold" for="heures">Heures : </label>
            <input type="text" id="heures" name="heures" pattern="[0-9]+([0-9]*)?" title="Entrez un nombre entier" maxlength="2">
        </td>
        <td>
            <label class="bold" for="minutes">Minutes : </label>
            <input type="text" id="minutes" name="minutes" pattern="[0-9]+([0-9]*)?" title="Entrez un nombre entier entre 0 et 59" maxlength="2" required>
        </td>
    </tr>
</table>
    </div>
    <div class="form-group">
    <label class="bold" for="emargement">Feuille d'émargement :</label>
        <input type="file" id="emargement" name="emargement" accept=".pdf, .doc, .docx">
        <div style="margin-bottom: 20px;"></div>
        </div>
        <div class="form-group">
    <label class="bold" for="saisie_libre">Commentaire(s) / Détail(s) supplémentaire(s) (si besoin) :</label>
        <input type="text" id="saisie_libre" name="saisie_libre">
        </div>
        <button type="submit">Envoyer</button>
</form>
</div>

<!-- Questionnaire pour Recherche -->
<div class="formulaire-container" id="recherche">
<form method="post" action="Recherche.php">
<div class="activites-themes-container">
<div class="colonne">
    <label class="bold" for="sujet">Thématique générale de la question <span class="etoile">*</span> :</label>
        <select id="themeG2" name="themeG" class="longueurraccourcie" required>
            <option value="...">...</option>
            <option value="Aide aux déclarations">Aide aux déclarations</option>
            <option value="Statuts/ag & projet & gouvernance">Statuts/ag & projet & gouvernance</option>
            <option value="Réglementation & juridique">Réglementation & juridique</option>
            <option value="Evènementiel">Evènementiel</option>
            <option value="Emploi & CCN">Emploi & CCN</option>
            <option value="Comptabilité">Comptabilité</option>
            <option value="Mecenat & financement">Mecenat & financement</option>
            <option value="Fiscalité">Fiscalité</option>
            <option value="Formation">Formation</option>
            <option value="Médiation/Crise">Médiation/Crise</option>
            <option value="Autre">Autre</option>
        </select>
    
    <div id="autreThemeContainer2" style="display:none;">
        <label for="autreTheme">Précisez autre :</label>
        <input type="text" id="autreTheme" name="autreTheme" placeholder="Précisez autre">
    </div>
    
<script>
    document.getElementById("themeG2").addEventListener("change", function() {
        var autreThemeContainer = document.getElementById("autreThemeContainer2");
        var select = document.getElementById("themeG2");

        if (select.value === "Autre") {
            autreThemeContainer.style.display = "block";
        } else {
            autreThemeContainer.style.display = "none";
        }
    });
</script>
</div>
    <div class="colonne">
    <label class="bold" for="sujet">Autres thématiques de la question (si utile) :</label>
        <div class="theme-group">
            <label><input type="checkbox" class="theme" name="theme[]" value="Aide aux déclarations"> Aide aux déclarations</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Statuts/ag & projet & gouvernance"> Statuts/ag & projet & gouvernance</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Réglementation & juridique"> Réglementation & juridique</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Evenementiel"> Evenementiel</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Emploi & CCN"> Emploi & CCN</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Comptabilité"> Comptabilité</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Mecenat & financement"> Mecenat & financement</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Fiscalité"> Fiscalité</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Formation"> Formation</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Médiation/Crise"> Médiation/Crise</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Autre2" onclick="afficherChampAutre2()"> Autre </label>
            <input type="text" id="autreChamp2" name="autreChamp2" style="display:none;" placeholder="Précisez autre">
        </div>
        </div>
</div>        
<script>
function afficherChampAutre2() {
    console.log("La fonction afficherChampAutre() est appelée.");
    var checkbox = document.querySelector('input[name="theme[]"][value="Autre2"]');
    var champ = document.getElementById("autreChamp2");
    if (checkbox.checked) {
        champ.style.display = "block";
        checkbox.value = "";
    } else {
        champ.style.display = "none";
    }
}
</script>
<div class="form-group2">   
    <label class="bold" for="Ressources">Ressource(s) <span class="etoile">*</span> : </label>
        <div class="Ressources" name="Ressources" required>
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
        </div>

    <div class="time-input-container">
        <label class="bold">Temps consacré  <span class="etoile">*</span>:</label>
        <table>
    <tr>
        <td>
            <label class="bold" for="heures">Heures : </label>
            <input type="text" id="heures" name="heures" pattern="[0-9]+([0-9]*)?" title="Entrez un nombre entier" maxlength="2">
        </td>
        <td>
            <label class="bold" for="minutes">Minutes : </label>
            <input type="text" id="minutes" name="minutes" pattern="[0-9]+([0-9]*)?" title="Entrez un nombre entier entre 0 et 59" maxlength="2" required>
        </td>
    </tr>
</table>
    </div>

    <div class="form-group">
    <label class="bold" for="Réponse">Commentaire(s) / Détail(s) supplémentaire(s) si besoin : </label>
        <input type="text" placeholder="Réponse" id="Réponse" name="Réponse">
    </div>

    <div class="form-group">
    <label class="bold" for="Recherchepartagee ">Réseau Guid'Asso : Recherche partagée avec / transmise à <span class="etoile">*</span> :</label>
        <select id="Recherchepartagee" name="Recherchepartagee"class="longueurraccourcie2" required>
            <option value="...">...</option>
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
            <option value="Drive interne / Fiche de synthèse / Ressources">Drive interne / Fiche de synthèse / Ressources</option>
            <option value="Autre">Autre</option>
        </select>
    </div>
    <div class="form-group1">
    <label class="bold">Cette recherche a donné lieu à la création/modification d'une fiche de synthèse :  
        <div class="tooltip">
            <span class="tooltiptext">Merci de la transmettre au chargé de mission pour partage éventuel et diffusion.</span>
            <span class="tooltip-icon">i</span>
        </div>
    </label>
        <div class="radio-group">
            <label>
                <input type="radio" id="oui" name="temps" value="Oui"> Oui
            </label>
            <label>
                <input type="radio" id="non" name="temps" value="Non"> Non
            </label>
        </div>
    </div>
        <div class="form-group1">
            <label class="bold" for="classification" required>Quelle classification de Guid'Asso ? <span class="etoile">*</span>:</label>
            <div class="radio-group">
                <label>
                    <input type="radio" class="classification" name="classification" value="Orientation"> Orientation
                </label>
                <label>
                    <input type="radio" class="classification" name="classification" value="Information"> Information 
                </label>
                <label>
                    <input type="radio" class="classification" name="classification" value="Accompagnement Généraliste"> Accompagnement Généraliste
                </label>
                <label>
                    <input type="radio" class="classification" name="classification" value="Accompagnement Spécialiste"> Accompagnement Spécialiste
                </label>
            </div>
        </div>
<button type="submit">Envoyer</button>
</form>
</div>

<!-- Questionnaire pour Long Suivi -->
<div class="formulaire-container" id="longsuivi">
    <form method="post" action="Longsuivi.php">
    <div class="form-group1">
        <label class="bold" for="type">Type(s) de rendez-vous : 
        <!-- bulle aide -->
            <span class="tooltip">
            <span class="tooltiptext">Plusieurs choix possibles</span>
            <span class="tooltip-icon">i</span>
            </span>
        <!-- fin bulle aide -->
        </label>
        <div class="checkbox-container">
            <label><input type="checkbox" class="type" name="type[]" value="Par mail"> Par mail</label>
            <label><input type="checkbox" class="type" name="type[]" value="Par téléphone/visio"> Par téléphone/visio </label>
            <label><input type="checkbox" class="type" name="type[]" value="En présentiel"> En présentiel</label>
        </div>
    </div>
<br>
    <div class="form-group">
    <label class="bold" for="association">Nom de l'association <span class="etoile">*</span> :</label>
    <input type="text" placeholder="Nom de l'association" id="association3" name="association" required>
    </div>  
    <div class="suggestions-box" id="associationSuggestions3"></div>

<script>
$(document).ready(function(){
    $('#association3').keyup(function(){
        var query = $(this).val();
        if(query != ''){
            $.ajax({
                url:"recupassoc.php",
                method:"POST",
                data:{query:query},
                success:function(data){
                    $('#associationSuggestions3').fadeIn();
                    $('#associationSuggestions3').html(data);
                }
            });
        }
    });

    // Lorsqu'on clique sur une suggestion
    $(document).on('click', '#associationSuggestions3 ul', function(){
        var selectedAssociation = $(this).text();
        $('#association3').val(selectedAssociation);
        $('#associationSuggestions3').fadeOut();
    });

    // Lorsqu'on survole une suggestion
    $(document).on('mouseover', '#associationSuggestions3 ul', function(){
        $(this).css('background-color', '#abcdf0'); // Changez la couleur de fond ici
    });

    // Lorsqu'on quitte une suggestion
    $(document).on('mouseout', '#associationSuggestions3 ul', function(){
        $(this).css('background-color', ''); // Réinitialise la couleur de fond à celle définie par défaut
    });

    // Lorsqu'on sort du champ de texte
    $('#association3').focusout(function(){
        $('#associationSuggestions3').fadeOut();
    });
});
</script>

<!--   <label class="bold" for="CDC">Communauté de communes <span class="etoile">*</span> : </label>
        <select id="CDC" name="CDC" required>
            <option value="...">...</option>
            <option value="Civraisien-en-poitou">Civraisien-en-Poitou</option>
            <option value="Grand-Châtellerault">Grand-Châtellerault</option>
            <option value="Grand-Poitiers">Grand-Poitiers</option>
            <option value="Haut-Poitou">Haut-Poitou</option>
            <option value="Pays Loudunais">Pays Loudunais</option>
            <option value="Vallées du Clain">Vallées du Clain</option>
            <option value="Vienne-et-Gartempe">Vienne-et-Gartempe</option>
            <option value="Autre">Autre (ex : hors département, ou structure inter EPCI, ou interdépartementale)</option>
        </select>

    <label class="bold" for="CP">Code postal <span class="etoile">*</span> :</label>
        <input type="text" placeholder="Code postal" id="CP" name="CP" required>
-->
    <div class="form-group">
    <label class="bold" for="commune">Commune : </label>
            <input type="text" placeholder="Commune" id="commune3" name="commune">
    </div>
    <div id="communeSuggestions3" class="suggestions-box"></div>

<script>
$(document).ready(function(){
    $('#commune3').keyup(function(){
        var query = $(this).val();
        if(query != ''){
            $.ajax({
                url:"recupcom.php",
                method:"POST",
                data:{query:query},
                success:function(data){
                    $('#communeSuggestions3').fadeIn();
                    $('#communeSuggestions3').html(data);
                }
            });
        }
    });

   // Lorsque vous cliquez sur une suggestion
   $(document).on('click', '#communeSuggestions3 ul', function(){
        var selectedCommune = $(this).text();
        $('#commune3').val(selectedCommune);
        $('#communeSuggestions3').fadeOut();
    });

    // Lorsque la souris survole une suggestion
    $(document).on('mouseover', '#communeSuggestions3 ul', function(){
        $(this).css('background-color', '#abcdf0'); // Changez la couleur de fond ici
    });

    // Lorsque la souris quitte une suggestion
    $(document).on('mouseout', '#communeSuggestions3 ul', function(){
        $(this).css('background-color', ''); // Réinitialise la couleur de fond à celle définie par défaut
    });

    // Lorsque vous sortez du champ de texte
    $('#commune3').focusout(function(){
        $('#communeSuggestions3').fadeOut();
    });
});

</script>
<div class="activites-themes-container">
    <div class="colonne">
    <label class="bold" for="act_principale"> Activité principale de l'association <span class="etoile">*</span> : </label>
        <select id="act_principale" name="act_principale" class="longueurraccourcie" required>
            <option value="...">...</option>
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
    <div class="form-group">
    <label class="bold" for="Nom Contact">Nom du Contact <span class="etoile">*</span> :</label>
    <input type="text" placeholder="Nom Contact" id="NcontactInput3" name="nom_contact" required>
    </div>
    <div class="suggestions-box" id="contactSuggestions3"></div>

<script>
$(document).ready(function(){
    $('#NcontactInput3').keyup(function(){
        var query = $(this).val();
        if(query != ''){
            $.ajax({
                url:"recupcontact.php",
                method:"POST",
                data:{query:query},
                success:function(data){
                    $('#contactSuggestions3').fadeIn();
                    $('#contactSuggestions3').html(data);
                }
            });
        }
    });

    // Lorsque vous cliquez sur une suggestion
    $(document).on('click', '#contactSuggestions3 ul', function(){
        var selectedContact = $(this).text();
        $('#NcontactInput3').val(selectedContact);
        $('#contactSuggestions3').fadeOut();
    });

    // Lorsque vous survolez une suggestion
    $(document).on('mouseover', '#contactSuggestions3 ul', function(){
        $(this).css('background-color', '#abcdf0'); // Changez la couleur de fond ici
    });

    // Lorsque vous quittez une suggestion
    $(document).on('mouseout', '#contactSuggestions3 ul', function(){
        $(this).css('background-color', ''); // Réinitialise la couleur de fond à celle définie par défaut
    });

    // Lorsque vous sortez du champ de texte
    $('#NcontactInput3').focusout(function(){
        $('#contactSuggestions3').fadeOut();
    });
});
</script>
    <div class="form-group">
    <label class="bold" for="Prénom Contact">Prénom du Contact <span class="etoile">*</span> : </label>
    <input type="text" placeholder="Prénom Contact" id="PcontactInput3" name="prenom_contact" required>
    </div>
    <div class="suggestions-box" id="PcontactSuggestions3"></div>
    
<script>
$(document).ready(function(){
    $('#PcontactInput3').keyup(function(){
        var query = $(this).val();
        if(query != ''){
            $.ajax({
                url:"recupPcontact.php",
                method:"POST",
                data:{query:query},
                success:function(data){
                    $('#PcontactSuggestions3').fadeIn();
                    $('#PcontactSuggestions3').html(data);
                }
            });
        }
    });

    // Lorsque vous cliquez sur une suggestion
    $(document).on('click', '#PcontactSuggestions3 ul', function(){
        var selectedPrenomContact = $(this).text();
        $('#PcontactInput3').val(selectedPrenomContact);
        $('#PcontactSuggestions3').fadeOut();
    });

    // Lorsque vous survolez une suggestion
    $(document).on('mouseover', '#PcontactSuggestions3 ul', function(){
        $(this).css('background-color', '#abcdf0'); // Changez la couleur de fond ici
    });

    // Lorsque vous quittez une suggestion
    $(document).on('mouseout', '#PcontactSuggestions3 ul', function(){
        $(this).css('background-color', ''); // Réinitialise la couleur de fond à celle définie par défaut
    });

    // Lorsque vous sortez du champ de texte
    $('#PcontactInput3').focusout(function(){
        $('#PcontactSuggestions3').fadeOut();
    });
});
</script>
    <div class="form-group1">
    <label class="bold" for="type" required>Civilité <span class="etoile">*</span> : </label>
        <div class="radio-group">
        <label>
            <input type="radio" class="genre" name="genre" value="Madame"> Madame
        </label>
        <label>
            <input type="radio" class="genre" name="genre" value="Monsieur"> Monsieur 
        </label>
        <label>
            <input type="radio" class="genre" name="genre" value="Nongenre"> Non genré 
        </label>
        </div>
    </div>

    <div class="form-group">
    <label class="bold" for="mail">Adresse mail :</label>
    <input type="email" placeholder="Mail" id="mail" name="mail">
    </div> 
        <div class="activites-themes-container">
        <div class="colonne">
            <label class="bold" for="sujet">Thématique générale de la question <span class="etoile">*</span> :</label>
                <select id="themeG3" name="themeG" class="longueurraccourcie" required>
                    <option value="...">...</option>
                    <option value="Aide aux déclarations">Aide aux déclarations</option>
                    <option value="Statuts/ag & projet & gouvernance">Statuts/ag & projet & gouvernance</option>
                    <option value="Réglementation & juridique">Réglementation & juridique</option>
                    <option value="Evènementiel">Evènementiel</option>
                    <option value="Emploi & CCN">Emploi & CCN</option>
                    <option value="Comptabilité">Comptabilité</option>
                    <option value="Mecenat & financement">Mecenat & financement</option>
                    <option value="Fiscalité">Fiscalité</option>
                    <option value="Formation">Formation</option>
                    <option value="Médiation/Crise">Médiation/Crise</option>
                    <option value="Autre">Autre</option>
                </select>

        <div id="autreThemeContainer3" style="display:none;">
            <label for="autreTheme">Précisez autre :</label>
            <input type="text" id="autreTheme" name="autreTheme" placeholder="Précisez autre">
        </div>

<script>
    document.getElementById("themeG3").addEventListener("change", function() {
        var autreThemeContainer = document.getElementById("autreThemeContainer3");
        var select = document.getElementById("themeG3");

        if (select.value === "Autre") {
            autreThemeContainer.style.display = "block";
        } else {
            autreThemeContainer.style.display = "none";
        }
    });
</script>
</div>
    <div class="colonne">
    <label class="bold" for="sujet">Autres thématiques de la question (si utile) :</label>
        <div class="theme-group">
            <label><input type="checkbox" class="theme" name="theme[]" value="Aide aux déclarations"> Aide aux déclarations</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Statuts/ag & projet & gouvernance"> Statuts/ag & projet & gouvernance</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Réglementation & juridique"> Réglementation & juridique</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Evenementiel"> Evenementiel</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Emploi & CCN"> Emploi & CCN</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Comptabilité"> Comptabilité</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Mecenat & financement"> Mecenat & financement</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Fiscalité"> Fiscalité</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Formation"> Formation</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Médiation/Crise"> Médiation/Crise</label>
            <label><input type="checkbox" class="theme" name="theme[]" value="Autre3" onclick="afficherChampAutre3()"> Autre </label>
            <input type="text" id="autreChamp3" name="autreChamp3" style="display:none;" placeholder="Précisez autre">
        </div>
    </div>
</div>           
<script>
function afficherChampAutre3() {
    console.log("La fonction afficherChampAutre() est appelée.");
    var checkbox = document.querySelector('input[name="theme[]"][value="Autre3"]');
    var champ = document.getElementById("autreChamp3");
    if (checkbox.checked) {
        champ.style.display = "block";
        checkbox.value="";
    } else {
        champ.style.display = "none";
    }
}
</script>
    <div class="form-group">
    <label class="bold" for="recherchepartagee ">Réseau Guid'Asso : Accompagnement suivi avec / partagé avec / transmis à <span class="etoile">*</span> : </label>
        <select id="recherchepartagee" name="recherchepartagee" class="longueurraccourcie2"  required>
            <option value="...">...</option>
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
            <option value="Drive interne / Fiche de synthèse / Ressources">Drive interne / Fiche de synthèse / Ressources</option>
            <option value="Autre">Autre</option>
        </select>
    </div>
    <div class="form-group">
    <label class="bold" for="Temps">Temps consacré ETP : <span class="etoile">*</span>
        <div class="tooltip">
            <span class="tooltiptext">
                ETP équivalent  temps total approximatif
                <br>1 jour>>> (env. 7h total ou 2x1/2journées ou soirées)
                <br> 2 jours>>> (equiv. 12-14h ou 4x1/2journées ou soirées)
                <br> 3 jours>>> (equiv. 18-20h ou 6x1/2journées ou soirées)
                <br> 4 jours>>> (equiv. total 25-30h)
                <br> 5 jours>>> (equiv. total 35h ou semaine entière)
                <br> 6 jours>>> (equiv. cumulé de 6 journées dispersées)
                <br> 1 sem>>> (equiv. 7 jours cumulés dispersés)
                <br> 2 sem>>> (equiv. 10-14 jours dispersés ou consécutifs)
                <br> 1 mois>>> (jours cumulés ou immersion)</span>
                <span class="tooltip-icon">i</span>
        </div> </label>
                <select id="Temps" name="Temps" class="longueurraccourcie2"  required>
                    <option value="...">...</option>
                    <option value="1440">1 Jour</option>
                    <option value="2880">2 Jours</option>
                    <option value="4320">3 Jours</option>
                    <option value="5760">4 Jours</option>
                    <option value="7200">5 Jours</option>
                    <option value="8640">6 Jours</option>
                    <option value="10080">1 Semaine</option>
                    <option value="20160">2 Semaines</option>
                    <option value="40320">1 Mois</option> <!-- je suis partie du principe ou 1 mois = 4 semaines -->
                </select>
    </div>
    <div class="form-group">
    <label class="bold" for="occurence">Nombre de rendez-vous <span class="etoile">*</span> :</label>
        <input type="number" id="champIncremental" name="occurence" oninput="incrementerChamp(this)" required/>
    </div>
    <div class="form-group1">
            <label class="bold" for="classification" required>Quelle classification de Guid'Asso ? <span class="etoile">*</span>:</label>
            <div class="radio-group">
                <label>
                    <input type="radio" class="classification" name="classification" value="Orientation"> Orientation
                </label>
                <label>
                    <input type="radio" class="classification" name="classification" value="Information"> Information 
                </label>
                <label>
                    <input type="radio" class="classification" name="classification" value="Accompagnement Généraliste"> Accompagnement Généraliste
                </label>
                <label>
                    <input type="radio" class="classification" name="classification" value="Accompagnement Spécialiste"> Accompagnement Spécialiste
                </label>
            </div>
    </div>

    <div class="form-group1">
    <label class="bold">Cet accompagnement a-t-il nécessité des recherches ? :
        <div class="tooltip">
            <span class="tooltiptext">Si le suivi a donné lieu à une recherche, merci de remplir le formulaire Recherche</span>
            <span class="tooltip-icon">i</span>
        </div></label>
    
        <div name="acc" required>
            <input type="radio" id="oui" name="temps" value="Oui"> Oui
            <input type="radio" id="non" name="temps" value="Non"> Non
        </div>
    </div>
<button type="submit" class="center-button">Envoyer</button>
</form>
</div>

<script src="script.js"></script>

<!-- Banderole de fin de page -->
<section id="footer" class="footer">
    <div class="footer-content">
        <div class="footer-text">
            <p>Guid'Asso</p>
            <p>Patrice Mancino : 06 07 08 09 10</p>
            <p>Assistance client :  vieasso86@guidasso86.fr </p>
        </div>
        <div class="footer-image">
            <img src="vienneA.png" alt="Logo">
        </div>
    </div>
</section>
</body>
</html>