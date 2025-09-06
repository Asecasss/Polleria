<?php
session_start();
include("../BackEnd/conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'ADMIN') {
    die("❌ Acceso denegado. Solo administradores.");
}

// ✅ Si se envía el formulario para AGREGAR usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    $nombre = mysqli_real_escape_string($enlace, $_POST['nombre']);
    $apellido = mysqli_real_escape_string($enlace, $_POST['apellido']);
    $usuario = mysqli_real_escape_string($enlace, $_POST['usuario']);
    $password = mysqli_real_escape_string($enlace, $_POST['password']);
    $rol = $_POST['rol'];

    $sql = "INSERT INTO usuario (nombre, apellido, usuario, password_hash, rol)
            VALUES ('$nombre', '$apellido', '$usuario', '$password', '$rol')";
    mysqli_query($enlace, $sql);

    header("Location: controlUsuarios.php?msg=agregado");
    exit();
}

// ✅ Eliminar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id = intval($_POST['id_usuario']);

    $stmt = mysqli_prepare($enlace, "DELETE FROM usuario WHERE id_usuario = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: controlUsuarios.php?msg=eliminado");
        exit();
    } else {
        echo "❌ Error al eliminar: " . mysqli_error($enlace);
    }

    mysqli_stmt_close($stmt);
}

// ✅ Si se envió el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'], $_POST['rol'])) {
    $id = intval($_POST['id_usuario']);
    $nuevoRol = $_POST['rol'];

    // ✅ Usar prepared statements
    $stmt = mysqli_prepare($enlace, "UPDATE usuario SET rol = ? WHERE id_usuario = ?");
    mysqli_stmt_bind_param($stmt, "si", $nuevoRol, $id);

    if (mysqli_stmt_execute($stmt)) {
        // Éxito
        header("Location: controlUsuarios.php?msg=ok");
        exit();
    } else {
        echo "❌ Error al actualizar: " . mysqli_error($enlace);
    }

    mysqli_stmt_close($stmt);
}

// ✅ Obtener todos los usuarios
$sql = "SELECT id_usuario, nombre, apellido, usuario, rol FROM usuario";
$resultado = mysqli_query($enlace, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link href="https://fonts.googleapis.com/css2?family=Dosis:wght@200..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../dashboard/header.css">
    <link rel="stylesheet" href="stylesUsuarios/styles.css">
</head>
<body>
<?php include '../dashboard/header.php'; ?>

<div class="container">
    <h2>Gestión de Usuarios</h2>

    <!-- Botón para desplegar formulario -->
    <button class="btn" onclick="toggleForm()">Agregar Usuario</button>

    <!-- Formulario oculto -->
    <div class="form-container" id="formUsuario">
        <h3>Nuevo Usuario</h3>
        <form class="formularioAgregar" action="controlUsuarios.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" required>
            </div>
            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" name="apellido" id="apellido" required>
            </div>
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" name="usuario" id="usuario" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="rol">Rol:</label>
                <select name="rol" id="rol" required>
                    <option value="">Seleccione un rol</option>
                    <option value="MESERO">Mesero</option>
                    <option value="CAJERO">Cajero</option>
                    <option value="ADMIN">Admin</option>
                </select>
            </div>

            <input type="hidden" name="accion" value="agregar">

            <button type="submit" class="btn">Guardar</button>
        </form>
    </div>

    <!-- Tabla de Control de Usuarios -->
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre completo</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Acción</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($resultado)) { ?>
            <tr>
                <td><?= $row['id_usuario'] ?></td>
                <td><?= $row['nombre'] . " " . $row['apellido'] ?></td>
                <td><?= $row['usuario'] ?></td>
                <td>
                    <form method="POST" action="controlUsuarios.php" class="rolForm">
                        <input type="hidden" name="id_usuario" value="<?= $row['id_usuario'] ?>">
                        <label>
                            <select name="rol">
                                <option value="ADMIN" <?= $row['rol'] == 'ADMIN' ? 'selected' : '' ?>>Admin</option>
                                <option value="MESERO" <?= $row['rol'] == 'MESERO' ? 'selected' : '' ?>>Mesero</option>
                                <option value="CAJERO" <?= $row['rol'] == 'CAJERO' ? 'selected' : '' ?>>Cajero</option>
                            </select>
                        </label>
                        <button class="btnActualizar" type="submit">Actualizar</button>
                    </form>
                </td>

                <td>
                    <!-- Formulario para Eliminar -->
                    <form method="POST" action="controlUsuarios.php" style="display:inline-block;"
                          onsubmit="return confirmarEliminar();">
                        <input type="hidden" name="id_usuario" value="<?= $row['id_usuario'] ?>">
                        <input type="hidden" name="accion" value="eliminar">
                        <button class="btnEliminar" type="submit" style="background:red; color:white;">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
</body>

<script>
    function toggleForm() {
        document.getElementById("formUsuario").classList.toggle("active");
    }

    function confirmarEliminar() {
        return confirm("⚠️ ¿Seguro que deseas eliminar este usuario?");
    }
</script>
</html>
