<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include_once __DIR__ . '/../../config/BD.php';
include_once __DIR__ . '/../../config/session.php';

$mom = $_SESSION['MAIL'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Initialisation des variables d'erreur et de succès
        $_SESSION['error_message_modification'] = "";
        $_SESSION['success_message'] = "";
        $_SESSION['form_data'] = $_POST; // Conserver les valeurs en cas d'erreur

        if (!isset($_POST['form_type'])) {
            throw new Exception("Type de formulaire non spécifié.");
        }

        // Vérification des champs obligatoires
        $missingFields = [];

        if ($_POST['form_type'] === "update_user_email") {
            // Récupération des données du formulaire (modification par e-mail)
            $email = filter_var($_POST['email1'] ?? '', FILTER_SANITIZE_EMAIL); // Email en lecture seule
            $nom = trim($_POST['nom1'] ?? '');
            $prenom = trim($_POST['prenom1'] ?? '');
            $classification = trim($_POST['classification'] ?? '');
            $structure = trim($_POST['structure1'] ?? '');
            $role = isset($_POST['role']) ? intval($_POST['role']) : 0; // Nouveau champ pour le rôle

            if (empty($email)) $missingFields[] = "Adresse email";
            if (empty($nom)) $missingFields[] = "Nom";
            if (empty($prenom)) $missingFields[] = "Prénom";
            if (empty($classification)) $missingFields[] = "Classification";
            if (empty($role)) $missingFields[] = "Rôle";

            if (!empty($missingFields)) {
                throw new Exception("Les champs suivants sont obligatoires et manquent : " . implode(", ", $missingFields) . ".");
            }

            // Vérifier si l'email existe dans la base
            $stmt = $pdo->prepare("SELECT * FROM GUIDASSO WHERE MAIL = :mail");
            $stmt->bindParam(':mail', $email);
            $stmt->execute();
            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$existingUser) {
                throw new Exception("Adresse email non reconnue. Veuillez vérifier l'email saisi.");
            }

            if ($structure === '') {
                $structure = $existingUser['STRUCTURE'] ?? null;
            }

            // Mise à jour du profil incluant le rôle (IDFONCTION)
            $stmt = $pdo->prepare("UPDATE GUIDASSO SET NOMPERSONNE = :nom, PRENOMPERSONNE = :prenom, STRUCTURE = :structure, CLASSIFICATION = :classification, IDFONCTION = :role WHERE MAIL = :mail");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':structure', $structure);
            $stmt->bindParam(':classification', $classification);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':mail', $email);
            $stmt->execute();

            $_SESSION['success_message'] = "Le profil a été mis à jour avec succès.";
            unset($_SESSION['form_data']); // Supprimer les anciennes valeurs en cas de succès

        } elseif ($_POST['form_type'] === "update_user_name") {
            // Récupération des données du formulaire (modification par nom)
            $nom = trim($_POST['nom2'] ?? '');
            $prenom = trim($_POST['prenom2'] ?? '');
            $email = filter_var($_POST['email2'] ?? '', FILTER_SANITIZE_EMAIL); // Email en lecture seule
            $classification = trim($_POST['classification'] ?? '');
            $structure = trim($_POST['structure2'] ?? '');
            $role = isset($_POST['role']) ? intval($_POST['role']) : 0; // Nouveau champ pour le rôle

            if (empty($email)) $missingFields[] = "Adresse email";
            if (empty($nom)) $missingFields[] = "Nom";
            if (empty($prenom)) $missingFields[] = "Prénom";
            if (empty($classification)) $missingFields[] = "Classification";
            if (empty($role)) $missingFields[] = "Rôle";

            if (!empty($missingFields)) {
                throw new Exception("Les champs suivants sont obligatoires et manquent : " . implode(", ", $missingFields) . ".");
            }

            // Vérifier si l'email existe dans la base
            $stmt = $pdo->prepare("SELECT * FROM GUIDASSO WHERE MAIL = :mail");
            $stmt->bindParam(':mail', $email);
            $stmt->execute();
            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$existingUser) {
                throw new Exception("L'utilisateur avec cet email n'existe pas. Vérifiez vos informations.");
            }

            if ($structure === '') {
                $structure = $existingUser['STRUCTURE'] ?? null;
            }

            // Mise à jour du profil incluant le rôle (IDFONCTION)
            $stmt = $pdo->prepare("UPDATE GUIDASSO SET NOMPERSONNE = :nom, PRENOMPERSONNE = :prenom, STRUCTURE = :structure, CLASSIFICATION = :classification, IDFONCTION = :role WHERE MAIL = :mail");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':structure', $structure);
            $stmt->bindParam(':classification', $classification);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':mail', $email);
            $stmt->execute();

            $_SESSION['success_message'] = "Le profil a été mis à jour avec succès.";
            unset($_SESSION['form_data']); // Supprimer les anciennes valeurs en cas de succès
        }

        //  Suppression de l'utilisateur si la confirmation est reçue
        elseif ($_POST['form_type'] === "delete_user") {
            $email = filter_var($_POST['email1'] ?? '', FILTER_SANITIZE_EMAIL);
            $confirmation = $_POST['confirm'] ?? '';

            if ($confirmation === "true") {
                $stmt = $pdo->prepare("DELETE FROM GUIDASSO WHERE MAIL = :mail");
                $stmt->bindParam(':mail', $email);
                $stmt->execute();

                $_SESSION['success_message'] = "L'utilisateur a été supprimé avec succès.";
            } else {
                $_SESSION['error_message_modification'] = "Suppression annulée.";
            }
        }

        session_write_close();
        header("Location: /../views/pageadmin.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['error_message_modification'] = $e->getMessage();
        session_write_close();
        header("Location: /../views/pageadmin.php");
        exit();
    }
}
?>
