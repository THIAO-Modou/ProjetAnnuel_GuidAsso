<?php
session_start();
include_once __DIR__ . '/../../config/BD.php';
include_once __DIR__ . '/../user/get_user_info.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);


if (!isset($_SESSION['MAIL'])) {
    http_response_code(401);
    echo json_encode(["error" => "Utilisateur non authentifié."]);
    exit();
}
//  Recupérer les informations de l'utilisateur connecté
$user = getUserInfoByEmail($_SESSION['MAIL']);
$email = htmlspecialchars($_SESSION['MAIL'] ?? '');

// Gestion de la fonction de l'utilisateur
if ($user) {
    $idFonction = $user['IDFONCTION'];
} else {
    echo json_encode(["error" => "Fonction utilisateur introuvable"]);
    exit();
}

try {
    // Gestion de la pagination
     $nbLignes = isset($_GET['nbLignes']) ? intval($_GET['nbLignes']) : 20;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($page - 1) * $nbLignes;

    
    //  Nombre de ligne en fonction des utilisateurs
    if ($idFonction == 1 || $idFonction == 4) {
        $countQuery = "SELECT COUNT(*) FROM QUESTIONNAIRE WHERE MAILGUIDASSO = :email";
        $countStmt = $pdo->prepare($countQuery);
        $countStmt->bindValue(':email', $email, PDO::PARAM_STR);
        $countStmt->execute();
        $totalEntries = $countStmt->fetchColumn();
    } elseif ($idFonction == 2 || $idFonction == 3) {
        $countQuery = "SELECT COUNT(*) FROM QUESTIONNAIRE";
        $totalEntries = $pdo->query($countQuery)->fetchColumn();
    }
    //  Calculer le nombre total de pages
    $totalPages = ceil($totalEntries / $nbLignes);

    // Pour les profils utilisateur et utilisateur_visiteur
    if ($idFonction == 1 || $idFonction ==4) {
        $query = "SELECT     
                        Q.HORODATEUR,
                        Q.NOMASSO, 
                        Q.NOMSTRUCTURE, 
                        Q.NOMEVENEMENT,
                        C.NOMCONTACT,
                        C.CIVILITE,
                        Q.THEMEGENERAL,
                        Q.AUTRETHEMATIQUE,
                        G.NOMPERSONNE AS NOMUTILISATEUR,
                        G.PRENOMPERSONNE AS PRENOMUTILISATEUR
						G.MAIL AS EMAILUtilisateur                    FROM QUESTIONNAIRE Q
                    LEFT JOIN CONTACT C ON Q.IDCONTACT = C.IDCONTACT
                    LEFT JOIN GUIDASSO G ON Q.MAILGUIDASSO = G.MAIL
                    WHERE Q.MAILGUIDASSO = :email
                    ORDER BY Q.HORODATEUR DESC
                    LIMIT :nbLignes OFFSET :offset";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    
    // Pour les profils admin et visiteur_admin
    }elseif($idFonction == 2 || $idFonction ==3) {
        $query = "SELECT     
                        Q.HORODATEUR,
                        Q.NOMASSO, 
                        Q.NOMSTRUCTURE, 
                        Q.NOMEVENEMENT,
                        C.NOMCONTACT,
                        C.CIVILITE,
                        Q.THEMEGENERAL,
                        Q.AUTRETHEMATIQUE,
                        G.NOMPERSONNE AS NOMUTILISATEUR,
                        G.PRENOMPERSONNE AS PRENOMUTILISATEUR
						G.MAIL AS EMAILUtilisateur 
                    FROM QUESTIONNAIRE Q
                    LEFT JOIN CONTACT C ON Q.IDCONTACT = C.IDCONTACT
                    LEFT JOIN GUIDASSO G ON Q.MAILGUIDASSO = G.MAIL
                    ORDER BY Q.HORODATEUR DESC
                    LIMIT :nbLignes OFFSET :offset";
        $stmt = $pdo->prepare($query);
    }
    $stmt->bindParam(":nbLignes", $nbLignes, PDO::PARAM_INT);
    $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();

    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //Envoie les résultats de la requete et les données de la pagination sous forme JSON aux fichier JS
    header('Content-Type: application/json');
    echo json_encode([
        "entries" => $entries,
        "totalPages" => $totalPages,
        "email" => $email
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erreur lors de l'exécution de la requête."]);
    exit();
}

?>
