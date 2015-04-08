<?php
require_once("myLib/myDb.php"); //Codigo para manejar conexion a base da datos.
require_once("myLib/myPw.php"); //Codigo para manejo de passwords.

//Conexion a la base de datos
$conexion = conectarDb();

//Abrir encabezado de body y html
echo <<<OUT
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Basic HTML File</title>
		<meta name="viewport" content="width=device-width, initial scale=1.0">
	<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
	</head>
<body>
OUT;

//Codigo de PHP con HTML
echo <<<OUT

<a href="crearUsuario.php">Sign up</a>
<a href="iniciarSesion.php">Log in </a>
<script src="http://code.jquery.com/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>




OUT;

//Cerrar encabezado de body y html 
echo <<<OUT
  </body>
</html>
OUT;

?>
