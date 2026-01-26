<?php
//  Définition des paramètres de connexion à la base de données 
$host = 'db5016852135.hosting-data.io';
$dbname = 'dbs13606446';
$username = 'dbu1064354';
$password = 'juvras-caBqys-sinka0';


try {
    // Création de l'objet PDO avec encodage UTF-8
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    //  Active les exceptions pour capturer les erreurs SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
} catch (PDOException $e) {
    // Arrête le script en cas d’erreur de connexion
    die("Impossible de se connecter à la base de données : " . $e->getMessage());
}

// Fonction d’insertion générique
function INSERTSQL($tableName, $values = []) {
    global $pdo;
    try {
        // Création des listes de champs et de placeholders
        $fields = implode(',', array_keys($values));
        $placeholders = implode(',', array_map(fn($val) => ":$val", array_keys($values)));

        // Construction de la requête SQL
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
        //  Formatage des clauses SET et WHERE
        $setFields = array_map(fn($field) => "$field = :$field", array_keys($values));
        $whereFields = array_map(fn($field) => "$field = :$field", array_keys($where));
        $setClause = implode(', ', $setFields);
        $whereClause = implode(' AND ', $whereFields);
        
        // Construction de la requête UPDATE
        $sql = "UPDATE $tableName SET $setClause WHERE $whereClause";
        $rows = $pdo->prepare($sql);

        // Fusionne données à mettre à jour et conditions
        return $rows->execute(array_merge($values, $where));
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function hashUserPasswords() {
    global $pdo;
    try {
        // Sélection des comptes avec mot de passe en clair
        $stmt = $pdo->query("SELECT Mail, motdepasse FROM GUIDASSO");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Vérifie que le mot de passe n’est pas déjà haché
            if (!preg_match("/^\$2y\$/", $row['motdepasse'])) {

                // Hachage en BCRYPT
                $hashedPassword = password_hash($row['motdepasse'], PASSWORD_BCRYPT);

                // Mise à jour dans la base
                UPDATESQL('GUIDASSO', ['motdepasse' => $hashedPassword], ['Mail' => $row['Mail']]);
            }
        }
    } catch (PDOException $e) {
        // Affiche une erreur en cas de problème
        echo "Erreur : " . $e->getMessage();
    }
}


// Fonction qui recupere les information d'un utilisateur en foncition de son email
function getUserInfoByEmail($email) {
    global $pdo; // Utilise la connexion existante

    // Vérifie que la connexion PDO est bien active
    if (!isset($pdo)) {
        throw new Exception("Erreur : connexion à la base de données non établie.");
    }
    // Requête SQL avec jointure pour enrichir les infos
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

    // Retourne un tableau associatif ou null si rien trouvé
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null; // Retourne un tableau ou null si aucun résultat
}

?>
