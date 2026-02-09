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

// Retourne la liste unique des activites (code_craig) depuis la table matrix
function getCraigActivities() {
    global $pdo;

    if (!isset($pdo)) {
        throw new Exception("Erreur : connexion a la base de donnees non etablie.");
    }

    $stmt = $pdo->query("SELECT DISTINCT code_craig FROM matrix WHERE code_craig IS NOT NULL AND code_craig <> '' AND LOWER(code_craig) <> 'autre' ORDER BY code_craig");
    $values = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!$values) {
        return [];
    }

    $values = array_map('trim', $values);
    $values = array_filter($values, function ($v) {
        if ($v === '') {
            return false;
        }
        return mb_strtolower($v, 'UTF-8') !== 'autre';
    });

    return array_values(array_unique($values));
}

// Retourne le code_craig a partir d'un social_object (ex: 006020 -> 006000 si besoin)
function getCraigActivityFromSocialObject($socialObject) {
    global $pdo;

    if (!isset($pdo)) {
        throw new Exception("Erreur : connexion a la base de donnees non etablie.");
    }

    $code = preg_replace('/\D+/', '', (string)$socialObject);
    if ($code === '' || $code === '000000') {
        return null;
    }

    $code = str_pad($code, 6, '0', STR_PAD_LEFT);

    $stmt = $pdo->prepare("SELECT code_craig FROM matrix WHERE social_object = :code LIMIT 1");
    $stmt->bindParam(":code", $code, PDO::PARAM_STR);
    $stmt->execute();
    $value = $stmt->fetchColumn();
    if ($value !== false && $value !== null && $value !== '') {
        return trim((string)$value);
    }

    $intCode = (int)$code;
    $baseCode = intdiv($intCode, 1000) * 1000;
    $baseCodeStr = sprintf("%06d", $baseCode);
    if ($baseCodeStr !== $code) {
        $stmt = $pdo->prepare("SELECT code_craig FROM matrix WHERE social_object = :code LIMIT 1");
        $stmt->bindParam(":code", $baseCodeStr, PDO::PARAM_STR);
        $stmt->execute();
        $value = $stmt->fetchColumn();
        if ($value !== false && $value !== null && $value !== '') {
            return trim((string)$value);
        }
    }

    return null;
}

?>
