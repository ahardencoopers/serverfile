<?php
require_once("myLib/myDb.php"); //Codigo para manejar conexion a base da datos.
require_once("myLib/myPw.php"); //Codigo para manejo de passwords.
require_once("myLib/myQuery.php"); //Codigo para manejo de queries. 
require_once("myLib/myMisc.php"); //Codigo misc. (Output con newline, crear hyperlinks, etc) 
require_once("myLib/mySession.php"); //Codigo para manejo de sesiones.  
require_once("myLib/myFile.php"); //Codigo para manejo de archivos remotos

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

//Manejo para subir archivos
if($_FILES['archivo']['name'])
{
	if(!$_FILES['archivo']['error'])
	{
		subirArchivo();
	}
	else
	{
		echoLine("Error al subir archivo al servidor");
	}
}

//Manejo para crear directorios
//Si se llena y se entrega la forma para crear un directorio
//1. Se se crea y se muestra el path donde se creo el directorio 
if($_POST['nombreDirectorio'] != "")
{
	$path = "";
	//Funcion para crear un directorio en la carpeta actual indicada por $path
	//myFile.php
	subirDirectorio($path);
	echoLine("Se ha creado con exito el directorio en: archivosRoot/".$path.$_POST['nombreDirectorio']);
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
	//Funcion para crear mensaje de bienvenido.
	//myMisc.php
	bienvenido($nombreUsuario);
	//Funcion para crear forma de logout
	//mySession.php
	crearLogout("fileHome.php");

	//Forma para subir archivos.
	//myFile.php
	crearFormaArchivo("fileHome.php");
	echoLine("");

	//Forma para crear directorios
	//myFile.php
	crearFormaDirectorio("fileHome.php");
	echoLine("");



	//Cerrar encabezado de body y html.
	echo <<<OUT
	</body>
	</html>
OUT;

}
else	//HTML Para sesion invalida.
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
	<p><a href="iniciarSesion.php">Volver a log in<a/></p>
OUT;

	//Cerrar encabezado de body y html.
	echo <<<OUT
	</body>
	</html>
OUT;
}

?>
