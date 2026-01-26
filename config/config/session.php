<?php
if(session_status() !== PHP_SESSION_ACTIVE) session_start();

$temps_Max = 25*60; // 25 minutes

// Vérifier si l'utilisateur est inactif depuis plus d'une minute
if (isset($_SESSION['start']) && (time() - $_SESSION['start']) > $temps_Max) {
    unset($_SESSION['MAIL']);
    unset($_SESSION['IDFONCTION']);
    unset($_SESSION['start']);
    header("Location: /../views/pageconnexion.php"); // Correction du chemin
    exit();
}

// Mettre à jour l’heure de la session
$_SESSION['start'] = time();
?>
