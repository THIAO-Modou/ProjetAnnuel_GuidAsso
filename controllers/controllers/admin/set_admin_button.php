<?php
session_start();
if (isset($_POST['boutonClique'])) {
    $_SESSION['bouton_admin_actif'] = $_POST['boutonClique']; 
}
http_response_code(204);
?>