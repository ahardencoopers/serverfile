<?php
require_once("myLib/myDb.php"); //Codigo para manejar conexion a base da datos.
require_once("myLib/myPw.php"); //Codigo para manejo de passwords.
require_once("myLib/myQuery.php"); //Codigo para manejo de queries. 
require_once("myLib/myBind.php"); //Codigo para manejo de queries. 
require_once("myLib/myMisc.php"); //Codigo misc. (Output con newline, crear hyperlinks, etc) 

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
$seguirCreandoUsuario = true;

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
	//2 => Confirmar Password
	//3 => Tipo de usuario
	convertirArreglo($arrTemp, $arrDatos);
	
	//1. Checar que no haya otro usuario con el mismo nombre.
	//Query para verificar que no haya otro usuario con el mismo nombre.
	$queryChecarUsuario = "SELECT nombre FROM Usuarios WHERE nombre = ?";

	if(prepararQuery($queryChecarUsuario, $stmtChecarUsuario, $conexion) && $seguirCreandoUsuario)
	{
		mysqli_stmt_bind_param($stmtChecarUsuario, "s", $arrDatos[0]);
		mysqli_stmt_execute($stmtChecarUsuario);
		mysqli_stmt_bind_result($stmtChecarUsuario, $checarNombre);
		mysqli_stmt_fetch($stmtChecarUsuario);

	}
	else
	{
		echoLine("Error al checar usuarios");
		$seguirCreandoUsuario = false;
	}

	mysqli_stmt_store_result($stmtChecarUsuario);

	//2. Si no hay un usuario con el mismo nombre, crear al usuario.
	//Query para crear un nuevo usuario en la base de datos.
	$queryCrearUsuario =  "INSERT INTO Usuarios (nombre, tipo) VALUES (?, ?)";

	//Verificar si las passwords son iguales y no hay otro usuario con
	//el mismo nombre.
	//Si lo anterior se cumple, crear al usuario.
	if($arrDatos[1] == $arrDatos[2] && $arrDatos[0] != $checarNombre)
	{
		//Asignar el tipo de usuario correspondiente.
		if($arrDatos[3] == "normal")
		{
			$arrDatos[3] = 1;
		}
		else
		{
			$arrDatos[3] = 0;
		}
		
		//Crear hash de la password y borrar 
		//password de confirmacion.
		$arrDatos[1] = hashPassword($arrDatos[1]);
		$arrDatos[2] = "";

		//Si se logra preparar query, seguir ejecucion para crear usuario.
		if(prepararQuery($queryCrearUsuario, $stmtCrearUsuario, $conexion) && $seguirCreandoUsuario)
		{
			//Pasar valores a y ejecutar query.
			/*mysqli_stmt_bind_param($stmtCrearUsuario, "si", $arrDatos[0], $arrDatos[3]);
			mysqli_stmt_execute($stmtCrearUsuario);*/

			$formatoValoresTemp = "si";
			prepararBind($stmtCrearUsuario, $formatoValoresTemp, $arrDatos[0], $arrDatos[3]);
			pasarValores($stmtCrearUsuario, $formatoValoresTemp, $arrDatos[0], $arrDatos[3]);
			ejecutarQuery($stmtCrearUsuario);
		}
		else
		{
			echoLine("Error al preparar query.");
		}

		mysqli_stmt_store_result($stmtCrearUsuario);
	}
	else
	{
		echoLine("Las passwords no coinciden o hay un usuario con el mismo nombre. 
		Vuelve a ingresar los datos");
		$seguirCreandoUsuario = false;
	}

	//3. Relacionar password del usuario en la base de datos
	//3.a Obtener id unico del usuario recien creado con su nombre
	//Query para obtener id unico del usuario con su nombre
	$queryObtenerId = "SELECT id FROM Usuarios WHERE nombre = ?";
	
	if(prepararQuery($queryObtenerId, $stmtObtenerId, $conexion) && $seguirCreandoUsuario && $seguirCreandoUsuario)
	{
		mysqli_stmt_bind_param($stmtObtenerId, "s", $arrDatos[0]);
		mysqli_stmt_execute($stmtObtenerId);
		mysqli_stmt_bind_result($stmtObtenerId, $idUsuario);
		mysqli_stmt_fetch($stmtObtenerId);
	}
	else
	{
		echoLine("Error al obtener id unico del usuario.");

	}

	mysqli_stmt_store_result($stmtObtenerId);

	//Despues de ejecutar una query que regresa un set de resultados,
	//si no se termina de hacer fetch de todos los resultados del fetch
	//cualquier query subsequente que se quiera ejecutar dara un
	//"Command out of sync error", lo cual indica que aun quedan resultados
	//a los cuales hacer fetch antes de seguir procesando queries.
	//Para esto se debe de procesar todos los resultados para seguir
	//ejecutando queries nuevas. Esta comando "limpia" todos los resultados
	//sobre los que se no se hizo un fetch.
	mysqli_stmt_store_result($stmtObtenerId);

	//3.b Ingresar la hash del password y id unico del usuario en la tabla
	//de passwords.
	//Query para insertar hash del password en la tabla de passwords.
	$queryInsertarPassword = "INSERT INTO Passwords (usuarioFK, password) VALUES (?, ?)";


	if(prepararQuery($queryInsertarPassword, $stmtInsertarPassword, $conexion) && $seguirCreandoUsuario)
	{
		mysqli_stmt_bind_param($stmtInsertarPassword, "is", $idUsuario, $arrDatos[1]);
		mysqli_stmt_execute($stmtInsertarPassword);
	}
	else
	{
		echoLine("Error al Ingresar password en la base de datos");
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
  </head>
  <body>
OUT;

//Codigo de PHP con HTML.

//Forma para ingresar nuevo usuario y password.
echo <<<OUT
<form action="crearUsuario.php" method="post">
	Nombre de usuario: <input type="text" name="nuevoUsuario">
	<br>
	Password: <input type="password" name="nuevoPassword">
	<br>
	Confirmar Password: <input type="password" name="confirmarPassword">
	<br>
	Tipo de usuario: <select name="tipoUsuario">
		<option value="normal">Normal</option>
		<option value="root">Root</option>
	</select>
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
