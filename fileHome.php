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

//Manejo de logout
//Si la sesion es valida, hacer llamada a funcion de mySession.php terminarSesion(),
if($_POST['logout'] == "true" && validarSesion())
{
	terminarSesion();
	$url = "index.php";
	header("Location: ".$url);
	exit;
}

//Manejo de sitio
//1. Si la sesion es valida, cargar credenciales de usuario 
//de arreglo super global $_SESSION[] e interfaz de usuario
//1.a. La interfaz de usuario consiste de un mensaje, boton de logout y opciones
//para manipular archivos y directorios (subir archivos, crear directorios etc.)
//2. Si la sesion no es valida,  desplegar que la sesion no es valida y Mostrar una liga para que el usuario haga login.
//3. Si el usuario hace clic en log out, terminar la sesion y regresarlo a index.php.

//1.
if(validarSesion())
{
	$nombreUsuario = $_SESSION["nombre"];
	$hashUsuario = $_SESSION["hash"];
	$idUsuario = $_SESSION["id"];

	$mensaje = "Bienvenido, ".$nombreUsuario;
	//1.a.
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
	<form action="fileHome.php" method="post">
		<input type="hidden" name="logout" value="true">
		<input type="submit" name="submitLogout">
	</form>



OUT;

	//Cerrar encabezado de body y html.
	echo <<<OUT
	</body>
	</html>
OUT;

}
else
{
	//Abrir encabezado de body y html
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
	<h1>Sesion invalida</h1>
OUT;

	//Cerrar encabezado de body y html.
	echo <<<OUT
	</body>
	</html>
OUT;
}

?>
