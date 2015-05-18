<?php
require_once("myLib/myDb.php"); //Codigo para manejar conexion a base da datos.
require_once("myLib/myPw.php"); //Codigo para manejo de passwords.
require_once("myLib/myQuery.php"); //Codigo para manejo de queries. 
require_once("myLib/myMisc.php"); //Codigo misc. (Output con newline, crear hyperlinks, etc) 
require_once("myLib/myUser.php"); //Codigo para manejo de usuarios en la base de datos. 

noExpire();

//Conexion a la base de datos.
$conexion = conectarDb();

//Manejo de forma

//Si la forma esta completa, se procesa de esta manera.
//1. Checar que no haya otro usuario con el mismo nombre.
//2. Si no hay usuario con mismo nombre, pasar a creacion de usuario.
//2.a. Checar que password y confirmar password coincidan.
//2.b. Sacar hash de la password.
//2.c. Terminar de crear usuario.
//3. Relacionar password del usuario con el usuario en la base de datos
//3.a. Obtener id unico del usuario recien creado con su nombre.
//3.b. ingresar hash del password y id unico del usuario en la tabla
//de passwords.

//Validar que la forma haya sido enviada con todos los elementos
//mandando una copia de POST a hayVacios().
$arrTemp = $_POST;
$seguirCreandoUsuario = false;

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
	//2 => Confirmar Password
	//3 => Tipo de usuario
	convertirArreglo($arrTemp, $arrDatos);

	if(agregarUsuario($arrDatos))
	{
		$seguirCreandoUsuario = true;	
	}
	else
	{
		$seguirCreandoUsuario = false;
	}

	
	//3. Relacionar password del usuario en la base de datos
	//3.a Obtener id unico del usuario recien creado con su nombre
	//Query para obtener id unico del usuario con su nombre
	if($seguirCreandoUsuario)
	{
		agregarPassword($arrDatos);
	}
}
else
{
	echoLine("Insuficientes datos para crear al usuario");
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
			<h1>Sign Up</h1>
		</div>
	</div>

	<form action="crearUsuario.php" method="post">
		<div class="form-group">
			<label for="nuevoUsuario"> Nombre de Usuario </label>
			<input type="text" class="form-control" name="nuevoUsuario">
		</div>

		<div class="form-group">
			<label for="nuevoPassword"> Password: </label> 
			<input type="password" class="form-control" name="nuevoPassword">
		</div>

		<div class="form-group">
			<label for="confirmarPassword">	Confirmar Password: </label>
			<input type="password" class="form-control" name="confirmarPassword">
		</div>

		<div class="form-group">
			<label for="tipoUsuario"> Tipo de usuario: </label>
			<select class="form-control" name="tipoUsuario">
			<option value="normal">Normal</option>
			<option value="root">Root</option>
			</select>
		</div>

		<input class="btn btn-primary" value="Sign Up" type="submit" name="submitUsuario">
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
