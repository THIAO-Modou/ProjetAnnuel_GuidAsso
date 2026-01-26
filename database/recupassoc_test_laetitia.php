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

        // Créer une instance de la classe ApiService
        $apiService = new ApiService();
        
        // Récupérer les informations de l'entreprise via l'API
        $communesData = $apiService->getEntrepriseInfoBySiren($siren);

        // Vérifier si des communes ont été trouvées
        if (!empty($communesData)) {
            echo "<p>Voici les communes et codes postaux associés au SIREN '$siren' :</p>";
            echo "<ul>";
            foreach ($communesData as $commune) {
                echo "<li>" . htmlspecialchars($commune['commune']) . " - " . htmlspecialchars($commune['code_postal']) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Aucune association ou commune trouvée pour le SIREN '$siren'.</p>";
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
    <button type="submit">Rechercher</button>
</form>
