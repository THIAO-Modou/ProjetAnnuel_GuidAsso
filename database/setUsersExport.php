<?php
session_start();

if (isset($_POST['users'])) {
    $_SESSION['selected_users'] = $_POST['users']; // tableau d’IDCONTACT
} else {
    unset($_SESSION['selected_users']); // si rien sélectionné
}

exit();
?>