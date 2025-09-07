<?php
session_start();
include '../BackEnd/conexion.php'; // mysqli en $enlace

if (!isset($_SESSION['usuario'])) {
    header("Location: ../Login/Login.html");
    exit();
}

date_default_timezone_set('America/Lima');
// Opcional: forzar zona horaria MySQL
$enlace->query("SET time_zone='-05:00'");

// --- FILTROS ---
$filtro_pago  = $_GET['metodo'] ?? '';
$filtro_fecha = $_GET['fecha'] ?? date('Y-m-d');
$rango        = $_GET['rango'] ?? 'dia';

// Validaciones
$fecha_valida = DateTime::createFromFormat('Y-m-d', $filtro_fecha);
if (!$fecha_valida) $filtro_fecha = date('Y-m-d');

$metodos_validos = ["EFECTIVO", "YAPE", "TARJETA"];
if ($filtro_pago !== '' && !in_array($filtro_pago, $metodos_validos, true)) $filtro_pago = '';

/** Helpers **/
function bind_params_ref(mysqli_stmt $stmt, string $types, array $params): void {
    if ($types === '') return;
    $refs = [];
    foreach ($params as $k => $v) { $refs[$k] = &$params[$k]; }
    array_unshift($refs, $types);
    call_user_func_array([$stmt, 'bind_param'], $refs);
}

function rangoFechas(string $rango, string $fechaBase): array {
    $base = new DateTimeImmutable($fechaBase . ' 00:00:00');
    switch ($rango) {
        case 'semana':
            // ISO: lunes a domingo
            $start = $base->modify('monday this week');
            $end   = $start->modify('+1 week');
            break;
        case 'mes':
            $start = $base->modify('first day of this month');
            $end   = $start->modify('first day of next month');
            break;
        case 'todo':
            $start = new DateTimeImmutable('1970-01-01 00:00:00');
            $end   = new DateTimeImmutable('2999-12-31 23:59:59');
            break;
        default: // dia
            $start = $base;
            $end   = $base->modify('+1 day');
            break;
    }
    return [$start, $end];
}

function buildFiltroSQL(string $rango, string $fechaBase, string $metodo = ''): array {
    [$start, $end] = rangoFechas($rango, $fechaBase);
    $sql  = "pa.fecha_hora >= ? AND pa.fecha_hora < ?";
    $types = "ss";
    $params = [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')];

    if ($metodo !== '') {
        $sql .= " AND pa.metodo = ?";
        $types .= "s";
        $params[] = $metodo;
    }
    return [$sql, $types, $params, $start, $end];
}

[$condicion, $param_tipo, $param_valor, $desdeDT, $hastaDT] = buildFiltroSQL($rango, $filtro_fecha, $filtro_pago);

// --- INDICADORES (por defecto: hoy desde 8am) ---
$hoy = (new DateTimeImmutable('today'))->format('Y-m-d');
$sql_indicadores = "
    SELECT SUM(pa.monto) AS total, COUNT(pa.id_pago) AS num_ventas
    FROM pago pa
    WHERE pa.fecha_hora >= CONCAT(?, ' 08:00:00') AND pa.fecha_hora < CONCAT(?, ' 23:59:59')
";
$stmt_ind = $enlace->prepare($sql_indicadores);
bind_params_ref($stmt_ind, "ss", [$hoy, $hoy]);
$stmt_ind->execute();
$indicadores = $stmt_ind->get_result()->fetch_assoc() ?: ['total'=>0,'num_ventas'=>0];

// --- DETALLE ---
$sql_dia = "
    SELECT pa.id_pago, c.tipo, pa.metodo, pa.monto, pa.fecha_hora, u.nombre
    FROM pago pa
    JOIN comprobante c ON pa.id_comprobante = c.id_comprobante
    JOIN usuario u ON pa.id_usuario = u.id_usuario
    WHERE $condicion
    ORDER BY pa.fecha_hora DESC
";
$stmt = $enlace->prepare($sql_dia);
bind_params_ref($stmt, $param_tipo, $param_valor);
$stmt->execute();
$res_dia = $stmt->get_result();

// --- TOTALES ---
$sql_total = "
    SELECT SUM(pa.monto) AS total, COUNT(pa.id_pago) AS num_ventas
    FROM pago pa
    WHERE $condicion
";
$stmt_total = $enlace->prepare($sql_total);
bind_params_ref($stmt_total, $param_tipo, $param_valor);
$stmt_total->execute();
$total = $stmt_total->get_result()->fetch_assoc() ?: ['total'=>0,'num_ventas'=>0];

// --- Texto rango con Intl ---
$fmtDia   = new IntlDateFormatter('es_PE', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
$fmtMes   = new IntlDateFormatter('es_PE', IntlDateFormatter::LONG, IntlDateFormatter::NONE, null, null, "LLLL 'de' y");
$texto_rango = '';
switch ($rango) {
    case 'semana':
        $texto_rango = "Semana del " . ucfirst($fmtDia->format($desdeDT)) . " al " . ucfirst($fmtDia->format($hastaDT->modify('-1 second')));
        break;
    case 'mes':
        $texto_rango = "Mes de " . ucfirst($fmtMes->format($desdeDT));
        break;
    case 'todo':
        $texto_rango = "Todo el historial de ventas";
        break;
    default:
        $texto_rango = "Día " . ucfirst($fmtDia->format($desdeDT));
        break;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes</title>
    <link href="https://fonts.googleapis.com/css2?family=Dosis:wght@200..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../dashboard/header.css">
    <link rel="stylesheet" href="Styles/stylesReportes.css">
</head>
<body>
<?php include '../dashboard/header.php'; ?>

<main class="contenedor">
    <!-- FILTROS -->
    <section class="filtros">
        <form method="get">
            <label>Fecha:
                <input type="date" name="fecha" value="<?= htmlspecialchars($filtro_fecha) ?>">
            </label>

            <label>Rango:
                <select name="rango">
                    <option value="dia" <?= ($rango ?? '')=="dia"?"selected":"" ?>>Día</option>
                    <option value="semana" <?= ($rango ?? '')=="semana"?"selected":"" ?>>Semana</option>
                    <option value="mes" <?= ($rango ?? '')=="mes"?"selected":"" ?>>Mes</option>
                    <option value="todo" <?= ($rango ?? '')=="todo"?"selected":"" ?>>Todo</option>
                </select>
            </label>

            <label>Método de pago:
                <select name="metodo">
                    <option value="">Todos</option>
                    <option value="EFECTIVO" <?= $filtro_pago=="EFECTIVO"?"selected":"" ?>>Efectivo</option>
                    <option value="YAPE" <?= $filtro_pago=="YAPE"?"selected":"" ?>>Yape</option>
                    <option value="TARJETA" <?= $filtro_pago=="TARJETA"?"selected":"" ?>>Tarjeta</option>
                </select>
            </label>

            <button type="submit">Filtrar</button>
        </form>
    </section>

    <!-- INDICADORES (ventas del día desde 8am) -->
    <section class="cards">
        <div class="card">
            <h3>💰 Ganancia Día</h3>
            <p>S/ <?= number_format($indicadores['total'] ?? 0, 2) ?></p>
        </div>
        <div class="card">
            <h3>🛒 Ventas Día</h3>
            <p><?= $indicadores['num_ventas'] ?? 0 ?></p>
        </div>
    </section>

    <!-- RESULTADOS (según filtro) -->
    <h3>Resumen (<?= $texto_rango ?>)</h3>
    <p>Total ventas: <?= $total['num_ventas'] ?? 0 ?></p>
    <p>Monto total: S/ <?= number_format($total['total'] ?? 0, 2) ?></p>

    <!-- TABLA VENTAS -->
    <section class="tabla">
        <h2>Ventas del <?= htmlspecialchars($filtro_fecha) ?></h2>
        <div class="tabla-scroll">
            <table>
                <thead>
                <tr>
                    <th>ID Pago</th>
                    <th>Tipo</th>
                    <th>Método</th>
                    <th>Monto</th>
                    <th>Hora</th>
                    <th>Usuario</th>
                </tr>
                </thead>
                <tbody>
                <?php while($row = $res_dia->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id_pago']) ?></td>
                        <td><?= htmlspecialchars($row['tipo']) ?></td>
                        <td><?= htmlspecialchars($row['metodo']) ?></td>
                        <td>S/ <?= number_format($row['monto'], 2) ?></td>
                        <td><?= date('H:i', strtotime($row['fecha_hora'])) ?></td>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>

</main>
</body>
</html>
