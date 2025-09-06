<?php
session_start();
include '../BackEnd/conexion.php'; // Ajusta la ruta a tu archivo de conexión

// Verificar que el formulario fue enviado por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $usuario  = $_POST['usuario'];
    $password = $_POST['password'];
    $rol      = $_POST['rol'];

    // Encriptar contraseña (seguridad)
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Preparar la consulta
    $sql = "INSERT INTO usuario (nombre, apellido, usuario, password_hash, rol)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $enlace->prepare($sql); // $enlace es tu conexión desde conexion.php
    $stmt->bind_param("sssss", $nombre, $apellido, $usuario, $password_hash, $rol);

    // Ejecutar
    if ($stmt->execute()) {
        echo "<script>
                alert('✅ Usuario agregado correctamente');
                window.location.href='usuarios.php'; 
              </script>";
    } else {
        echo "❌ Error al agregar usuario: " . $enlace->error;
    }

    $stmt->close();
    $enlace->close();
} else {
    echo "Acceso inválido.";
}
?>
