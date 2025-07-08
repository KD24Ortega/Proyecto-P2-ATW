<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/clases/EstadisticaBasica.php';

$estad      = new EstadisticaBasica();
$dataSets   = &$_SESSION['dataSets'];
$message    = '';
$singleStat = null;
$singleId   = null;
$fullReport = null;

// Inicializar si no existe
if (!isset($dataSets)) {
    $dataSets = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        case 'add':
            $id   = trim((string)($_POST['id'] ?? ''));
            $raw  = trim((string)($_POST['values'] ?? ''));
            if ($id === '') {
                $message = 'El identificador no puede quedar vacío.';
                break;
            }
            $vals = array_filter(
                array_map('trim', explode(',', $raw)),
                fn($v) => $v !== ''
            );
            $dataSets[$id] = array_map('floatval', $vals);
            $message = "Conjunto «<strong>" . htmlspecialchars($id) . "</strong>» guardado con "
                     . count($dataSets[$id]) . " valores.";
            break;

        case 'single':
            $id = $_POST['dataset'] ?? '';
            if (!isset($dataSets[$id])) {
                $message = "El conjunto «<strong>" . htmlspecialchars($id) . "</strong>» no existe.";
            } else {
                $singleStat = $estad->generarInforme([$id => $dataSets[$id]])[$id];
                $singleId   = $id;
            }
            break;

        case 'full':
            if (empty($dataSets)) {
                $message = 'No hay conjuntos cargados para generar informe.';
            } else {
                $fullReport = $estad->generarInforme($dataSets);
            }
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas Básicas</title>
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <h1>Estadísticas Básicas (Media, Mediana, Moda)</h1>

    <?php if ($message !== ''): ?>
        <div class="alert alert-info" role="alert">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- Formulario: Agregar / Actualizar conjunto -->
    <div class="panel panel-default">
        <div class="panel-heading"><strong>1) Agregar/Actualizar conjunto</strong></div>
        <div class="panel-body">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label>Identificador:</label>
                    <input type="text" name="id" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Valores (separados por comas):</label>
                    <input type="text" name="values" class="form-control" placeholder="e.g. 1, 2, 2, 3">
                </div>
                <button type="submit" class="btn btn-primary">Guardar conjunto</button>
            </form>
        </div>
    </div>

    <!-- Formulario: Calcular estadísticas de un solo conjunto -->
    <div class="panel panel-default">
        <div class="panel-heading"><strong>2) Calcular estadísticas de un conjunto</strong></div>
        <div class="panel-body">
            <form method="POST">
                <input type="hidden" name="action" value="single">
                <div class="form-group">
                    <label>Selecciona conjunto:</label>
                    <select name="dataset" class="form-control" required>
                        <option value="">-- Elige uno --</option>
                        <?php foreach ($dataSets as $key => $_): ?>
                            <option value="<?= htmlspecialchars($key) ?>">
                                <?= htmlspecialchars($key) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Calcular</button>
            </form>
        </div>
    </div>

    <!-- Resultado de un solo conjunto -->
    <?php if (is_array($singleStat) && $singleId !== null): ?>
        <div class="well">
           <h4>Resultados para «<?= htmlspecialchars($singleId, ENT_QUOTES, 'UTF-8') ?>»</h4>
            <p><strong>Valores:</strong> <?= implode(', ', $dataSets[$singleId]) ?></p>
            <p><strong>Media:</strong>   <?= $singleStat['media']   ?? '—' ?></p>
            <p><strong>Mediana:</strong> <?= $singleStat['mediana'] ?? '—' ?></p>
            <p><strong>Moda:</strong>
                <?= empty($singleStat['moda']) ? '—' : implode(', ', $singleStat['moda']) ?>
            </p>
        </div>
    <?php endif; ?>

    <!-- Formulario: Informe completo -->
    <div class="panel panel-default">
        <div class="panel-heading"><strong>3) Generar informe completo</strong></div>
        <div class="panel-body">
            <form method="POST">
                <input type="hidden" name="action" value="full">
                <button type="submit" class="btn btn-info">Generar informe</button>
            </form>
        </div>
    </div>

    <!-- Informe completo -->
    <?php if (is_array($fullReport)): ?>
        <h3>Informe Completo</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Conjunto</th>
                    <th>Valores</th>
                    <th>Media</th>
                    <th>Mediana</th>
                    <th>Moda</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fullReport as $id => $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($id) ?></td>
                        <td><?= implode(', ', $dataSets[$id]) ?></td>
                        <td><?= $s['media']   ?? '—' ?></td>
                        <td><?= $s['mediana'] ?? '—' ?></td>
                        <td><?= empty($s['moda']) ? '—' : implode(', ', $s['moda']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>
</body>
</html>
