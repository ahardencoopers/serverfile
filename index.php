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
  </head>
  <body>
OUT;

//Codigo de PHP con HTML
echo <<<OUT

<a href="crearUsuario.php">Sign Up</a>

OUT;

//Cerrar encabezado de body y html 
echo <<<OUT
  </body>
</html>
OUT;

?>
