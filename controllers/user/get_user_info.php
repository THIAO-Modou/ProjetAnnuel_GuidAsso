<?php
include_once __DIR__ . '/../../config/BD.php';

function getUserInfo($email) {
    global $pdo;

    try {
        error_log("📌 Vérification de l'email reçu dans getUserInfo: " . $email);

        $stmt = $pdo->prepare("SELECT NOMPERSONNE, PRENOMPERSONNE, MAIL, IDFONCTION, CLASSIFICATION FROM GUIDASSO WHERE MAIL = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            error_log("⚠️ Aucun utilisateur trouvé pour l'email: " . $email);
        } else {
            error_log("✅ Utilisateur trouvé: " . json_encode($user));
        }

        return $user;
    } catch (PDOException $e) {
        error_log("❌ Erreur SQL: " . $e->getMessage());
        return null;
    }
}


?>
