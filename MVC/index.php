<?php
$apiresponse = false; // Variable para controlar si ya se ha impreso el mensaje
$mensajeImpreso =false; // Bandera para controlar si ya se ha impreso el mensaje
$json = file_get_contents('php://input');
$datos = json_decode($json, false);
require_once "./credentials.php";
require_once "./autoload.php";
require_once "./routes/web.php";
require_once "./public/js/config_global/formatjs.php";
require_once "./public/css/config_global/formatcss.php";
?>
