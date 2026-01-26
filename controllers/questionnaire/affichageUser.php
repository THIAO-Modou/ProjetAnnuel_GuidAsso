<?php
// Récupérer les données de la base de données
$sql = "SELECT * FROM GUIDASSO"; // Remplacez "utilisateurs" par le nom de votre table
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Afficher chaque ligne de la table
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
        echo "<td>" . htmlspecialchars($row['prenom']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
        echo "<td>" . htmlspecialchars($row['classification']) . "</td>";
        // Ajouter un lien pour modifier l'utilisateur
        echo "<td><a href='edit.php?id=" . $row['id'] . "'>Modifier</a></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>Aucun utilisateur trouvé.</td></tr>";
}
?>