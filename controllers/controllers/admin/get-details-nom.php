<?php
include_once __DIR__ . '/../../config/BD.php';

// Vérifier si l'e-mail est reçu
if (isset($_POST['nom2'])) {
    $nom = trim($_POST['nom2']);

    // Préparer une requête SQL pour récupérer les données
    $stmt = $pdo->prepare("SELECT NOMPERSONNE, PRENOMPERSONNE, MAIL, STRUCTURE, CLASSIFICATION FROM GUIDASSO WHERE NOMPERSONNE = ?");
    $stmt->execute([$nom]);

    // Vérifier si une ligne correspond
    if ($stmt->rowCount() > 0) {
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($data); // Envoyer les données sous forme de JSON
    } else {
        echo json_encode(["error" => "Aucune donnée trouvée pour ce nom."]);
    }
}
?>
