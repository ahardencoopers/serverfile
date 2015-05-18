<?php
require_once("myLib/myDb.php"); //Codigo para manejar conexion a base da datos.
require_once("myLib/myPw.php"); //Codigo para manejo de passwords.
require_once("myLib/myQuery.php"); //Codigo para manejo de queries. 
require_once("myLib/myMisc.php"); //Codigo misc. (Output con newline, crear hyperlinks, etc) 
require_once("myLib/mySession.php"); //Codigo para manejo de sesiones.  
require_once("myLib/myFile.php"); //Codigo para manejo de archivos remotos
require_once("myLib/myFs.php"); //Codigo para manejo de sistema de archivos. 

noExpire();

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
//1. Se crea y se muestra el path donde se creo el directorio 
if($_POST['nombreDirectorio'] != "")
{
	$path = "";
	//Funcion para crear un directorio en la carpeta actual indicada por $path
	//myFile.php
	subirDirectorio($path);
	$creoDirectorio = subirArchivo();
	if($creoDirectorio)
	{
		echoLine("Se ha creado con exito el directorio en: archivosRoot/".$path.$_POST['nombreDirectorio']);
	}
	else
	{
		echoLine("No se pudo crear directorio en: archivosRoot/".$path.$_POST['nombreDirectorio']);
	}
}

if($_POST['nuevoDirectorio'] != "" && validarSesion())
{
	cambiarDirectorio($_POST['nuevoDirectorio']);
}

if($_POST['regresarDirectorio'] == "true")
{
	cambiarDirectorio("..");
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

	//Inicializar directorio
	$directorioActual = $_SESSION["directorioActual"];
	//1.a.
	//Abrir encabezado de body y html.
echo <<<OUT
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>serverfile</title>
		<meta name="viewport" content="width=device-width, initial scale=1.0">
		<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="bootstrap/css/custom.css">
		<link rel="stylesheet" type="text/css" href="bootstrap/css/dotted.css">
	</head>
	<body class="dotted">
OUT;

echo <<<OUT
<div class="container-fluid img-rounded">

	<div class="row">
		<div class="col-xs-12 text-center">
			<h1>Bienvenido, $nombreUsuario </h1>
			<form action="fileHome.php" method="post">
				<input type="hidden" name="logout" value="true">
				<input class="btn-link" type="submit" name="submitLogout" value="Cerrar sesion">
			</form>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-6">
			<h2>Subir Archivo</h2>
			<form action="fileHome.php" method="post" enctype="multipart/form-data">

				<div class="form-group">
					<label for="archivo"> Escoger Archivo: </label>
					<input class="btn btn-file btn-default" type="file" name="archivo"/>
				</div>

				<div class="form-group">
					<label for="descArchivo"> Descripcion: </label>
					<input class="form-control" type="text" name="descArchivo" size="25">
				</div>

				<div class="form-group">
					<label for="visiArchivo"> Visibilidad: </label> 
					<br>
					<fieldset class="btn">
						<input type="radio" name="visiArchivo" value="publico">Publico
						<br>
						<input type="radio" name="visiArchivo" value="privado">Privado
					</fieldset>
				</div>

				<input class="btn btn-primary" type="submit" name="subirArchivo" value="Subir Archivo">
			</form>
		</div>

		<div class="col-xs-6">
			<h2>Crear Carpeta</h2>

			<form action="fileHome.php" method="post">
				<div class="form-group">
					<label for="nombreDirectorio"> Nombre de carpeta: </label>
					<input class="form-control" type="text" name="nombreDirectorio" size="25"/>
				</div>

				<div class="form-group">
					<label for="descArchivo"> Descripcion: </label>
					<input class="form-control" type="text" name="descArchivo" size="25">
				</div>

				<div class="form-group">
					<label for="visiArchivo"> Visibilidad: </label> 
					<br>
					<fieldset class="btn">
						<input type="radio" name="visiArchivo" value="publico">Publico
						<br>
						<input type="radio" name="visiArchivo" value="privado">Privado
					</fieldset>
				</div>

				<input type="hidden" name="crearDirectorio" value="true">

				<input class="btn btn-primary" type="submit" name="crearCarpeta" value="Crear Carpeta">
			</form>
		</div>

	</div>


	<div class="row">
		<div class = "col-xs-12">
			<h1>Archivos en: $directorioActual</h1>	

			<form action="fileHome.php" class="left-link important-link" method="post">
				<input type="hidden" name="regresarDirectorio" value="true">
				<input class="btn-link" type="submit" name="submitRegresar" value="Regresar una carpeta">
			</form>

		
OUT;

	mostrarArchivos();

echo <<<OUT
			</table>
		</div>
	</div>


	<div class="row-fluid">
		<div class="col-xs-12 text-center">
			<div class="footer text-center">
				<p>ahardencoopers@gmail.com</p>
				<p><a href="https://github.com/ahardencoopers/serverfile">https://github.com/ahardencoopers/serverfile</p>
			</div>
		</div>
	</div>


</div>

OUT;


	//Cerrar encabezado de body y html.
echo <<<OUT
	<script src="http://code.jquery.com/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
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
		<meta charset="utf-8">
		<title>serverfile</title>
		<meta name="viewport" content="width=device-width, initial scale=1.0">
		<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="bootstrap/css/custom.css">
		<link rel="stylesheet" type="text/css" href="bootstrap/css/dotted.css">

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
	<script src="http://code.jquery.com/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>
OUT;
}

?>
