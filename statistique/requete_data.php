<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start(); 

// pour accéder à tous les fichiers php pour la connexion, session et inscription
include_once __DIR__ . '/../config/BD.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Impossible de se connecter à la base de données : " . $e->getMessage());
}

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
                FROM QUESTIONNAIRE 
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
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%populaire%' THEN 'Education populaire, Jeunesse'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sport%' THEN 'Sport, activités indoor et plein-air'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%lien%' THEN 'Lien social, éducation, insertion, logement'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%culture%' THEN 'Culture, loisirs'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%caritatif%' THEN 'Caritatif et solidarité'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%environnement%' THEN 'Environnement, écologie et dévelop. durable'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%science%' THEN 'Science, recherche, technologies'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sécurité%' THEN 'Sécurité, secours, défense'
                                -- WHEN ACTIVITEPRINCIPALEASSO IS NULL OR ACTIVITEPRINCIPALEASSO = '' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%...%' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%indéfini%' THEN 'N/A'
                                ELSE 'Autre, N/A'
                            END AS categorie
                        FROM QUESTIONNAIRE
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
                                WHEN LOWER(THEMEGENERAL) LIKE '%aide%' OR '%démarche%' OR '%declaration%' OR '%compte asso%' OR '%depot%' OR 'siret' OR 'siren' THEN 'Aide aux déclarations'
                                WHEN LOWER(THEMEGENERAL) LIKE '%statu%' OR '%gouvernance%' OR '%creation%' THEN 'Statuts/AG & projet & gouvernance'
                                WHEN LOWER(THEMEGENERAL) LIKE '%ngagement%' OR '%bénévol%' OR '%volontaire%' OR 'cvn' THEN 'Engagement bénévole'
                                WHEN LOWER(THEMEGENERAL) LIKE '%diation%' OR '%crise%' THEN 'Médiation/Crise'
                                WHEN LOWER(THEMEGENERAL) LIKE '%réglementa%' OR '%juri%' THEN 'Réglementation & juridique'
                                WHEN LOWER(THEMEGENERAL) LIKE '%evenem%' THEN 'Evenementiel'
                                WHEN LOWER(THEMEGENERAL) LIKE 'mecen%' OR '%finance%' OR '%subvention%' OR 'dons' OR '%reconnaissance%' THEN 'Mecenat & financement'
                                WHEN LOWER(THEMEGENERAL) LIKE '%compta%' THEN 'Comptabilité'
                                WHEN LOWER(THEMEGENERAL) LIKE '%fiscal%' OR 'dgfip' OR 'tva' OR 'impot%' THEN 'Fiscalité'
                                WHEN LOWER(THEMEGENERAL) LIKE '%emploi%' OR '%salarie%' THEN 'Emploi & CCN'
                                WHEN LOWER(THEMEGENERAL) LIKE '%formation%' THEN 'Formation'
                                WHEN LOWER(THEMEGENERAL) LIKE '%disso%' OR '%sommeil%' THEN 'Dissolution'
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
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%populaire%' THEN 'Education populaire, Jeunesse'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sport%' THEN 'Sport, activités indoor et plein-air'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%lien%' THEN 'Lien social, éducation, insertion, logement'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%culture%' THEN 'Culture, loisirs'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%caritatif%' THEN 'Caritatif et solidarité'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%environnement%' THEN 'Environnement, écologie et dévelop. durable'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%science%' THEN 'Science, recherche, technologies'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sécurité%' THEN 'Sécurité, secours, défense'
                            -- WHEN ACTIVITEPRINCIPALEASSO IS NULL OR ACTIVITEPRINCIPALEASSO = '' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%...%' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%indéfini%' THEN 'Non renseigné'
                            ELSE 'Autre, N/A'
                        END AS categorie
                    FROM QUESTIONNAIRE
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
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%populaire%' THEN 'Education populaire, Jeunesse'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sport%' THEN 'Sport, activités indoor et plein-air'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%lien%' THEN 'Lien social, éducation, insertion, logement'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%culture%' THEN 'Culture, loisirs'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%caritatif%' THEN 'Caritatif et solidarité'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%environnement%' THEN 'Environnement, écologie et dévelop. durable'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%science%' THEN 'Science, recherche, technologies'
                                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sécurité%' THEN 'Sécurité, secours, défense'
                                -- WHEN ACTIVITEPRINCIPALEASSO IS NULL OR ACTIVITEPRINCIPALEASSO = '' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%...%' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%indéfini%' THEN 'Non renseigné'
                                ELSE 'Autre, N/A'
                            END AS categorie
                        FROM QUESTIONNAIRE
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
                        WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%populaire%' THEN 'Education populaire, Jeunesse'
                        WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sport%' THEN 'Sport, activités indoor et plein-air'
                        WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%lien%' THEN 'Lien social, éducation, insertion, logement'
                        WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%culture%' THEN 'Culture, loisirs'
                        WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%caritatif%' THEN 'Caritatif et solidarité'
                        WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%environnement%' THEN 'Environnement, écologie et dévelop. durable'
                        WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%science%' THEN 'Science, recherche, technologies'
                        WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sécurité%' THEN 'Sécurité, secours, défense'
                        -- WHEN ACTIVITEPRINCIPALEASSO IS NULL OR ACTIVITEPRINCIPALEASSO = '' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%...%' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%indéfini%' THEN 'Non renseigné'
                        ELSE 'Autre, N/A'
                    END AS categorie
                FROM QUESTIONNAIRE
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
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%populaire%' THEN 'Education populaire, Jeunesse'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sport%' THEN 'Sport, activités indoor et plein-air'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%lien%' THEN 'Lien social, éducation, insertion, logement'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%culture%' THEN 'Culture, loisirs'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%caritatif%' THEN 'Caritatif et solidarité'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%environnement%' THEN 'Environnement, écologie et dévelop. durable'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%science%' THEN 'Science, recherche, technologies'
                            WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sécurité%' THEN 'Sécurité, secours, défense'
                            -- WHEN ACTIVITEPRINCIPALEASSO IS NULL OR ACTIVITEPRINCIPALEASSO = '' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%...%' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%indéfini%' THEN 'Non renseigné'
                            ELSE 'Autre, N/A'
                        END AS categorie
                    FROM QUESTIONNAIRE
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
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%populaire%' THEN 'Education populaire, Jeunesse'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sport%' THEN 'Sport, activités indoor et plein-air'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%lien%' THEN 'Lien social, éducation, insertion, logement'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%culture%' THEN 'Culture, loisirs'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%caritatif%' THEN 'Caritatif et solidarité'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%environnement%' THEN 'Environnement, écologie et dévelop. durable'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%science%' THEN 'Science, recherche, technologies'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sécurité%' THEN 'Sécurité, secours, défense'
                -- WHEN ACTIVITEPRINCIPALEASSO IS NULL OR ACTIVITEPRINCIPALEASSO = '' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%...%' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%indéfini%' THEN 'Non renseigné'
                ELSE 'Autre, N/A'
            END AS categorie
        FROM QUESTIONNAIRE
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
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%populaire%' THEN 'Education populaire, Jeunesse'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sport%' THEN 'Sport, activités indoor et plein-air'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%lien%' THEN 'Lien social, éducation, insertion, logement'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%culture%' THEN 'Culture, loisirs'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%caritatif%' THEN 'Caritatif et solidarité'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%environnement%' THEN 'Environnement, écologie et dévelop. durable'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%science%' THEN 'Science, recherche, technologies'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sécurité%' THEN 'Sécurité, secours, défense'
                -- WHEN ACTIVITEPRINCIPALEASSO IS NULL OR ACTIVITEPRINCIPALEASSO = '' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%...%' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%indéfini%' THEN 'Non renseigné'
                ELSE 'Autre, N/A'
            END AS categorie
        FROM QUESTIONNAIRE
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
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%populaire%' THEN 'Education populaire, Jeunesse'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sport%' THEN 'Sport, activités indoor et plein-air'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%lien%' THEN 'Lien social, éducation, insertion, logement'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%culture%' THEN 'Culture, loisirs'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%caritatif%' THEN 'Caritatif et solidarité'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%environnement%' THEN 'Environnement, écologie et dévelop. durable'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%science%' THEN 'Science, recherche, technologies'
                WHEN LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%sécurité%' THEN 'Sécurité, secours, défense'
                -- WHEN ACTIVITEPRINCIPALEASSO IS NULL OR ACTIVITEPRINCIPALEASSO = '' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%...%' OR LOWER(ACTIVITEPRINCIPALEASSO) LIKE '%indéfini%' THEN 'Non renseigné'
                ELSE 'Autre, N/A'
            END AS categorie
        FROM QUESTIONNAIRE
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
                                WHEN LOWER(THEMEGENERAL) LIKE '%aide%' OR '%démarche%' OR '%declaration%' OR '%compte asso%' OR '%depot%' OR 'siret' OR 'siren' THEN 'Aide aux déclarations'
                                WHEN LOWER(THEMEGENERAL) LIKE '%statu%' OR '%gouvernance%' OR '%creation%' THEN 'Statuts/AG & projet & gouvernance'
                                WHEN LOWER(THEMEGENERAL) LIKE '%ngagement%' OR '%bénévol%' OR '%volontaire%' OR 'cvn' THEN 'Engagement bénévole'
                                WHEN LOWER(THEMEGENERAL) LIKE '%diation%' OR '%crise%' THEN 'Médiation/Crise'
                                WHEN LOWER(THEMEGENERAL) LIKE '%réglementa%' OR '%juri%' THEN 'Réglementation & juridique'
                                WHEN LOWER(THEMEGENERAL) LIKE '%evenem%' THEN 'Evenementiel'
                                WHEN LOWER(THEMEGENERAL) LIKE 'mecen%' OR '%finance%' OR '%subvention%' OR 'dons' OR '%reconnaissance%' THEN 'Mecenat & financement'
                                WHEN LOWER(THEMEGENERAL) LIKE '%compta%' THEN 'Comptabilité'
                                WHEN LOWER(THEMEGENERAL) LIKE '%fiscal%' OR 'dgfip' OR 'tva' OR 'impot%' THEN 'Fiscalité'
                                WHEN LOWER(THEMEGENERAL) LIKE '%emploi%' OR '%salarie%' THEN 'Emploi & CCN'
                                WHEN LOWER(THEMEGENERAL) LIKE '%formation%' THEN 'Formation'
                                WHEN LOWER(THEMEGENERAL) LIKE '%disso%' OR '%sommeil%' THEN 'Dissolution'
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
                                WHEN LOWER(THEMEGENERAL) LIKE '%aide%' OR '%démarche%' OR '%declaration%' OR '%compte asso%' OR '%depot%' OR 'siret' OR 'siren' THEN 'Aide aux déclarations'
                                WHEN LOWER(THEMEGENERAL) LIKE '%statu%' OR '%gouvernance%' OR '%creation%' THEN 'Statuts/AG & projet & gouvernance'
                                WHEN LOWER(THEMEGENERAL) LIKE '%ngagement%' OR '%bénévol%' OR '%volontaire%' OR 'cvn' THEN 'Engagement bénévole'
                                WHEN LOWER(THEMEGENERAL) LIKE '%diation%' OR '%crise%' THEN 'Médiation/Crise'
                                WHEN LOWER(THEMEGENERAL) LIKE '%réglementa%' OR '%juri%' THEN 'Réglementation & juridique'
                                WHEN LOWER(THEMEGENERAL) LIKE '%evenem%' THEN 'Evenementiel'
                                WHEN LOWER(THEMEGENERAL) LIKE 'mecen%' OR '%finance%' OR '%subvention%' OR 'dons' OR '%reconnaissance%' THEN 'Mecenat & financement'
                                WHEN LOWER(THEMEGENERAL) LIKE '%compta%' THEN 'Comptabilité'
                                WHEN LOWER(THEMEGENERAL) LIKE '%fiscal%' OR 'dgfip' OR 'tva' OR 'impot%' THEN 'Fiscalité'
                                WHEN LOWER(THEMEGENERAL) LIKE '%emploi%' OR '%salarie%' THEN 'Emploi & CCN'
                                WHEN LOWER(THEMEGENERAL) LIKE '%formation%' THEN 'Formation'
                                WHEN LOWER(THEMEGENERAL) LIKE '%disso%' OR '%sommeil%' THEN 'Dissolution'
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
                WHEN LOWER(THEMEGENERAL) LIKE '%aide%' OR '%démarche%' OR '%declaration%' OR '%compte asso%' OR '%depot%' OR 'siret' OR 'siren' THEN 'Aide aux déclarations'
                WHEN LOWER(THEMEGENERAL) LIKE '%statu%' OR '%gouvernance%' OR '%creation%' THEN 'Statuts/AG & projet & gouvernance'
                WHEN LOWER(THEMEGENERAL) LIKE '%ngagement%' OR '%bénévol%' OR '%volontaire%' OR 'cvn' THEN 'Engagement bénévole'
                WHEN LOWER(THEMEGENERAL) LIKE '%diation%' OR '%crise%' THEN 'Médiation/Crise'
                WHEN LOWER(THEMEGENERAL) LIKE '%réglementa%' OR '%juri%' THEN 'Réglementation & juridique'
                WHEN LOWER(THEMEGENERAL) LIKE '%evenem%' THEN 'Evenementiel'
                WHEN LOWER(THEMEGENERAL) LIKE 'mecen%' OR '%finance%' OR '%subvention%' OR 'dons' OR '%reconnaissance%' THEN 'Mecenat & financement'
                WHEN LOWER(THEMEGENERAL) LIKE '%compta%' THEN 'Comptabilité'
                WHEN LOWER(THEMEGENERAL) LIKE '%fiscal%' OR 'dgfip' OR 'tva' OR 'impot%' THEN 'Fiscalité'
                WHEN LOWER(THEMEGENERAL) LIKE '%emploi%' OR '%salarie%' THEN 'Emploi & CCN'
                WHEN LOWER(THEMEGENERAL) LIKE '%formation%' THEN 'Formation'
                WHEN LOWER(THEMEGENERAL) LIKE '%disso%' OR '%sommeil%' THEN 'Dissolution'
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
                WHEN LOWER(THEMEGENERAL) LIKE '%aide%' OR '%démarche%' OR '%declaration%' OR '%compte asso%' OR '%depot%' OR 'siret' OR 'siren' THEN 'Aide aux déclarations'
                WHEN LOWER(THEMEGENERAL) LIKE '%statu%' OR '%gouvernance%' OR '%creation%' THEN 'Statuts/AG & projet & gouvernance'
                WHEN LOWER(THEMEGENERAL) LIKE '%ngagement%' OR '%bénévol%' OR '%volontaire%' OR 'cvn' THEN 'Engagement bénévole'
                WHEN LOWER(THEMEGENERAL) LIKE '%diation%' OR '%crise%' THEN 'Médiation/Crise'
                WHEN LOWER(THEMEGENERAL) LIKE '%réglementa%' OR '%juri%' THEN 'Réglementation & juridique'
                WHEN LOWER(THEMEGENERAL) LIKE '%evenem%' THEN 'Evenementiel'
                WHEN LOWER(THEMEGENERAL) LIKE 'mecen%' OR '%finance%' OR '%subvention%' OR 'dons' OR '%reconnaissance%' THEN 'Mecenat & financement'
                WHEN LOWER(THEMEGENERAL) LIKE '%compta%' THEN 'Comptabilité'
                WHEN LOWER(THEMEGENERAL) LIKE '%fiscal%' OR 'dgfip' OR 'tva' OR 'impot%' THEN 'Fiscalité'
                WHEN LOWER(THEMEGENERAL) LIKE '%emploi%' OR '%salarie%' THEN 'Emploi & CCN'
                WHEN LOWER(THEMEGENERAL) LIKE '%formation%' THEN 'Formation'
                WHEN LOWER(THEMEGENERAL) LIKE '%disso%' OR '%sommeil%' THEN 'Dissolution'
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
                WHEN LOWER(THEMEGENERAL) LIKE '%aide%' OR '%démarche%' OR '%declaration%' OR '%compte asso%' OR '%depot%' OR 'siret' OR 'siren' THEN 'Aide aux déclarations'
                WHEN LOWER(THEMEGENERAL) LIKE '%statu%' OR '%gouvernance%' OR '%creation%' THEN 'Statuts/AG & projet & gouvernance'
                WHEN LOWER(THEMEGENERAL) LIKE '%ngagement%' OR '%bénévol%' OR '%volontaire%' OR 'cvn' THEN 'Engagement bénévole'
                WHEN LOWER(THEMEGENERAL) LIKE '%diation%' OR '%crise%' THEN 'Médiation/Crise'
                WHEN LOWER(THEMEGENERAL) LIKE '%réglementa%' OR '%juri%' THEN 'Réglementation & juridique'
                WHEN LOWER(THEMEGENERAL) LIKE '%evenem%' THEN 'Evenementiel'
                WHEN LOWER(THEMEGENERAL) LIKE 'mecen%' OR '%finance%' OR '%subvention%' OR 'dons' OR '%reconnaissance%' THEN 'Mecenat & financement'
                WHEN LOWER(THEMEGENERAL) LIKE '%compta%' THEN 'Comptabilité'
                WHEN LOWER(THEMEGENERAL) LIKE '%fiscal%' OR 'dgfip' OR 'tva' OR 'impot%' THEN 'Fiscalité'
                WHEN LOWER(THEMEGENERAL) LIKE '%emploi%' OR '%salarie%' THEN 'Emploi & CCN'
                WHEN LOWER(THEMEGENERAL) LIKE '%formation%' THEN 'Formation'
                WHEN LOWER(THEMEGENERAL) LIKE '%disso%' OR '%sommeil%' THEN 'Dissolution'
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
                WHEN LOWER(THEMEGENERAL) LIKE '%aide%' OR '%démarche%' OR '%declaration%' OR '%compte asso%' OR '%depot%' OR 'siret' OR 'siren' THEN 'Aide aux déclarations'
                WHEN LOWER(THEMEGENERAL) LIKE '%statu%' OR '%gouvernance%' OR '%creation%' THEN 'Statuts/AG & projet & gouvernance'
                WHEN LOWER(THEMEGENERAL) LIKE '%ngagement%' OR '%bénévol%' OR '%volontaire%' OR 'cvn' THEN 'Engagement bénévole'
                WHEN LOWER(THEMEGENERAL) LIKE '%diation%' OR '%crise%' THEN 'Médiation/Crise'
                WHEN LOWER(THEMEGENERAL) LIKE '%réglementa%' OR '%juri%' THEN 'Réglementation & juridique'
                WHEN LOWER(THEMEGENERAL) LIKE '%evenem%' THEN 'Evenementiel'
                WHEN LOWER(THEMEGENERAL) LIKE 'mecen%' OR '%finance%' OR '%subvention%' OR 'dons' OR '%reconnaissance%' THEN 'Mecenat & financement'
                WHEN LOWER(THEMEGENERAL) LIKE '%compta%' THEN 'Comptabilité'
                WHEN LOWER(THEMEGENERAL) LIKE '%fiscal%' OR 'dgfip' OR 'tva' OR 'impot%' THEN 'Fiscalité'
                WHEN LOWER(THEMEGENERAL) LIKE '%emploi%' OR '%salarie%' THEN 'Emploi & CCN'
                WHEN LOWER(THEMEGENERAL) LIKE '%formation%' THEN 'Formation'
                WHEN LOWER(THEMEGENERAL) LIKE '%disso%' OR '%sommeil%' THEN 'Dissolution'
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
                WHEN LOWER(THEMEGENERAL) LIKE '%aide%' OR '%démarche%' OR '%declaration%' OR '%compte asso%' OR '%depot%' OR 'siret' OR 'siren' THEN 'Aide aux déclarations'
                WHEN LOWER(THEMEGENERAL) LIKE '%statu%' OR '%gouvernance%' OR '%creation%' THEN 'Statuts/AG & projet & gouvernance'
                WHEN LOWER(THEMEGENERAL) LIKE '%ngagement%' OR '%bénévol%' OR '%volontaire%' OR 'cvn' THEN 'Engagement bénévole'
                WHEN LOWER(THEMEGENERAL) LIKE '%diation%' OR '%crise%' THEN 'Médiation/Crise'
                WHEN LOWER(THEMEGENERAL) LIKE '%réglementa%' OR '%juri%' THEN 'Réglementation & juridique'
                WHEN LOWER(THEMEGENERAL) LIKE '%evenem%' THEN 'Evenementiel'
                WHEN LOWER(THEMEGENERAL) LIKE 'mecen%' OR '%finance%' OR '%subvention%' OR 'dons' OR '%reconnaissance%' THEN 'Mecenat & financement'
                WHEN LOWER(THEMEGENERAL) LIKE '%compta%' THEN 'Comptabilité'
                WHEN LOWER(THEMEGENERAL) LIKE '%fiscal%' OR 'dgfip' OR 'tva' OR 'impot%' THEN 'Fiscalité'
                WHEN LOWER(THEMEGENERAL) LIKE '%emploi%' OR '%salarie%' THEN 'Emploi & CCN'
                WHEN LOWER(THEMEGENERAL) LIKE '%formation%' THEN 'Formation'
                WHEN LOWER(THEMEGENERAL) LIKE '%disso%' OR '%sommeil%' THEN 'Dissolution'
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
if (!isset($pdo)) {
    echo json_encode(["error" => "Connexion à la base de données échouée"]);
    exit();
}


$result = $pdo->query($sql);
// Vérifier si la requête s'est bien exécutée
if ($result) {
    $data = $result->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
} else {
    echo json_encode(["error" => "Erreur dans la requête : " . $pdo->errorInfo()[2]]);
}
$pdo = null; // ferme proprement la connexion PDO
?>
