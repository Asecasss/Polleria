<?php

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pico Dorado</title>
    <link href="https://fonts.googleapis.com/css2?family=Dosis:wght@200..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="header.css">
</head>
<body>
<header>
    <div class="logo">
        <img src="../imagenes/logoPicoDorado.png" alt="Logo Pico Dorado"/>
        <h1>Pico Dorado</h1>
    </div>


    <button id="menu" class="menu"><i class="bi bi-list"></i></button>
    <nav>

        <ul class="nav" id="nav">
            <button id="cerrar" class="cerrar-menu"><i class="bi bi-x-square"></i></button>

            <?php if($_SESSION['rol'] == 'ADMIN'){ ?>
                <li><a href="../InterfazMesero/InterfazMesero.php">Panel Principal</a></li>
                <li><a href="../controlUsuarios/controlUsuarios.php">Gestión de usuarios</a></li>
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
</body>
<script>
    const nav = document.querySelector('#nav');
    const menu = document.querySelector('#menu');
    const cerrar = document.querySelector('#cerrar');

    menu.addEventListener('click', () => {
        nav.classList.add('visible');
    })

    cerrar.addEventListener('click', () => {
        nav.classList.remove('visible');
    })
</script>
</html>