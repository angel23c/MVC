<?php
include("Usuarios.php");

$op = $_POST["op"];
$Usuarios = new Usuarios();

if ($op == "IniciaSesion") {
	$usuario = $_POST["usuario"];
	$clave = $_POST["clave"];
	echo trim($Usuarios->IniciaSesion($usuario,$clave));
}

if ($op == "obtenerUsuarios") {
	echo trim($Usuarios->obtenerUsuarios());
}

if ($op == "obtenerUsuario") {
	$idadministradores = $_POST["idadministradores"];
	echo trim($Usuarios->obtenerUsuario($idadministradores));
}

if ($op == "insertarUsuario") {
	$nombre = $_POST["nombre"];
	$usuario = $_POST["usuario"];
	$clave = $_POST["clave"];
	$idperfiles = $_POST["idperfiles"];

	echo trim($Usuarios->insertarUsuario($nombre,$usuario,$clave,$idperfiles));
}


if ($op == "actualizarUsuario") {
	$nombre = $_POST["nombre"];
	$usuario = $_POST["usuario"];
	$idperfiles = $_POST["idperfiles"];
	$idadministradores = $_POST["idadministradores"];

	echo trim($Usuarios->actualizarUsuario($nombre,$usuario,$idperfiles,$idadministradores));
}

if ($op == "CambiarStatusUsuario") {
	$status = $_POST["status"];
	$idadministradores = $_POST["idadministradores"];

	echo trim($Usuarios->CambiarStatusUsuario($status,$idadministradores));
}

if ($op == "obtenerRoles") {
	echo trim($Usuarios->obtenerRoles());
}

if ($op == "validaUsuarioActivo") {
	echo trim($Usuarios->validaUsuarioActivo());
}
?>
