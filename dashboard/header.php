<?php

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Dosis:wght@200..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <link rel="stylesheet" href="header.css">
    <title>Pico Dorado</title>
</head>

<header>
    <div class="logo">
        <img src="../imagenes/logoPicoDorado.png" alt="Logo Pico Dorado"/>
        <h1>Pico Dorado</h1>
    </div>

    <nav>
        <ul class="nav">
            <?php if($_SESSION['rol'] == 'ADMIN'){ ?>
                <li><a href="../InterfazMesero/InterfazMesero.php">Panel Principal</a></li>
                <li><a href="../InterfazAdmin/usuarios.php">Gestión de usuarios</a></li>
                <li><a href="../InterfazAdmin/reportes.php">Reportes</a></li>
            <?php } ?>

            <?php if($_SESSION['rol'] == 'MESERO'){ ?>
                <li><a href="../InterfazMesero/InterfazMesero.php">Panel Principal</a></li>
            <?php } ?>

            <?php if($_SESSION['rol'] == 'CAJERO'){ ?>
                <li><a href="../InterfazMesero/InterfazMesero.html">Panel Principal</a></li>
                <li><a href="../InterfazCajero/interfazCajero.php">Pagos Totales</a></li>
            <?php } ?>

            <!-- Falta la opcion de cerrar sesion -->
            <!-- <li><a href="../logout.php">Cerrar sesión</a></li> -->
        </ul>
    </nav>
</header>
</html>