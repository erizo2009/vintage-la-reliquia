<?php
session_start();

// Categorías: nombre que se ve, archivo de imagen (carpeta img/) y filtro para productos.php
$categorias = [
  ['Comedor',               'comedor.png',     'comedor'],
  ['Dormitorio',            'dormitorio.png',  'dormitorio'],
  ['Living',                'living.png',      'living'],
  ['Vitrinas y guardado',   'vitrinas.png',    'vitrinas'],
  ['Escritorios y ficheros','escritorios.png', 'escritorios'],
  ['Iluminación',           'iluminacion.png', 'iluminacion'],
];

// Cambiá 'usuario' por la variable de sesión que uses al iniciar sesión
$logueado = isset($_SESSION['usuario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>La Reliquia - Antigüedades</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Almendra:wght@400;700&family=Montserrat:wght@800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="estilos.css">
</head>
<body>

<header class="topbar">
  <form class="buscador" action="productos.php" method="get" role="search">
    <input type="search" name="q" placeholder="Buscar" aria-label="Buscar">
    <button type="submit" aria-label="Buscar">
      <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
        <circle cx="10.5" cy="10.5" r="6.5"/><path d="M15.5 15.5 21 21"/>
      </svg>
    </button>
  </form>

  <a class="logo" href="inicio.php">
    <img src="img/logo.png" alt="La Reliquia antigüedades">
  </a>

  <div class="acciones">
    <?php if ($logueado): ?>
      <a href="cerrar_sesion.php">Cerrar sesión</a>
    <?php else: ?>
      <a href="registro.php">Crear cuenta</a>
      <span class="sep" aria-hidden="true"></span>
      <a href="login.php">Iniciar sesión</a>
    <?php endif; ?>
    <a class="carrito" href="carrito.php" aria-label="Carrito">
      <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
        <path d="M2 3h3l2.6 12.2a1 1 0 0 0 1 .8h9.4a1 1 0 0 0 1-.8L21 7H6"/>
        <circle cx="9.5" cy="20" r="1.4"/><circle cx="17" cy="20" r="1.4"/>
      </svg>
    </a>
  </div>
</header>

<nav class="menu" aria-label="Principal">
  <a href="inicio.php">Inicio</a>
  <a href="productos.php">Productos</a>
  <a href="contacto.php">Contacto</a>
</nav>

<main>
  <form class="categorias" action="productos.php" method="get">
    <?php foreach ($categorias as [$nombre, $imagen, $filtro]): ?>
      <button class="tarjeta" type="submit" name="categoria" value="<?= htmlspecialchars($filtro) ?>">
        <img src="img/<?= htmlspecialchars($imagen) ?>" alt="">
        <span><?= htmlspecialchars($nombre) ?></span>
      </button>
    <?php endforeach; ?>
  </form>
</main>

</body>
</html>