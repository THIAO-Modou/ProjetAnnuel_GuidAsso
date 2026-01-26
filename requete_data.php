<?php

include 'connexion.php'; // connexion bdd

// récupération des données par la méthode POST
$graph = $_POST['choix'];

switch ($graph) {
    case 'graph1':
        $sql = "SELECT 
                    CASE 
                        WHEN LOWER(epci) LIKE '%grand-poitiers%' THEN 'Grand-Poitiers'
                        WHEN LOWER(epci) LIKE '%non%' THEN 'Hors département'
                        WHEN LOWER(epci) LIKE '%haut%' THEN 'Haut-Poitou'
                        WHEN LOWER(epci) LIKE '%vienne%' THEN 'Vienne-et-Gartempe'
                        WHEN LOWER(epci) LIKE '%grand-ch%' THEN 'Grand-Châtellerault'
                        WHEN LOWER(epci) LIKE '%pays%' THEN 'Pays Loudunais'
                        WHEN LOWER(epci) LIKE '%civ%' THEN 'Civraisien-en-Poitou'
                        WHEN LOWER(epci) LIKE '%val%' THEN 'Vallées du Clain'
                        ELSE 'Non renseigné'
                    END AS categorie,
                    COUNT(*) AS total
                FROM ASSOCIATION 
                GROUP BY categorie";
        break;

        case 'graph2':
            $sql = "SELECT 
                        categorie,
                        COUNT(*) AS total
                    FROM (
                        SELECT 
                            CASE
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%emploi%' THEN 'Emploi, économie, ESS'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%patrimoine%' THEN 'Patrimoine, tourisme'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%servic%' THEN 'Service aux personnes, santé et handicap'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sport%' THEN 'Sport, activités indoor et plein-air'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%lien%' THEN 'Lien social, éducation, insertion, logement'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%culture%' THEN 'Culture, loisirs'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%caritatif%' THEN 'Caritatif et solidarité'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%environnement%' THEN 'Environnement, écologie et dévelop. durable'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%science%' THEN 'Science, recherche, technologies'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sécurité%' THEN 'Sécurité, secours, défense'
                                WHEN ACTIVITEPRINCIPALEASSO IS NULL OR ACTIVITEPRINCIPALEASSO = '' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%...%' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%indéfini%' THEN 'Non renseigné'
                                ELSE 'Autre'
                            END AS categorie
                        FROM ASSOCIATION
                    ) AS temp
                    GROUP BY categorie";
            break;
        

    case 'graph3':
        $sql = "SELECT 
                        categorie,
                        COUNT(*) AS total
                    FROM (
                        SELECT 
                            CASE
                                WHEN LOWER(THEMEGENERAL) LIKE '%aide%' THEN 'Aide aux déclarations'
                                WHEN LOWER(THEMEGENERAL) LIKE '%statu%' THEN 'Statuts/ag & projet & gouvernance'
                                WHEN LOWER(THEMEGENERAL) LIKE '%regl%' THEN 'Réglementation & juridique'
                                WHEN LOWER(THEMEGENERAL) LIKE '%even%' THEN 'Evenementiel'
                                WHEN LOWER(THEMEGENERAL) LIKE 'mecen%' THEN 'Mecenat & financement'
                                WHEN LOWER(THEMEGENERAL) LIKE '%compta%' THEN 'Comptabilité'
                                WHEN LOWER(THEMEGENERAL) LIKE '%fiscal%' THEN 'Fiscalité'
                                WHEN LOWER(THEMEGENERAL) LIKE '%emploi%' THEN 'Emploi & CCN'
                                WHEN LOWER(THEMEGENERAL) LIKE '%formation%' THEN 'Formation'
                                WHEN LOWER(THEMEGENERAL) LIKE '%disso%' THEN 'Dissolution'
                                WHEN THEMEGENERAL IS NULL THEN 'Non renseigné'
                                ELSE 'Autre'
                            END AS categorie
                        FROM QUESTIONNAIRE
                        ) AS temp
                        GROUP BY categorie";
            break;

    case 'graph4':
    $sql = "SELECT 
                categorie,
                COUNT(*) AS total
            FROM (
                SELECT 
                    CASE 
                        WHEN LOWER(NATUREECHANGE) LIKE '%e-mail%' OR LOWER(NATUREECHANGE) LIKE '%mail%' THEN 'Par mail'
                        WHEN LOWER(NATUREECHANGE) LIKE '%téléphone%' THEN 'Par téléphone/visio'
                        WHEN LOWER(NATUREECHANGE) LIKE '%rendez-vous%' OR LOWER(NATUREECHANGE) LIKE '%présentiel%' OR LOWER(NATUREECHANGE) LIKE '%permanence%' THEN 'En présentiel'
                        ELSE 'Non renseigné'
                    END AS categorie
                FROM QUESTIONNAIRE
            ) AS temp
            GROUP BY categorie";
    break;

        

    case 'graph5':
        $sql = "SELECT 
                    categorie,
                    COUNT(*) AS total
                FROM (
                    SELECT 
                        CASE
                            WHEN LOWER(CLASSIFICATIONGUIDASSO) LIKE '%information%' THEN 'Orientation'
                            WHEN LOWER(CLASSIFICATIONGUIDASSO) LIKE '%orientation%' THEN 'Information'
                            WHEN LOWER(CLASSIFICATIONGUIDASSO) LIKE '%accompagnement spécialiste%' THEN 'Accompagnement spécialiste'
                            WHEN LOWER(CLASSIFICATIONGUIDASSO) LIKE '%accompagnement généraliste%' THEN 'Accompagnement généraliste'
                            ELSE 'Non renseigné'
                        END AS categorie
                    FROM QUESTIONNAIRE
                ) AS temp
                GROUP BY categorie";
        break;
    

    // Graphs liés aux activités principales des associations par EPCI
    case 'graph6':
        $sql = "SELECT 
                    categorie,
                    COUNT(*) AS total
                FROM (
                    SELECT 
                        CASE
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%emploi%' THEN 'Emploi, économie, ESS'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%patrimoine%' THEN 'Patrimoine, tourisme'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%servic%' THEN 'Service aux personnes, santé et handicap'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sport%' THEN 'Sport, activités indoor et plein-air'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%lien%' THEN 'Lien social, éducation, insertion, logement'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%culture%' THEN 'Culture, loisirs'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%caritatif%' THEN 'Caritatif et solidarité'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%environnement%' THEN 'Environnement, écologie et dévelop. durable'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%science%' THEN 'Science, recherche, technologies'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sécurité%' THEN 'Sécurité, secours, défense'
                            WHEN ACTIVITEPRINCIPALEASSO IS NULL OR ACTIVITEPRINCIPALEASSO = '' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%...%' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%indéfini%' THEN 'Non renseigné'
                            ELSE 'Autre'
                        END AS categorie
                    FROM ASSOCIATION
                    WHERE epci = 'Grand-Poitiers'
                ) AS temp
                GROUP BY categorie";
        break;
    

        case 'graph7':
            $sql = "SELECT 
                        categorie,
                        COUNT(*) AS total
                    FROM (
                        SELECT 
                            CASE
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%emploi%' THEN 'Emploi, économie, ESS'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%patrimoine%' THEN 'Patrimoine, tourisme'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%servic%' THEN 'Service aux personnes, santé et handicap'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sport%' THEN 'Sport, activités indoor et plein-air'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%lien%' THEN 'Lien social, éducation, insertion, logement'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%culture%' THEN 'Culture, loisirs'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%caritatif%' THEN 'Caritatif et solidarité'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%environnement%' THEN 'Environnement, écologie et dévelop. durable'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%science%' THEN 'Science, recherche, technologies'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sécurité%' THEN 'Sécurité, secours, défense'
                                WHEN ACTIVITEPRINCIPALEASSO IS NULL OR ACTIVITEPRINCIPALEASSO = '' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%...%' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%indéfini%' THEN 'Non renseigné'
                                ELSE 'Autre'
                            END AS categorie
                        FROM ASSOCIATION
                        WHERE epci = 'Haut-Poitou'
                    ) AS temp
                    GROUP BY categorie";
            break;
        

case 'graph8':
    $sql = "SELECT 
                categorie,
                COUNT(*) AS total
            FROM (
                SELECT 
                    CASE
                        WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%emploi%' THEN 'Emploi, économie, ESS'
                        WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%patrimoine%' THEN 'Patrimoine, tourisme'
                        WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%servic%' THEN 'Service aux personnes, santé et handicap'
                        WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sport%' THEN 'Sport, activités indoor et plein-air'
                        WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%lien%' THEN 'Lien social, éducation, insertion, logement'
                        WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%culture%' THEN 'Culture, loisirs'
                        WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%caritatif%' THEN 'Caritatif et solidarité'
                        WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%environnement%' THEN 'Environnement, écologie et dévelop. durable'
                        WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%science%' THEN 'Science, recherche, technologies'
                        WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sécurité%' THEN 'Sécurité, secours, défense'
                        WHEN ACTIVITEPRINCIPALEASSO IS NULL OR ACTIVITEPRINCIPALEASSO = '' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%...%' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%indéfini%' THEN 'Non renseigné'
                        ELSE 'Autre'
                    END AS categorie
                FROM ASSOCIATION
                WHERE epci = 'Vallées du clain'
            ) AS temp
            GROUP BY categorie";
    break;

    case 'graph9':
        $sql = "SELECT 
                    categorie,
                    COUNT(*) AS total
                FROM (
                    SELECT 
                        CASE
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%emploi%' THEN 'Emploi, économie, ESS'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%patrimoine%' THEN 'Patrimoine, tourisme'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%servic%' THEN 'Service aux personnes, santé et handicap'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sport%' THEN 'Sport, activités indoor et plein-air'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%lien%' THEN 'Lien social, éducation, insertion, logement'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%culture%' THEN 'Culture, loisirs'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%caritatif%' THEN 'Caritatif et solidarité'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%environnement%' THEN 'Environnement, écologie et dévelop. durable'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%science%' THEN 'Science, recherche, technologies'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sécurité%' THEN 'Sécurité, secours, défense'
                            WHEN ACTIVITEPRINCIPALEASSO IS NULL OR ACTIVITEPRINCIPALEASSO = '' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%...%' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%indéfini%' THEN 'Non renseigné'
                            ELSE 'Autre'
                        END AS categorie
                    FROM ASSOCIATION
                    WHERE epci = 'Grand-Châtellerault'
                ) AS temp
                GROUP BY categorie";
        break;
     

    case 'graph10':
        $sql = "SELECT 
        categorie,
        COUNT(*) AS total
    FROM (
        SELECT 
            CASE
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%emploi%' THEN 'Emploi, économie, ESS'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%patrimoine%' THEN 'Patrimoine, tourisme'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%servic%' THEN 'Service aux personnes, santé et handicap'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sport%' THEN 'Sport, activités indoor et plein-air'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%lien%' THEN 'Lien social, éducation, insertion, logement'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%culture%' THEN 'Culture, loisirs'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%caritatif%' THEN 'Caritatif et solidarité'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%environnement%' THEN 'Environnement, écologie et dévelop. durable'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%science%' THEN 'Science, recherche, technologies'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sécurité%' THEN 'Sécurité, secours, défense'
                WHEN ACTIVITEPRINCIPALEASSO IS NULL OR ACTIVITEPRINCIPALEASSO = '' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%...%' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%indéfini%' THEN 'Non renseigné'
                ELSE 'Autre'
            END AS categorie
        FROM ASSOCIATION
        WHERE epci = 'Vienne-et-Gartempe'
    ) AS temp
    GROUP BY categorie";
break;

    case 'graph11':
        $sql = "SELECT 
        categorie,
        COUNT(*) AS total
    FROM (
        SELECT 
            CASE
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%emploi%' THEN 'Emploi, économie, ESS'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%patrimoine%' THEN 'Patrimoine, tourisme'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%servic%' THEN 'Service aux personnes, santé et handicap'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sport%' THEN 'Sport, activités indoor et plein-air'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%lien%' THEN 'Lien social, éducation, insertion, logement'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%culture%' THEN 'Culture, loisirs'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%caritatif%' THEN 'Caritatif et solidarité'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%environnement%' THEN 'Environnement, écologie et dévelop. durable'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%science%' THEN 'Science, recherche, technologies'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sécurité%' THEN 'Sécurité, secours, défense'
                WHEN ACTIVITEPRINCIPALEASSO IS NULL OR ACTIVITEPRINCIPALEASSO = '' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%...%' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%indéfini%' THEN 'Non renseigné'
                ELSE 'Autre'
            END AS categorie
        FROM ASSOCIATION
        WHERE epci = 'Civraisien-en-Poitou'
    ) AS temp
    GROUP BY categorie";
break;

    case 'graph12':
        $sql = "SELECT 
        categorie,
        COUNT(*) AS total
    FROM (
        SELECT 
            CASE
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%emploi%' THEN 'Emploi, économie, ESS'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%patrimoine%' THEN 'Patrimoine, tourisme'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%servic%' THEN 'Service aux personnes, santé et handicap'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sport%' THEN 'Sport, activités indoor et plein-air'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%lien%' THEN 'Lien social, éducation, insertion, logement'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%culture%' THEN 'Culture, loisirs'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%caritatif%' THEN 'Caritatif et solidarité'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%environnement%' THEN 'Environnement, écologie et dévelop. durable'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%science%' THEN 'Science, recherche, technologies'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sécurité%' THEN 'Sécurité, secours, défense'
                WHEN ACTIVITEPRINCIPALEASSO IS NULL OR ACTIVITEPRINCIPALEASSO = '' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%...%' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%indéfini%' THEN 'Non renseigné'
                ELSE 'Autre'
            END AS categorie
        FROM ASSOCIATION
        WHERE epci = 'Pays loudunais'
    ) AS temp
    GROUP BY categorie";
break;
            

    // Graphs liés aux thèmes généraux des questionnaires par EPCI
    case 'graph13':
        $sql = "SELECT 
                        categorie,
                        COUNT(*) AS total
                    FROM (
                        SELECT 
                            CASE
                                WHEN LOWER(THEMEGENERAL) LIKE '%aide%' THEN 'Aide aux déclarations'
                                WHEN LOWER(THEMEGENERAL) LIKE '%statu%' THEN 'Statuts/ag & projet & gouvernance'
                                WHEN LOWER(THEMEGENERAL) LIKE '%regl%' THEN 'Réglementation & juridique'
                                WHEN LOWER(THEMEGENERAL) LIKE '%even%' THEN 'Evenementiel'
                                WHEN LOWER(THEMEGENERAL) LIKE 'mecen%' THEN 'Mecenat & financement'
                                WHEN LOWER(THEMEGENERAL) LIKE '%compta%' THEN 'Comptabilité'
                                WHEN LOWER(THEMEGENERAL) LIKE '%fiscal%' THEN 'Fiscalité'
                                WHEN LOWER(THEMEGENERAL) LIKE '%emploi%' THEN 'Emploi & CCN'
                                WHEN LOWER(THEMEGENERAL) LIKE '%formation%' THEN 'Formation'
                                WHEN LOWER(THEMEGENERAL) LIKE '%disso%' THEN 'Dissolution'
                                WHEN THEMEGENERAL IS NULL THEN 'Non renseigné'
                                ELSE 'Autre'
                            END AS categorie
                        FROM QUESTIONNAIRE
                        WHERE epci = 'Grand-Poitiers'
                        ) AS temp
                        GROUP BY categorie";
        break;

    case 'graph14':
        $sql = "SELECT 
                        categorie,
                        COUNT(*) AS total
                    FROM (
                        SELECT 
                            CASE
                                WHEN LOWER(THEMEGENERAL) LIKE '%aide%' THEN 'Aide aux déclarations'
                                WHEN LOWER(THEMEGENERAL) LIKE '%statu%' THEN 'Statuts/ag & projet & gouvernance'
                                WHEN LOWER(THEMEGENERAL) LIKE '%regl%' THEN 'Réglementation & juridique'
                                WHEN LOWER(THEMEGENERAL) LIKE '%even%' THEN 'Evenementiel'
                                WHEN LOWER(THEMEGENERAL) LIKE 'mecen%' THEN 'Mecenat & financement'
                                WHEN LOWER(THEMEGENERAL) LIKE '%compta%' THEN 'Comptabilité'
                                WHEN LOWER(THEMEGENERAL) LIKE '%fiscal%' THEN 'Fiscalité'
                                WHEN LOWER(THEMEGENERAL) LIKE '%emploi%' THEN 'Emploi & CCN'
                                WHEN LOWER(THEMEGENERAL) LIKE '%formation%' THEN 'Formation'
                                WHEN LOWER(THEMEGENERAL) LIKE '%disso%' THEN 'Dissolution'
                                WHEN THEMEGENERAL IS NULL THEN 'Non renseigné'
                                ELSE 'Autre'
                            END AS categorie
                        FROM QUESTIONNAIRE
                        WHERE epci = 'Haut-Poitou'
                        ) AS temp
                        GROUP BY categorie";
        break;

    case 'graph15':
        $sql = "SELECT 
        categorie,
        COUNT(*) AS total
    FROM (
        SELECT 
            CASE
                WHEN LOWER(THEMEGENERAL) LIKE '%aide%' THEN 'Aide aux déclarations'
                WHEN LOWER(THEMEGENERAL) LIKE '%statu%' THEN 'Statuts/ag & projet & gouvernance'
                WHEN LOWER(THEMEGENERAL) LIKE '%regl%' THEN 'Réglementation & juridique'
                WHEN LOWER(THEMEGENERAL) LIKE '%even%' THEN 'Evenementiel'
                WHEN LOWER(THEMEGENERAL) LIKE 'mecen%' THEN 'Mecenat & financement'
                WHEN LOWER(THEMEGENERAL) LIKE '%compta%' THEN 'Comptabilité'
                WHEN LOWER(THEMEGENERAL) LIKE '%fiscal%' THEN 'Fiscalité'
                WHEN LOWER(THEMEGENERAL) LIKE '%emploi%' THEN 'Emploi & CCN'
                WHEN LOWER(THEMEGENERAL) LIKE '%formation%' THEN 'Formation'
                WHEN LOWER(THEMEGENERAL) LIKE '%disso%' THEN 'Dissolution'
                WHEN THEMEGENERAL IS NULL THEN 'Non renseigné'
                ELSE 'Autre'
            END AS categorie
        FROM QUESTIONNAIRE
        WHERE epci = 'Vallées du Clain'
        ) AS temp
        GROUP BY categorie";
break;
             
    case 'graph16':
        $sql = "SELECT 
        categorie,
        COUNT(*) AS total
    FROM (
        SELECT 
            CASE
                WHEN LOWER(THEMEGENERAL) LIKE '%aide%' THEN 'Aide aux déclarations'
                WHEN LOWER(THEMEGENERAL) LIKE '%statu%' THEN 'Statuts/ag & projet & gouvernance'
                WHEN LOWER(THEMEGENERAL) LIKE '%regl%' THEN 'Réglementation & juridique'
                WHEN LOWER(THEMEGENERAL) LIKE '%even%' THEN 'Evenementiel'
                WHEN LOWER(THEMEGENERAL) LIKE 'mecen%' THEN 'Mecenat & financement'
                WHEN LOWER(THEMEGENERAL) LIKE '%compta%' THEN 'Comptabilité'
                WHEN LOWER(THEMEGENERAL) LIKE '%fiscal%' THEN 'Fiscalité'
                WHEN LOWER(THEMEGENERAL) LIKE '%emploi%' THEN 'Emploi & CCN'
                WHEN LOWER(THEMEGENERAL) LIKE '%formation%' THEN 'Formation'
                WHEN LOWER(THEMEGENERAL) LIKE '%disso%' THEN 'Dissolution'
                WHEN THEMEGENERAL IS NULL THEN 'Non renseigné'
                ELSE 'Autre'
            END AS categorie
        FROM QUESTIONNAIRE
        WHERE epci = 'Grand-Châtellerault'
        ) AS temp
        GROUP BY categorie";
break;
                

    case 'graph17':
        $sql = "SELECT 
        categorie,
        COUNT(*) AS total
    FROM (
        SELECT 
            CASE
                WHEN LOWER(THEMEGENERAL) LIKE '%aide%' THEN 'Aide aux déclarations'
                WHEN LOWER(THEMEGENERAL) LIKE '%statu%' THEN 'Statuts/ag & projet & gouvernance'
                WHEN LOWER(THEMEGENERAL) LIKE '%regl%' THEN 'Réglementation & juridique'
                WHEN LOWER(THEMEGENERAL) LIKE '%even%' THEN 'Evenementiel'
                WHEN LOWER(THEMEGENERAL) LIKE 'mecen%' THEN 'Mecenat & financement'
                WHEN LOWER(THEMEGENERAL) LIKE '%compta%' THEN 'Comptabilité'
                WHEN LOWER(THEMEGENERAL) LIKE '%fiscal%' THEN 'Fiscalité'
                WHEN LOWER(THEMEGENERAL) LIKE '%emploi%' THEN 'Emploi & CCN'
                WHEN LOWER(THEMEGENERAL) LIKE '%formation%' THEN 'Formation'
                WHEN LOWER(THEMEGENERAL) LIKE '%disso%' THEN 'Dissolution'
                WHEN THEMEGENERAL IS NULL THEN 'Non renseigné'
                ELSE 'Autre'
            END AS categorie
        FROM QUESTIONNAIRE
        WHERE epci = 'Vienne-et-Gartempe'
        ) AS temp
        GROUP BY categorie";
break;
               

    case 'graph18':
        $sql = "SELECT 
        categorie,
        COUNT(*) AS total
    FROM (
        SELECT 
            CASE
                WHEN LOWER(THEMEGENERAL) LIKE '%aide%' THEN 'Aide aux déclarations'
                WHEN LOWER(THEMEGENERAL) LIKE '%statu%' THEN 'Statuts/ag & projet & gouvernance'
                WHEN LOWER(THEMEGENERAL) LIKE '%regl%' THEN 'Réglementation & juridique'
                WHEN LOWER(THEMEGENERAL) LIKE '%even%' THEN 'Evenementiel'
                WHEN LOWER(THEMEGENERAL) LIKE 'mecen%' THEN 'Mecenat & financement'
                WHEN LOWER(THEMEGENERAL) LIKE '%compta%' THEN 'Comptabilité'
                WHEN LOWER(THEMEGENERAL) LIKE '%fiscal%' THEN 'Fiscalité'
                WHEN LOWER(THEMEGENERAL) LIKE '%emploi%' THEN 'Emploi & CCN'
                WHEN LOWER(THEMEGENERAL) LIKE '%formation%' THEN 'Formation'
                WHEN LOWER(THEMEGENERAL) LIKE '%disso%' THEN 'Dissolution'
                WHEN THEMEGENERAL IS NULL THEN 'Non renseigné'
                ELSE 'Autre'
            END AS categorie
        FROM QUESTIONNAIRE
        WHERE epci = 'Civraisien-en-Poitou'
        ) AS temp
        GROUP BY categorie";
break;

    case 'graph19':
        $sql = "SELECT 
        categorie,
        COUNT(*) AS total
    FROM (
        SELECT 
            CASE
                WHEN LOWER(THEMEGENERAL) LIKE '%aide%' THEN 'Aide aux déclarations'
                WHEN LOWER(THEMEGENERAL) LIKE '%statu%' THEN 'Statuts/ag & projet & gouvernance'
                WHEN LOWER(THEMEGENERAL) LIKE '%regl%' THEN 'Réglementation & juridique'
                WHEN LOWER(THEMEGENERAL) LIKE '%even%' THEN 'Evenementiel'
                WHEN LOWER(THEMEGENERAL) LIKE 'mecen%' THEN 'Mecenat & financement'
                WHEN LOWER(THEMEGENERAL) LIKE '%compta%' THEN 'Comptabilité'
                WHEN LOWER(THEMEGENERAL) LIKE '%fiscal%' THEN 'Fiscalité'
                WHEN LOWER(THEMEGENERAL) LIKE '%emploi%' THEN 'Emploi & CCN'
                WHEN LOWER(THEMEGENERAL) LIKE '%formation%' THEN 'Formation'
                WHEN LOWER(THEMEGENERAL) LIKE '%disso%' THEN 'Dissolution'
                WHEN THEMEGENERAL IS NULL THEN 'Non renseigné'
                ELSE 'Autre'
            END AS categorie
        FROM QUESTIONNAIRE
        WHERE epci = 'Pays loudunais'
        ) AS temp
        GROUP BY categorie";
break;

    default:
        $sql = "";
        break;
}




// exécution de la requête
$result = $conn->query($sql);


// Vérifier si la requête s'est bien exécutée
if ($result) {
    $data = []; // Tableau pour stocker les données

    // Vérification si la requête a renvoyé des résultats
    if ($result->num_rows > 0) {
        // Récupérer les données dans un tableau
        while ($row = $result->fetch_assoc()) {
            $data[] = $row; // Ajouter chaque ligne de résultats
        }

        // Envoi des données en format JSON
        echo json_encode($data);
    } else {
        echo json_encode([]); // Si aucune donnée n'est trouvée
    }
} else {
    // En cas d'erreur SQL, renvoyer une erreur
    echo json_encode(["error" => "Erreur dans la requête : " . $conn->error]);
}
$conn->close(); // Fermer la connexion
?>
