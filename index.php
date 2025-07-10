<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Proyecto Final</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body {
      height: 100%;
    }
    body {
      background-image: url('https://wallpapers.com/images/hd/mathematics-background-zwq7mm3o4j8bzk6i.jpg');
      background-size: cover;
      background-position: center;
      background-color: rgba(255, 255, 255, 0.5);
      background-blend-mode: overlay;
      display: flex;
      flex-direction: column;
    }
    .content {
      flex: 1;
    }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Inicio</a>
    </div>
  </nav>

  <div class="content py-5">
    <div class="container py-5">
      <div class="text-center mb-4">
        <h1>Proyecto Final</h1>
        <h2 class="text-muted">Selecciona un ejercicio para continuar</h2>
      </div>

      <div class="row justify-content-center g-4">
        <div class="col-md-5">
          <div class="card shadow">
            <div class="card-body text-center">
              <h5 class="card-title">Ejercicio Polinomios</h5>
              <p class="card-text">Manejo de polinomios.</p>
              <a href="ManejoPolinomios/index.php" class="btn btn-primary">Ir al ejercicio</a>
            </div>
          </div>
        </div>

        <div class="col-md-5">
          <div class="card shadow">
            <div class="card-body text-center">
              <h5 class="card-title">Ejercicio Estadistica</h5>
              <p class="card-text">Estadistica Basica</p>
              <a href="EstadisticaBasica/index.php" class="btn btn-primary">Ir al ejercicio</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<footer class="bg-dark text-white text-center py-4">
  <div class="container">
    <p class="mb-1">© 2025 Proyecto Final - Todos los derechos reservados</p>
  </div>
</footer>
</body>

</html>

