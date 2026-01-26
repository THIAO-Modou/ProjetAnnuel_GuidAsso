<?php

class ApiService {
    private $jwt_token;

    public function __construct() {
        $config = require 'token.php';
        $this->jwt_token = $config['jwt_token'];
    }

    /**
     * Récupère les informations des établissements d'une entreprise via son SIREN
     *
     * @param string $siren Le numéro SIREN de l'entreprise
     * @param string $context Le contexte de l'API
     * @param string $object L'objet de la demande
     * @param string $recipient Le destinataire
     * @return array Les informations des établissements
     * @throws Exception En cas d'erreur API
     */
    public function getEntrepriseInfoBySiren($siren, $context, $object, $recipient) {
        $api_url = "https://entreprise.api.gouv.fr/v4/djepva/api-association/associations/{$siren}"
                 . "?context=" . urlencode($context)
                 . "&object=" . urlencode($object)
                 . "&recipient=" . urlencode($recipient);

        // Initialisation de cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->jwt_token}",
            "Accept: application/json"
        ]);

        // Exécution de la requête
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Gestion des erreurs
        if ($response === false) {
            throw new Exception('Erreur de connexion API');
        }
        if ($http_code != 200) {
            throw new Exception("Erreur API : Code HTTP $http_code");
        }

        // Décodage JSON
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Erreur de décodage JSON');
        }

        return $data;
    }
}
?>
