<?php
require_once("myLib/myDb.php"); //Codigo para manejar conexion a base da datos.
require_once("myLib/myPw.php"); //Codigo para manejo de passwords.
require_once("myLib/myQuery.php"); //Codigo para manejo de queries. 
require_once("myLib/myMisc.php"); //Codigo misc. (Output con newline, crear hyperlinks, etc). 
require_once("myLib/mySession.php"); //Codigo para manejo de sesiones. 
require_once("myLib/myUser.php"); //Codigo para manejo de usuario. 

//Conexion a la base de datos.
$conexion = conectarDb();

//Manejo de forma para iniciar sesion
//Si la forma esta completa, se procesa de esta manera.
//1. Checar que el usuario exista en la base de datos.
//1.a. Si el usuario existe, obtener su id.
//2. Checar que password y id de la forma coincidan con password y id de la base de datos.
//3. Si coinciden, iniciar una sesion de php y pasar el usuario al sistema
//3.a. si no coinciden, pedir que ingrese credenciales correctas 

//Validar que la forma haya sido enviada con todos los elementos
//mandando una copia de POST a hayVacios().
$arrTemp = $_POST;
$seguirIniciandoSesion = false;

if(!hayVacios($arrTemp))
{
        //Usar la funcion convertirArreglo
        //para cargar los datos de $_POST a un arreglo
        //tradicional (indices ordenados ascendetes empezando
        //en 0) para no tener que declarar multiples
        //variables. El arreglo tradicional donde se pondran
        //los datos es $arrDatos.

        //Estructura de $arrDatos:
        //0 => Nombre de Usuario
        //1 => Password
        convertirArreglo($arrTemp, $arrDatos);
	//1.
	$nombreUsuario = "";
	$idUsuario = "";
	$hashUsuario = "";

	if(existeUsuario($arrDatos, $checarNombre, $checarId))
	{
		$nombreUsuario = $checarNombre;
		$idUsuario = $checarId;
		$seguirIniciandoSesion = true;
	}

	if(existePassword($arrDatos, $hashUsuario, $idUsuario) && $seguirIniciandoSesion)
	{
		haySesion();
		iniciarSesion($nombreUsuario, $hashUsuario, $idUsuario);
		
		$url = "fileHome.php";
		header("Location: ".$url);
		exit;
	}
}
else
{
	echoLine("Forma incompleta");
}

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

//Codigo de PHP con HTML.

//Forma para ingresar nuevo usuario y password.
echo <<<OUT

<div class="container-fluid img-rounded">

<div class="row">
	<div class="col-xs-12 text-center">
		<h1>Log In</h1>
	</div>
</div>


	<form action="iniciarSesion.php" method="post">
		<div class="form-group">
			<label for="nuevoUsuario"> Nombre de usuario: </label>
			<input class="form-control" type="text" name="nuevoUsuario">
		</div>

		<div class="form-group">
			<label for="nuevoPassword"> Password: </label>
			<input class="form-control" type="password" name="nuevoPassword">
		</div>

		<input class="btn btn-primary" value="Log in" type="submit" name="submitUsuario">
	</form>


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

?>
