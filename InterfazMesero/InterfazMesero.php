<?php
session_start(); // Inicia la sesión

// Verificar si el usuario está logueado
if(!isset($_SESSION['usuario'])){
    header("Location: ../Login/Login.html");
    exit();
}

// Solo mesero o admin pueden acceder
if($_SESSION['rol'] != 'MESERO' && $_SESSION['rol'] != 'ADMIN') {
    die("❌ Acceso denegado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Mesero</title>
    <link rel="stylesheet" href="../dashboard/header.css">
    <link rel="stylesheet" href="styles/mesas.css">
</head>
<body>
    <?php include '../dashboard/header.php' ?>

    <h2 class="titulo-seccion">Gestión de Mesas</h2>

    <div class="mesas-container" id="mesas-container">
        <!-- Mesas hechas con js segun asi va a ser mas comodo luego-->
    </div>

    <div class="pedido-panel" id="pedido-panel" style="display: none;">
        <h2>Mesa <span id="mesa-seleccionada-num"></span></h2>

        <div>
            <strong>Categorías:</strong>
            <div class="categorias" id="categorias"></div>
        </div>

        <div>
            <strong>Subcategorías:</strong>
            <div class="subcategorias" id="subcategorias"></div>
        </div>

        <div class="pedido-lista">
            <strong>Pedido:</strong>
            <ul id="lista-pedido"></ul>
        </div>

        <button class="btn-enviar" id="btn-enviar">
            <i class="fas fa-paper-plane"></i> Enviar Pedido
        </button>
        <button class="btn-pedido-hecho" id="btn-pedido-hecho" style="display:none; margin-top: 1rem; background-color: #388e3c;">
            <i class="fas fa-check"></i> Pedido Hecho
        </button>
    </div>
</body>
<script src="mesas.js"></script>
</html>