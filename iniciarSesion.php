<?php
require_once("myLib/myDb.php"); //Codigo para manejar conexion a base da datos.
require_once("myLib/myPw.php"); //Codigo para manejo de passwords.
require_once("myLib/myQuery.php"); //Codigo para manejo de queries. 
require_once("myLib/myMisc.php"); //Codigo misc. (Output con newline, crear hyperlinks, etc). 
require_once("myLib/mySession.php"); //Codigo para manejo de sesiones. 

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
$seguirIniciandoSesion = true;

if(!hayVacios($arrTemp))
{
        //Usur la funcion convertirArreglo
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
	$queryChecarUsuario = "SELECT nombre,id FROM Usuarios WHERE nombre = ?";

	if(prepararQuery($queryChecarUsuario, $stmtChecarUsuario, $conexion))
	{
		mysqli_stmt_bind_param($stmtChecarUsuario, "s", $arrDatos[0]);
                mysqli_stmt_execute($stmtChecarUsuario);
                mysqli_stmt_bind_result($stmtChecarUsuario, $checarNombre, $checarId);
                mysqli_stmt_fetch($stmtChecarUsuario);	

		if($arrDatos[0] == $checarNombre)
		{
			$nombreUsuario = $checarNombre;
			$idUsuario = $checarId;
		}
		else
		{
			$seguirIniciandoSesion = false;
		}

	}
	else
	{
		$seguirIniciandoSesion = false;
	}

	//Purgar resultados
	mysqli_stmt_store_result($stmtChecarUsuario);	

	//2.
	$queryChecarPassword = "SELECT password FROM Passwords WHERE usuarioFK = ?";
	
	if(prepararQuery($queryChecarPassword, $stmtChecarPassword, $conexion))
	{
		mysqli_stmt_bind_param($stmtChecarPassword, "i",  $idUsuario);
	        mysqli_stmt_execute($stmtChecarPassword);
	        mysqli_stmt_bind_result($stmtChecarPassword, $checarPassword);
	        mysqli_stmt_fetch($stmtChecarPassword);
	
		//3.
		if(password_verify($arrDatos[1], $checarPassword))
		{
			$hashUsuario = $checarPassword;

			haySesion();
			iniciarSesion($nombreUsuario, $hashUsuario, $idUsuario);
			var_dump($_SESSION);

			$url = "fileHome.php";
			header("Location: ".$url);
			exit;
		}
		else
		{
			echoLine("Credenciales invalidas");
		}

		mysqli_stmt_store_result($stmtChecarUsuario);	

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
  </head>
  <body>
OUT;

//Codigo de PHP con HTML.

//Forma para ingresar nuevo usuario y password.
echo <<<OUT
<form action="iniciarSesion.php" method="post">
	Nombre de usuario: <input type="text" name="nuevoUsuario">
	<br>
	Password: <input type="password" name="nuevoPassword">
	<br>
	<input type="submit" name="submitUsuario">
</form>
OUT;

//Cerrar encabezado de body y html.
echo <<<OUT
  </body>
</html>
OUT;

?>
