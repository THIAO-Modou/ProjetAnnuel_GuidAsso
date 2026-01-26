<?php
$host = 'db5016852135.hosting-data.io';
$dbname = 'dbs13606446';
$username = 'dbu1064354';
$password = 'juvras-caBqys-sinka0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // // 🔹 Requête de suppression des entrées avec HORODATEUR = '0000-00-00 00:00:00'
    // $query = "DELETE FROM QUESTIONNAIRE WHERE HORODATEUR = '0000-00-00 00:00:00'";
    // $stmt = $pdo->prepare($query);

    // // 🔹 Exécuter la suppression
    // $stmt->execute();
} catch (PDOException $e) {
    die("Impossible de se connecter à la base de données : " . $e->getMessage());
}

function INSERTSQL($tableName, $values = []) {
    global $pdo;
    try {
        $fields = implode(',', array_keys($values));
        $placeholders = implode(',', array_map(fn($val) => ":$val", array_keys($values)));
        $sql = "INSERT INTO $tableName ($fields) VALUES ($placeholders)";
        $rows = $pdo->prepare($sql);
        return $rows->execute($values);
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function UPDATESQL($tableName, $values = [], $where = []) {
    global $pdo;
    try {
        $setFields = array_map(fn($field) => "$field = :$field", array_keys($values));
        $whereFields = array_map(fn($field) => "$field = :$field", array_keys($where));
        $setClause = implode(', ', $setFields);
        $whereClause = implode(' AND ', $whereFields);
        $sql = "UPDATE $tableName SET $setClause WHERE $whereClause";
        $rows = $pdo->prepare($sql);
        return $rows->execute(array_merge($values, $where));
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function hashUserPasswords() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT Mail, motdepasse FROM GUIDASSO");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!preg_match("/^\$2y\$/", $row['motdepasse'])) {
                $hashedPassword = password_hash($row['motdepasse'], PASSWORD_BCRYPT);
                UPDATESQL('GUIDASSO', ['motdepasse' => $hashedPassword], ['Mail' => $row['Mail']]);
            }
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
