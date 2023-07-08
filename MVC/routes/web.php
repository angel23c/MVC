<?php

use App\Controllers\ControllerMembresia;
use App\Controllers\ControllerUsuarios;
use Rutas\Ruts;
//Rutas para la creacion del usuario
Ruts::getInstance('api')
    ->api()
    ->post("/registerapp", [ControllerUsuarios::class, "registerapp"])
    ->post("/loginapp", [ControllerUsuarios::class, "loginapp"])
    ->post("/cambiarclave", [ControllerUsuarios::class, "cambiarclave"])
    ->post("/correocambioclave", [ControllerUsuarios::class, "correocambioclave"])
    ->get("/verificacion/:codigo", [ControllerUsuarios::class, "verificacion"])
    ->get("/emailcambiarclave/:codigo", [ControllerUsuarios::class, "emailcambiarclave"])
    ->get("/verusuarios", [ControllerUsuarios::class, "verusuarios"])
    ->dispatch();
// Iniciar sesion automaticamente con el api_key
Ruts::getInstance('api_auth')
    ->key()
    ->post("/loginapi", [ControllerUsuarios::class, "loginapi"])
    ->dispatch();

?>