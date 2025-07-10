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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <img src="https://cdn-icons-png.flaticon.com/512/3309/3309960.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top me-2">
    <a class="navbar-brand" href="../index.php">Estadistica</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" aria-current="page" href="../ManejoPolinomios/index.php">Manejo de Polinomios</a></li>
        <li class="nav-item"><a class="nav-link active" href="../EstadisticaBasica/index.php">Estadistica Basica</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
  <h1 class="mb-5 text-center">Estadísticas Básicas (Media, Mediana, Moda)</h1>

  <?php if ($message !== ''): ?>
      <div class="alert alert-info" role="alert">
          <?= $message ?>
      </div>
  <?php endif; ?>

  <div class="card mb-4">
    <div class="card-header fw-bold">1) Agregar/Actualizar conjunto</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="action" value="add">
        <div class="mb-3">
          <label class="form-label">Identificador:</label>
          <input type="text" name="id" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Valores (separados por comas):</label>
          <input type="text" name="values" class="form-control" placeholder="e.g. 1, 2, 2, 3">
        </div>
        <button type="submit" class="btn btn-primary">Guardar conjunto</button>
      </form>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header fw-bold">2) Calcular estadísticas de un conjunto</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="action" value="single">
        <div class="mb-3">
          <label class="form-label">Selecciona conjunto:</label>
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

  <?php if (is_array($singleStat) && $singleId !== null): ?>
    <div class="alert alert-secondary">
      <h5>Resultados para «<?= htmlspecialchars($singleId, ENT_QUOTES, 'UTF-8') ?>»</h5>
      <p><strong>Valores:</strong> <?= implode(', ', $dataSets[$singleId]) ?></p>
      <p><strong>Media:</strong>   <?= $singleStat['media']   ?? '—' ?></p>
      <p><strong>Mediana:</strong> <?= $singleStat['mediana'] ?? '—' ?></p>
      <p><strong>Moda:</strong>
        <?= empty($singleStat['moda']) ? '—' : implode(', ', $singleStat['moda']) ?>
      </p>
    </div>
  <?php endif; ?>

  <div class="card mb-4">
    <div class="card-header fw-bold">3) Generar informe completo</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="action" value="full">
        <button type="submit" class="btn btn-info text-white">Generar informe</button>
      </form>
    </div>
  </div>

  <?php if (is_array($fullReport)): ?>
    <h3 class="mt-5 mb-3">Informe Completo</h3>
    <div class="table-responsive">
      <table class="table table-bordered align-middle text-center">
        <thead class="table-dark">
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
    </div>
  <?php endif; ?>
</div>


</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</html>
