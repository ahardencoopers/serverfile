<?php
require_once("myLib/myDb.php"); //Codigo para manejar conexion a base da datos.
require_once("myLib/myPw.php"); //Codigo para manejo de passwords.
require_once("myLib/myFs.php"); //Codigo para manejo de passwords.

noExpire();

//Conexion a la base de datos
$conexion = conectarDb();

//Inicializar directorio
iniciarDirectorio();

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
<body class="dotted">
OUT;

//Codigo de PHP con HTML
echo <<<OUT

<div class="container-fluid img-rounded">

	<div class="row">
		<div class="col-xs-12 text-center"> 
			<h1>serverfile</h1>
		 </div>
	</div>

	<div class="row">
		<div class="col-xs-5 center-block"> 
			<a href="iniciarSesion.php" class="btn btn-primary btn-block">Log in</a>
		 </div>
	</div>

	<div class="row">
		<div class="col-xs-5 center-block"> 
			<a href="crearUsuario.php" class="btn btn-default btn-block">Sign up</a>
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

//Javascript para bootstrap
echo <<<OUT

<script src="http://code.jquery.com/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

OUT;

//Cerrar encabezado de body y html 
echo <<<OUT
  </body>
</html>
OUT;

?>
