<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des pièces jointes</title>
</head>
<body>
    <h1>Liste des pièces jointes associées à des événements</h1>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
            <th>Horodateur</th>    
            <th>Nom du fichier</th>
                <th>Aperçu</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Connexion à la base de données
            include_once __DIR__ . '/config/BD.php';

            try {
                // Récupérer les fichiers ayant une pièce jointe (FILE non NULL)
                $stmt = $pdo->prepare("SELECT IDQUESTIONNAIRE, FILE, HORODATEUR FROM QUESTIONNAIRE WHERE FILE IS NOT NULL");
                $stmt->execute();
                $files = $stmt->fetchAll();

                // Vérifier si des fichiers existent
                if ($files) {
                    foreach ($files as $file) {
                        $filePath = htmlspecialchars($file['FILE']);
                        $fileName = basename($filePath);
                        $horodateur = htmlspecialchars($file['HORODATEUR']);

                        // Vérifiez que le fichier existe réellement sur le serveur
                        if (file_exists($filePath)) {
                            echo "<tr>
                                <td>$horodateur</td>
                                <td>$fileName</td>
                                <td><a href='$filePath' target='_blank'>Afficher</a></td>
                            </tr>";
                        }
                    }
                } else {
                    echo "<tr><td colspan='3'>Aucun fichier enregistré.</td></tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='3'>Erreur : " . $e->getMessage() . "</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
