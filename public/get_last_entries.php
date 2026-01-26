<?php
include_once __DIR__ . '/config/BD.php';

try {

    //date_format = conversion format hordateur 
    $query = "SELECT     
                    Q.HORODATEUR,
                    Q.NOMASSO, 
                    Q.NOMSTRUCTURE, 
                    Q.NOMEVENEMENT,
                    C.NOMCONTACT,
                    C.CIVILITE,
                    Q.THEMEGENERAL,
                    G.NOMPERSONNE AS NOMUTILISATEUR,
                    G.PRENOMPERSONNE AS PRENOMUTILISATEUR
                FROM QUESTIONNAIRE Q
                LEFT JOIN CONTACT C ON Q.IDCONTACT = C.IDCONTACT
                LEFT JOIN GUIDASSO G ON Q.MAILGUIDASSO = G.MAIL
                ORDER BY Q.HORODATEUR DESC 
                LIMIT 15;"; // limiter à 15 entrées et trier par ordre décroissant grâce à l'horodateur
    $stmt = $pdo->query($query);

    $entries = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $entries[] = [
            'HORODATEUR' => htmlspecialchars($row['HORODATEUR'], ENT_QUOTES, 'UTF-8'),
            'NOMASSO' => htmlspecialchars($row['NOMASSO'], ENT_QUOTES, 'UTF-8'),
            'NOMSTRUCTURE' => htmlspecialchars($row['NOMSTRUCTURE'], ENT_QUOTES, 'UTF-8'),
            'NOMEVENEMENT' => htmlspecialchars($row['NOMEVENEMENT'], ENT_QUOTES, 'UTF-8'),
            'NOMCONTACT' => htmlspecialchars($row['NOMCONTACT'], ENT_QUOTES, 'UTF-8'),
            'CIVILITE' => htmlspecialchars($row['CIVILITE'], ENT_QUOTES, 'UTF-8'),
            'THEMEGENERAL' => htmlspecialchars($row['THEMEGENERAL'], ENT_QUOTES, 'UTF-8'),
            'NOMUTILISATEUR' => htmlspecialchars($row['NOMUTILISATEUR'], ENT_QUOTES, 'UTF-8'),
            'PRENOMUTILISATEUR' => htmlspecialchars($row['PRENOMUTILISATEUR'], ENT_QUOTES, 'UTF-8')
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($entries);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur lors de l'exécution de la requête."]);
    exit();
}
?>
