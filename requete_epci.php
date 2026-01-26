<?php
include 'connexion.php'; // Connexion à la base de données

$sql = "SELECT epci, COUNT(*) as total FROM ASSOCIATION GROUP BY epci"; //requête pour récupérer le nombre d'association par EPCI

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
