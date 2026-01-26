<?php
// Inclure la classe ApiService
include 'APIService.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Vérifier que le champ SIREN est bien rempli
        if (!isset($_POST['siren']) || empty($_POST['siren'])) {
            throw new Exception("Le champ SIREN est obligatoire.");
        }

        // Récupérer le SIREN soumis par le formulaire
        $siren = $_POST['siren'];

        // Valider le format du SIREN
        if (!preg_match('/^\d{9}$/', $siren)) {
            throw new Exception("Le SIREN '$siren' n'est pas valide. Il doit être composé de 9 chiffres.");
        }

        // Définition des paramètres API (adapter si nécessaire)
        $context = "Test de l'API";
        $object = "Test de l'API";
        $recipient = "10000001700010"; // Mettre l'identifiant correct

        // Créer une instance de la classe ApiService
        $apiService = new ApiService();
        
        // Récupérer les informations de l'entreprise via l'API
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
                } else {
                    // Afficher un message si l'adresse est manquante
                    echo "<li>Adresse non disponible pour cet établissement.</li>";
                }
            }

            echo "</ul>";
        } else {
            echo "<p>Aucun établissement trouvé pour le SIREN '$siren'.</p>";
        }
    } catch (Exception $e) {
        // Afficher l'erreur
        echo "<p><strong>Erreur :</strong> " . $e->getMessage() . "</p>";
    }
}
?>

<!-- Formulaire HTML pour saisir un SIREN -->
<h2>Recherche d'une entreprise par SIREN</h2>
<form method="POST" action="">
    <label for="siren">Numéro SIREN :</label>
    <input type="text" id="siren" name="siren" required placeholder="Entrez un SIREN de 9 chiffres">
    <button type="submit">Obtenir les communes et codes postaux</button>
</form>
