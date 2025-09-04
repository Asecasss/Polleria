<?php
// Configuración de la base de datos
$server = "localhost";
$user = "root";
$password = "123456";
$db = "polleria_db";

$enlace = mysqli_connect($server, $user, $password, $db);

// Verificar conexión
if (!$enlace) {
    die("Fallo la conexion: " . mysqli_connect_error());
}

mysqli_set_charset($enlace, "utf8");
?>