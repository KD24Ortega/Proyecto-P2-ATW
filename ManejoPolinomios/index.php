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
  <title>Polinomios – Tabla dinámica</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <style>
    .term-table th, .term-table td { vertical-align: middle; }
    .term-table .btn-xs { padding: 2px 6px; }
  </style>
</head>
<body class="bg-light">
<div class="container py-5">
  <h1>Manejo de Polinomios</h1>

  <?php if ($msgError): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($msgError,ENT_QUOTES,'UTF-8') ?></div>
  <?php endif; ?>

  <!-- 1) Evaluar -->
  <div class="panel panel-default">
    <div class="panel-heading"><strong>1) Evaluar polinomio</strong></div>
    <div class="panel-body">
      <form method="POST">
        <input type="hidden" name="action" value="eval">

        <table class="table table-bordered term-table">
          <thead><tr><th>Grado</th><th>Coeficiente</th><th>Acción</th></thead>
          <tbody id="eval-rows">
            <tr class="term-row">
              <td>
                <select name="eval_grados[]" class="form-control">
                  <?php for($i=0;$i<=10;$i++): ?>
                    <option value="<?=$i?>"><?=$i?></option>
                  <?php endfor; ?>
                </select>
              </td>
              <td><input type="number" step="any" name="eval_coefs[]" class="form-control" value="0"></td>
              <td class="text-center">
                <button type="button" class="btn btn-danger btn-xs remove-term">×</button>
              </td>
            </tr>
          </tbody>
        </table>
        <button type="button" class="btn btn-default" id="eval-add-btn">Añadir término</button>

        <div class="form-group" style="margin-top:1em;">
          <label>Valor de x:</label>
          <input type="number" step="any" name="eval_x" class="form-control" value="0" required>
        </div>
        <button type="submit" class="btn btn-primary">Evaluar</button>
      </form>
    </div>
  </div>
  <?php if ($resultEval): ?>
    <div class="well">
      <p><strong>P(x) = <?= htmlspecialchars($resultEval['poly'],ENT_QUOTES,'UTF-8') ?></strong></p>
      <p>Para x = <?= $resultEval['x'] ?> → <strong>y = <?= $resultEval['y'] ?></strong></p>
    </div>
  <?php endif; ?>


  <!-- 2) Derivar -->
  <div class="panel panel-default">
    <div class="panel-heading"><strong>2) Derivar polinomio</strong></div>
    <div class="panel-body">
      <form method="POST">
        <input type="hidden" name="action" value="deriv">

        <table class="table table-bordered term-table">
          <thead><tr><th>Grado</th><th>Coeficiente</th><th>Acción</th></thead>
          <tbody id="deriv-rows">
            <tr class="term-row">
              <td>
                <select name="deriv_grados[]" class="form-control">
                  <?php for($i=0;$i<=10;$i++): ?>
                    <option value="<?=$i?>"><?=$i?></option>
                  <?php endfor; ?>
                </select>
              </td>
              <td><input type="number" step="any" name="deriv_coefs[]" class="form-control" value="0"></td>
              <td class="text-center">
                <button type="button" class="btn btn-danger btn-xs remove-term">×</button>
              </td>
            </tr>
          </tbody>
        </table>
        <button type="button" class="btn btn-default" id="deriv-add-btn">Añadir término</button>
        <button type="submit" class="btn btn-success" style="margin-top:1em;">Derivar</button>
      </form>
    </div>
  </div>
  <?php if ($resultDeriv): ?>
    <div class="well">
      <p><strong>P(x) = <?= htmlspecialchars($resultDeriv['orig'],ENT_QUOTES,'UTF-8') ?></strong></p>
      <p><strong>P′(x) = <?= htmlspecialchars($resultDeriv['der'],ENT_QUOTES,'UTF-8') ?></strong></p>
    </div>
  <?php endif; ?>


  <!-- 3) Sumar -->
  <div class="panel panel-default">
    <div class="panel-heading"><strong>3) Sumar polinomios</strong></div>
    <div class="panel-body">
      <form method="POST">
        <input type="hidden" name="action" value="sum">

        <h5>Polinomio P</h5>
        <table class="table table-bordered term-table">
          <thead><tr><th>Grado</th><th>Coeficiente</th><th>Acción</th></thead>
          <tbody id="sum1-rows">
            <tr class="term-row">
              <td>
                <select name="sum1_grados[]" class="form-control">
                  <?php for($i=0;$i<=10;$i++): ?>
                    <option value="<?=$i?>"><?=$i?></option>
                  <?php endfor; ?>
                </select>
              </td>
              <td><input type="number" step="any" name="sum1_coefs[]" class="form-control" value="0"></td>
              <td class="text-center">
                <button type="button" class="btn btn-danger btn-xs remove-term">×</button>
              </td>
            </tr>
          </tbody>
        </table>
        <button type="button" class="btn btn-default" id="sum1-add-btn">Añadir término P</button>

        <h5 style="margin-top:1em;">Polinomio Q</h5>
        <table class="table table-bordered term-table">
          <thead><tr><th>Grado</th><th>Coeficiente</th><th>Acción</th></thead>
          <tbody id="sum2-rows">
            <tr class="term-row">
              <td>
                <select name="sum2_grados[]" class="form-control">
                  <?php for($i=0;$i<=10;$i++): ?>
                    <option value="<?=$i?>"><?=$i?></option>
                  <?php endfor; ?>
                </select>
              </td>
              <td><input type="number" step="any" name="sum2_coefs[]" class="form-control" value="0"></td>
              <td class="text-center">
                <button type="button" class="btn btn-danger btn-xs remove-term">×</button>
              </td>
            </tr>
          </tbody>
        </table>
        <button type="button" class="btn btn-default" id="sum2-add-btn">Añadir término Q</button>
        <br><br>
        <button type="submit" class="btn btn-info">Sumar</button>
      </form>
    </div>
  </div>
  <?php if ($resultSum): ?>
    <div class="well">
      <p><strong>P(x) = <?= htmlspecialchars($resultSum['p1'],ENT_QUOTES,'UTF-8') ?></strong></p>
      <p><strong>Q(x) = <?= htmlspecialchars($resultSum['p2'],ENT_QUOTES,'UTF-8') ?></strong></p>
      <p><strong>P + Q = <?= htmlspecialchars($resultSum['sum'],ENT_QUOTES,'UTF-8') ?></strong></p>
    </div>
  <?php endif; ?>

</div>

<script>
  function setupDynamicTable(section) {
    const tbody  = document.getElementById(section + '-rows');
    const addBtn = document.getElementById(section + '-add-btn');

    addBtn.addEventListener('click', () => {
      const tpl   = tbody.querySelector('.term-row');
      const clone = tpl.cloneNode(true);
      clone.querySelector('input').value = '';
      clone.querySelector('select').selectedIndex = 0;
      tbody.appendChild(clone);
    });

    tbody.addEventListener('click', e => {
      if (e.target.classList.contains('remove-term')) {
        const rows = tbody.querySelectorAll('.term-row');
        if (rows.length > 1) {
          e.target.closest('.term-row').remove();
        }
      }
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    ['eval','deriv','sum1','sum2'].forEach(setupDynamicTable);
  });
</script>
</body>
</html>
