<?php
session_start();
include("conexion.php");

if (empty($_POST['usuario']) || empty($_POST['password'])) {
    die("❌ Usuario y contraseña requeridos 
         <br><a href='../Login/Login.html'>Volver al login</a>");
}

$usuario = mysqli_real_escape_string($enlace, $_POST['usuario']);
$password = mysqli_real_escape_string($enlace, $_POST['password']);

$sql = "SELECT id_usuario, nombre, apellido, usuario, rol 
        FROM usuario 
        WHERE usuario='$usuario' AND password_hash='$password'";

$resultado = mysqli_query($enlace, $sql);

if ($resultado && mysqli_num_rows($resultado) > 0) {
    $user = mysqli_fetch_assoc($resultado);

    $_SESSION['usuario_id'] = $user['id_usuario'];
    $_SESSION['usuario'] = $user['usuario'];
    $_SESSION['nombre_completo'] = $user['nombre'] . ' ' . $user['apellido'];
    $_SESSION['rol'] = $user['rol'];

    // Redirigir según rol
    if($user['rol'] == 'ADMIN') {
        header("Location: ../InterfazMesero/interfazMesero.php");
    } elseif($user['rol'] == 'MESERO') {
        header("Location: ../InterfazMesero/interfazMesero.php");
    } elseif($user['rol'] == 'CAJERO') {
        header("Location: ../InterfazCajero/interfazCajero.php");
    } else {
        echo "Rol no válido";
        exit();
    }
    exit();

} else {
    echo "Usuario o contraseña incorrectos 
          <br><a href='../Login/Login.html'>Volver al login</a>";
}

mysqli_close($enlace);
?>