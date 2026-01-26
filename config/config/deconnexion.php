<?php
session_start();
unset($_SESSION['MAIL']);
unset($_SESSION['IDPERSONNE']); //déconnecte la fonction

session_destroy();

header("Location: /../views/pageconnexion.php");

?>

