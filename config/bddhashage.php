<?php
include_once __DIR__ . '/config/BD.php';  // Inclure votre fichier de connexion à la base de données

// Exemple de mise à jour des mots de passe dans la base de données en les hachant
$defaultPassword = "GuidAsso86!";

// Hachage du mot de passe
$hashedPassword = password_hash($defaultPassword, PASSWORD_BCRYPT);

// Préparation de la requête SQL pour mettre à jour tous les mots de passe avec le hachage
$sql = "UPDATE GUIDASSO SET motdepasse = :motdepasse";

// Préparer la déclaration PDO
$stmt = $pdo->prepare($sql);

// Lier la valeur du mot de passe haché à la requête SQL
$stmt->bindValue(':motdepasse', $hashedPassword, PDO::PARAM_STR);

// Exécuter la mise à jour
if ($stmt->execute()) {
    echo "Les mots de passe ont été mis à jour avec succès.";
} else {
    echo "Erreur lors de la mise à jour des mots de passe.";
}

?>
