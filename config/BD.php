<?php
$host = 'db5016852135.hosting-data.io';
$dbname = 'dbs13606446';
$username = 'dbu1064354';
$password = 'juvras-caBqys-sinka0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
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


// Fonction qui recupere les information d'un utilisateur en foncition de son email
function getUserInfoByEmail($email) {
    global $pdo; // Utilise la connexion existante

    if (!isset($pdo)) {
        throw new Exception("Erreur : connexion à la base de données non établie.");
    }

    $query = "SELECT 
    G.NOMPERSONNE,
    G.PRENOMPERSONNE,
    G.IDFONCTION,
    G.CLASSIFICATION,
    F.NAMEFONCTION
    FROM GUIDASSO G
    LEFT JOIN FONCTION F ON G.IDFONCTION = F.IDFONCTION
    WHERE MAIL = :email";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null; // 🔹 Retourne un tableau ou null si aucun résultat
}

// Recupere le numero de departement par defaut (unique dans GUIDASSO)
function getNumeroDepartement() {
    global $pdo;

    if (!isset($pdo)) {
        throw new Exception("Erreur : connexion a la base de donnees non etablie.");
    }

    $stmt = $pdo->query("SELECT NumeroDepartement FROM GUIDASSO LIMIT 1");
    $value = $stmt->fetchColumn();

    return $value !== false ? $value : null;
}

?>
