<?php
declare(strict_types=1);
require_once __DIR__ . '/clases/Polinomio.php';


$resultEval  = null;
$resultDeriv = null;
$resultSum   = null;
$msgError    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'eval':
                $g = $_POST['eval_grados'] ?? [];
                $c = $_POST['eval_coefs']  ?? [];
                $t = [];
                foreach ($g as $i=>$grado) {
                    $t[(int)$grado] = (float)($c[$i] ?? 0);
                }
                $p = new Polinomio($t);
                $x = (float)($_POST['eval_x'] ?? 0);
                $resultEval = ['poly'=>(string)$p,'x'=>$x,'y'=>$p->evaluar($x)];
                break;

            case 'deriv':
                $g = $_POST['deriv_grados'] ?? [];
                $c = $_POST['deriv_coefs']  ?? [];
                $t = [];
                foreach ($g as $i=>$grado) {
                    $t[(int)$grado] = (float)($c[$i] ?? 0);
                }
                $p  = new Polinomio($t);
                $pd = $p->derivada();
                $resultDeriv = ['orig'=>(string)$p,'der'=>(string)$pd];
                break;

            case 'sum':
                // P
                $g1 = $_POST['sum1_grados'] ?? [];
                $c1 = $_POST['sum1_coefs']  ?? [];
                $t1 = [];
                foreach ($g1 as $i=>$grado) {
                    $t1[(int)$grado] = (float)($c1[$i] ?? 0);
                }
                // Q
                $g2 = $_POST['sum2_grados'] ?? [];
                $c2 = $_POST['sum2_coefs']  ?? [];
                $t2 = [];
                foreach ($g2 as $i=>$grado) {
                    $t2[(int)$grado] = (float)($c2[$i] ?? 0);
                }
                $sumT = Polinomio::sumarPolinomios($t1,$t2);
                $p1   = new Polinomio($t1);
                $p2   = new Polinomio($t2);
                $ps   = new Polinomio($sumT);
                $resultSum = ['p1'=>(string)$p1,'p2'=>(string)$p2,'sum'=>(string)$ps];
                break;

            default:
                throw new \Exception('Acción no reconocida');
        }
    } catch (\Throwable $e) {
        $msgError = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Manejo de Polinomios</title>
  <!-- CSS Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .term-table th, .term-table td { vertical-align: middle; }
    .term-table .btn-xs { padding: 2px 6px; }
  </style>
</head>
<body class="bg-light">

<!-- Navbar de Bootstrap -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <img src="https://cdn-icons-png.flaticon.com/512/5988/5988597.png" alt="Logo" width="35" height="35" class="d-inline-block align-text-top me-2">
    <a class="navbar-brand" href="../index.php">Polinomios</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="../ManejoPolinomios/index.php">Manejo de Polinomios</a></li>
        <li class="nav-item"><a class="nav-link" aria-current="page" href="../EstadisticaBasica/index.php">Estadistica Basica</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
  <h1 class="mb-5 text-center">Manejo de Polinomios</h1>

  <?php if ($msgError): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($msgError, ENT_QUOTES, 'UTF-8') ?></div>
  <?php endif; ?>

  <div class="card mb-4">
    <div class="card-header fw-bold">1) Evaluar polinomio</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="action" value="eval">
        <table class="table table-bordered term-table mb-3">
          <thead>
            <tr><th>Grado</th><th>Coeficiente</th><th>Acción</th></tr>
          </thead>
          <tbody id="eval-rows">
            <tr class="term-row">
              <td>
                <select name="eval_grados[]" class="form-control">
                  <option value="">Seleccionar Grado</option>
                  <?php for($i=0;$i<=10;$i++): ?>
                    <option value="<?=$i?>"><?=$i?></option>
                  <?php endfor; ?>
                </select>
              </td>
              <td><input type="number" step="any" name="eval_coefs[]" class="form-control" value=""></td>
              <td class="text-center">
                <button type="button" class="btn btn-danger btn-xs remove-term">X</button>
              </td>
            </tr>
          </tbody>
        </table>
        <button type="button" class="btn btn-outline-secondary mb-3" id="eval-add-btn">Añadir término</button>

        <div class="mb-3">
          <label class="form-label">Valor de x:</label>
          <input type="number" step="any" name="eval_x" class="form-control" value="" required>
        </div>
        <button type="submit" class="btn btn-primary">Evaluar</button>
      </form>
    </div>
  </div>

  <?php if ($resultEval): ?>
    <div class="alert alert-secondary">
      <p><strong>P(x) = <?= htmlspecialchars($resultEval['poly'], ENT_QUOTES, 'UTF-8') ?></strong></p>
      <p>Para x = <?= $resultEval['x'] ?> → <strong>y = <?= $resultEval['y'] ?></strong></p>
    </div>
  <?php endif; ?>

  <div class="card mb-4">
    <div class="card-header fw-bold">2) Derivar polinomio</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="action" value="deriv">
        <table class="table table-bordered term-table mb-3">
          <thead>
            <tr><th>Grado</th><th>Coeficiente</th><th>Acción</th></tr>
          </thead>
          <tbody id="deriv-rows">
            <tr class="term-row">
              <td>
                <select name="deriv_grados[]" class="form-control">
                  <option value="">Seleccionar Grado</option>
                  <?php for($i=0;$i<=10;$i++): ?>
                    <option value="<?=$i?>"><?=$i?></option>
                  <?php endfor; ?>
                </select>
              </td>
              <td><input type="number" step="any" name="deriv_coefs[]" class="form-control" value=""></td>
              <td class="text-center">
                <button type="button" class="btn btn-danger btn-xs remove-term">X</button>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="d-flex flex-column align-items-start gap-2 mt-3">
            <button type="button" class="btn btn-outline-secondary" id="deriv-add-btn">Añadir término</button>
            <button type="submit" class="btn btn-success">Derivar</button>
        </div>
      </form>
    </div>
  </div>

  <?php if ($resultDeriv): ?>
    <div class="alert alert-secondary">
      <p><strong>P(x) = <?= htmlspecialchars($resultDeriv['orig'], ENT_QUOTES, 'UTF-8') ?></strong></p>
      <p><strong>P′(x) = <?= htmlspecialchars($resultDeriv['der'], ENT_QUOTES, 'UTF-8') ?></strong></p>
    </div>
  <?php endif; ?>

  <div class="card mb-4">
    <div class="card-header fw-bold">3) Sumar polinomios</div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="action" value="sum">

        <h5>Polinomio P</h5>
        <table class="table table-bordered term-table mb-3">
          <thead>
            <tr><th>Grado</th><th>Coeficiente</th><th>Acción</th></tr>
          </thead>
          <tbody id="sum1-rows">
            <tr class="term-row">
              <td>
                <select name="sum1_grados[]" class="form-control">
                  <option value="">Seleccionar grado</option>
                  <?php for($i=0;$i<=10;$i++): ?>
                    <option value="<?=$i?>"><?=$i?></option>
                  <?php endfor; ?>
                </select>
              </td>
              <td><input type="number" step="any" name="sum1_coefs[]" class="form-control" value=""></td>
              <td class="text-center">
                <button type="button" class="btn btn-danger btn-xs remove-term">X</button>
              </td>
            </tr>
          </tbody>
        </table>
        <button type="button" class="btn btn-outline-secondary mb-4" id="sum1-add-btn">Añadir término P</button>

        <h5>Polinomio Q</h5>
        <table class="table table-bordered term-table mb-3">
          <thead>
            <tr><th>Grado</th><th>Coeficiente</th><th>Acción</th></tr>
          </thead>
          <tbody id="sum2-rows">
            <tr class="term-row">
              <td>
                <select name="sum2_grados[]" class="form-control">
                  <option value="">Seleccionar grado</option>
                  <?php for($i=0;$i<=10;$i++): ?>
                    <option value="<?=$i?>"><?=$i?></option>
                  <?php endfor; ?>
                </select>
              </td>
              <td><input type="number" step="any" name="sum2_coefs[]" class="form-control" value=""></td>
              <td class="text-center">
                <button type="button" class="btn btn-danger btn-xs remove-term">X</button>
              </td>
            </tr>
          </tbody>
        </table>
        <button type="button" class="btn btn-outline-secondary mb-3" id="sum2-add-btn">Añadir término Q</button>
        <br>
        <button type="submit" class="btn btn-info text-white">Sumar</button>
      </form>
    </div>
  </div>

  <?php if ($resultSum): ?>
    <div class="alert alert-secondary">
      <p><strong>P(x) = <?= htmlspecialchars($resultSum['p1'], ENT_QUOTES, 'UTF-8') ?></strong></p>
      <p><strong>Q(x) = <?= htmlspecialchars($resultSum['p2'], ENT_QUOTES, 'UTF-8') ?></strong></p>
      <p><strong>P + Q = <?= htmlspecialchars($resultSum['sum'], ENT_QUOTES, 'UTF-8') ?></strong></p>
    </div>
  <?php endif; ?>
</div>

<script>
  function setupDynamicTable(section) {
    const tbody  = document.getElementById(section + '-rows');
    const addBtn = document.getElementById(section + '-add-btn');

    function updateAvailableGrades() {
      const rows = tbody.querySelectorAll('.term-row');
      const usedGrades = new Set();
      
      rows.forEach(row => {
        const select = row.querySelector('select');
        if (select.value !== '') {
          usedGrades.add(parseInt(select.value));
        }
      });

      rows.forEach(row => {
        const select = row.querySelector('select');
        const currentValue = select.value;
        
        select.innerHTML = '';
        
        const emptyOption = document.createElement('option');
        emptyOption.value = '';
        emptyOption.textContent = 'Seleccionar grado';
        select.appendChild(emptyOption);
        
        for (let i = 0; i <= 10; i++) {
          if (!usedGrades.has(i) || i.toString() === currentValue) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            select.appendChild(option);
          }
        }
        
        if (currentValue !== '') {
          select.value = currentValue;
        }
      });

      const availableGrades = [];
      for (let i = 0; i <= 10; i++) {
        if (!usedGrades.has(i)) {
          availableGrades.push(i);
        }
      }
      
      addBtn.disabled = availableGrades.length === 0;
    }

    addBtn.addEventListener('click', () => {
      const tpl   = tbody.querySelector('.term-row');
      const clone = tpl.cloneNode(true);
      clone.querySelector('input').value = '';
      clone.querySelector('select').selectedIndex = 0;
      tbody.appendChild(clone);
      
      updateAvailableGrades();
    });

    tbody.addEventListener('click', e => {
      if (e.target.classList.contains('remove-term')) {
        const rows = tbody.querySelectorAll('.term-row');
        if (rows.length > 1) {
          e.target.closest('.term-row').remove();
          updateAvailableGrades();
        }
      }
    });

    tbody.addEventListener('change', e => {
      if (e.target.tagName === 'SELECT') {
        updateAvailableGrades();
      }
    });

    updateAvailableGrades();
  }

  document.addEventListener('DOMContentLoaded', () => {
    ['eval','deriv','sum1','sum2'].forEach(setupDynamicTable);
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>