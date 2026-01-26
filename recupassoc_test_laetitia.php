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
        $context = "Consultation d'association";
        $object = "Récupération des informations de l'association";
        $recipient = "10000001700010"; // À adapter selon l'usage

        // Créer une instance de la classe ApiService
        $apiService = new ApiService();
        
        // Récupérer les informations de l'association via l'API
        $associationData = $apiService->getEntrepriseInfoBySiren($siren, $context, $object, $recipient);

        // Vérifier si des informations ont été trouvées
        if (isset($associationData['data']) && !empty($associationData['data'])) {
            echo "<h3>Informations sur l'association associée au SIREN '$siren' :</h3>";
            echo "<ul>";

            // Vérifier et afficher les données importantes
            $nom = isset($associationData['data']['titre']) ? htmlspecialchars($associationData['data']['titre']) : "Non renseigné";
            $dateCreation = isset($associationData['data']['date_creation']) ? htmlspecialchars($associationData['data']['date_creation']) : "Non renseignée";
            $adresse = isset($associationData['data']['adresse_siege']) ? htmlspecialchars($associationData['data']['adresse_siege']) : "Non renseignée";

            echo "<li><strong>Nom :</strong> $nom</li>";
            echo "<li><strong>Date de création :</strong> $dateCreation</li>";
            echo "<li><strong>Adresse du siège :</strong> $adresse</li>";

            echo "</ul>";
        } else {
            echo "<p>Aucune information trouvée pour le SIREN '$siren'.</p>";
        }
    } catch (Exception $e) {
        // Afficher l'erreur
        echo "<p><strong>Erreur :</strong> " . $e->getMessage() . "</p>";
    }
}
?>

<!-- Formulaire HTML pour saisir un SIREN -->
<h2>Recherche d'une association par SIREN</h2>
<form method="POST" action="">
    <label for="siren">Numéro SIREN :</label>
    <input type="text" id="siren" name="siren" required placeholder="Entrez un SIREN de 9 chiffres">
    <button type="submit">Rechercher</button>
</form>
