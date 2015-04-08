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

<div class="container-fluid img-rounded myContainer">

	<div class="row">
		<div class="col-xs-12 text-center"> 
			<h1>serverfile</h1>
		 </div>
	</div>

	<div class="row-fluid">
		<div class="col-xs-4 col-xs-offset-2 text-center"> 
			<button type="button" class="btn btn-primary btn-block">
				Log in
				<a href="iniciarSesion.php"></a>
			</button>
		 </div>
	</div>

	<div class="row-fluid">
		<div class="col-xs-4 text-center"> 
			<button type="button" class="btn btn-block">
				Sign up
				<a href="crearUsuario.php"></a>
			</button>
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
