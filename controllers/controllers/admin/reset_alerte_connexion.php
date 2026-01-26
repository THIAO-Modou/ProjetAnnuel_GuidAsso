<?php
session_start();
unset($_SESSION['ALERTE_CONNEXION']);
unset($_SESSION['EMAIL_TENTE']);
unset($_SESSION['LISTE_ADMIN_EMAILS']);

header('Content-Type: application/json');
echo json_encode(["status" => "ok"]);
exit();
