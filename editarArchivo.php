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

if($_POST['submitEditarArchivo'] == "Editar" && validarSesion())
{
    $editarArchivo = $_POST['editarArchivo'];
}
else if($_POST['submitEditarArchivo'] == "Actualizar Archivo" && validarSesion())
{
	$directorioActual = $_SESSION["directorioActual"];

	$editarArchivo = $_POST['nombreOriginal'];
	$editarNombre = $_POST['editarNombre'];
	$editarDescr = $_POST['editarDescr'];
	$nombreCambiarUsuario = $_POST['cambiarUsuario'];
	if($_POST['listaVisib'] == "publico")
	{
		$editarVisib = 0; 
	}
	else
	{
		$editarVisib = 1;	
	}
	$editarPath = $directorioActual.$editarNombre;

	$queryCambiarUsuarioId = "SELECT id FROM Usuarios WHERE nombre = ?";
	if(prepararQuery($queryCambiarUsuarioId, $stmtCambiarUsuarioId, $conexion))
	{
		mysqli_stmt_bind_param($stmtCambiarUsuarioId, "s", $nombreCambiarUsuario);
		mysqli_stmt_execute($stmtCambiarUsuarioId);
		mysqli_stmt_bind_result($stmtCambiarUsuarioId, $idCambiarUsuario);
		mysqli_stmt_fetch($stmtCambiarUsuarioId);
		mysqli_stmt_store_result($stmtCambiarUsuarioId);
	}
	else
	{
		echoLine("Error al obtener id del nuevo usuario.");
	}

	if(($idCambiarUsuario != "" && $idCambiarUsuario != 0) || $nombreUsuario == $nombreCambiarUsuario)
	{
		$queryEditarArchivo = "UPDATE Archivos SET creadorFk = ?, nombre= ? , descr= ?, visib= ?, path = ? WHERE nombre= ?";

		if(prepararQuery($queryEditarArchivo, $stmtEditarArchivo, $conexion))
		{
			mysqli_stmt_bind_param($stmtEditarArchivo, "ississ", $idCambiarUsuario, $editarNombre, $editarDescr, 
				$editarVisib, $editarPath, $editarArchivo);
			mysqli_stmt_execute($stmtEditarArchivo);
			echoLine("El archivo ".$editarArchivo." se ha actualizado exitosamente.");
			
			$stringComando = "mv ".$directorioActual.$editarArchivo." ".$directorioActual.$editarNombre;
			exec($stringComando);

			$editarArchivo = $editarNombre;
			mysqli_stmt_store_result($stmtEditarArchivo);
		}
		else
		{
			echoLine("Error al actualizar archivo.");
		}
	}
	else
	{
		echoLine('Ingrese un usuario valido en el campo "Cambiar dueño del archivo"');
	}
	
}
else if($_POST['submitBorrarArchivo'] == "Borrar Archivo" )
{
	$directorioActual = $_SESSION['directorioActual'];
	$borrarArchivo = $_POST['nombreOriginal'];
	$tipoArchivo = $_POST['tipoArchivo'];

	if($tipoArchivo == "dir")
	{
			$queryBorrarArchivo = "DELETE FROM Archivos WHERE nombre LIKE ? OR path LIKE ?;";
			if(prepararQuery($queryBorrarArchivo, $stmtBorrarArchivo, $conexion))
			{
				$stringComando = "rm -R ".$directorioActual.$borrarArchivo;
				exec($stringComando);

				$borrarArchivo = "%".$borrarArchivo."%";
				mysqli_stmt_bind_param($stmtBorrarArchivo, "ss", $borrarArchivo, $borrarArchivo);
				mysqli_stmt_execute($stmtBorrarArchivo);
				mysqli_stmt_store_result($stmtEditarArchivo);

				echoLine("Se ha borrado el archivo ".$borrarArchivo." exitosamente.");
			}
			else
			{
				echoLine("Error al borrar archivo ".$borrarArchivo);
			}

	}
	else if($tipoArchivo != "dir")
	{
		$queryBorrarArchivo = "DELETE FROM Archivos WHERE nombre LIKE ?;";
		if(prepararQuery($queryBorrarArchivo, $stmtBorrarArchivo, $conexion))
		{
			$stringComando = "rm -R ".$directorioActual.$borrarArchivo;
			exec($stringComando);

			$borrarArchivo = $borrarArchivo;
			mysqli_stmt_bind_param($stmtBorrarArchivo, "s", $borrarArchivo);
			mysqli_stmt_execute($stmtBorrarArchivo);
			mysqli_stmt_store_result($stmtEditarArchivo);

			echoLine("Se ha borrado el archivo ".$borrarArchivo." exitosamente.");
		}
		else
		{
				echoLine("Error al borrar archivo ".$borrarArchivo);
		}

	}
	else
	{
		$editarArchivo = "No se ha seleccionado ningun archivo.";
	}
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
			<form action="editarArchivo.php" method="post">
				<input type="hidden" name="logout" value="true">
				<input class="btn-link" type="submit" name="submitLogout" value="Cerrar sesion">
			</form>
		</div>
	</div>

	<div class="row">
		
	</div>


	<div class="row">
		<div class = "col-xs-12">
			<h1>Editando: <a href="$directorioActual$editarArchivo" target="_blank">$editarArchivo</a></h1>	
			<h4>En: $directorioActual</h4>

			<form action="fileHome.php" class="left-link important-link" method="post">
				<input class="btn-link" type="submit" name="volverFileHome" value="Volver a subir archivos">
			</form>

		
OUT;

	mostrarEditarArchivo($editarArchivo);

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
