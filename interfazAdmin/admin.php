<?php
session_start();
include("../BackEnd/conexion.php");

// Verificar si hay sesión iniciada
if (!isset($_SESSION['usuario'])) {
    header("Location: ../Login/Login.html");
    exit();
}

// Verificar rol
if ($_SESSION['rol'] !== 'ADMIN') {
    die("Acceso denegado. Solo administradores.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador</title>
    <link rel="stylesheet" href="../dashboard/header.css">
    <link rel="stylesheet" href="stylesAdmin/styles.css">
</head>
<body>
    <?php include '../dashboard/header.php'; ?>

    <?php include '../controlUsuarios/controlUsuarios.php'; ?>



</body>
</html>

