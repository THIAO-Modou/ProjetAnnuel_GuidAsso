<?php
// Inclure le fichier APIService.php
include 'ApiService.php';

$siren = '775672272'; // Exemple de SIREN (à adapter selon le besoin)
$context = "Test de l'API"; // Contexte de la requête
$object = "Test de l'API"; // Objet de la requête
$recipient = "10000001700010"; // Destinataire

try {
    // Création de l'objet ApiService
    $apiService = new ApiService();

    // Utilisation de la méthode pour récupérer les informations d'entreprise via le SIREN
    $communesData = $apiService->getEntrepriseInfoBySiren($siren, $context, $object, $recipient);

    // Vérifier si des établissements ont été trouvés
    if (isset($communesData['data']['etablissements']) && !empty($communesData['data']['etablissements'])) {
        echo "<h3>Liste des établissements associés au SIREN '$siren' :</h3>";
        echo "<ul>";

        // Parcourir les établissements pour extraire les communes et codes postaux
        foreach ($communesData['data']['etablissements'] as $etablissement) {
            // Vérifier que l'adresse de l'établissement contient les informations nécessaires
            if (isset($etablissement['adresse']['commune']) && isset($etablissement['adresse']['code_postal'])) {
                // Récupérer l'adresse de chaque établissement
                $commune = htmlspecialchars($etablissement['adresse']['commune']);
                $codePostal = htmlspecialchars($etablissement['adresse']['code_postal']);
                
                // Afficher la commune et le code postal
                echo "<li>" . $commune . " - " . $codePostal . "</li>";
            }
        }

        echo "</ul>";
    } else {
        echo "<p>Aucun établissement trouvé pour le SIREN '$siren'.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
}
?>
