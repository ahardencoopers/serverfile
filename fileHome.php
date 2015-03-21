<?php
require_once("myLib/myDb.php"); //Codigo para manejar conexion a base da datos.
require_once("myLib/myPw.php"); //Codigo para manejo de passwords.
require_once("myLib/myQuery.php"); //Codigo para manejo de queries. 
require_once("myLib/myMisc.php"); //Codigo misc. (Output con newline, crear hyperlinks, etc) 
require_once("myLib/mySession.php"); //Codigo para manejo de sesiones.  

//Conexion a la base de datos.
$conexion = conectarDb();

//Checar si hay una sesion iniciada, si no, iniciarla 
haySesion();

//Validar sesion de php
if(validarSesion())
{
	$nombreUsuario = $_SESSION["nombre"];
	$mensaje = "Bienvenido, ".$nombreUsuario;
	terminarSesion();
}
else
{
	$mensaje = "Sesion invalida";
}

//Abrir encabezado de body y html.
echo <<<OUT
<!DOCTYPE html>
<html lang="en">
  <head>
  </head>
  <body>
OUT;

//Codigo de PHP con HTML.

//Forma para ingresar nuevo usuario y password.
echo <<<OUT
	<h1>$mensaje</h1>
OUT;

//Cerrar encabezado de body y html.
echo <<<OUT
  </body>
</html>
OUT;

?>
