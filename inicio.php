<?php
session_start();

// Categorías: nombre que se ve, archivo de imagen (carpeta img/) y filtro para productos.php
$categorias = [
  ['comedor.png',     'comedor'],
  ['dormitorio.png',  'dormitorio'],
  ['living.png',      'living'],
  ['vitrinas.png',    'vitrinas'],
  ['escritorios.png', 'escritorios'],
  ['iluminacion.png', 'iluminacion'],
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
  <style>
:root {
  --bordo: #4d2320;
  --dorado: #deb94f;
  --rojo-logo: #8f0b0b;
  --gris: #b1b1b1;
  --deco: "Almendra", Georgia, serif;
  --titulo: "Montserrat", Arial, sans-serif;
}

*, *::before, *::after { box-sizing: border-box; }

body {
  margin: 0;
  background: #fff;
  font-family: var(--deco);
  color: var(--dorado);
}

a { color: inherit; text-decoration: none; }

/* ---------- Encabezado ---------- */
.topbar {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  height: 118px;
  padding: 0 40px;
  background: var(--bordo);
}

/* buscador con subrayado dorado */
.buscador {
  display: flex;
  align-items: center;
  width: 100%;
  max-width: 262px;
  border-bottom: 2px solid var(--dorado);
}
.buscador input {
  flex: 1;
  min-width: 0;
  padding: 8px 4px 4px 18px;
  border: 0;
  outline: 0;
  background: transparent;
  font: inherit;
  font-size: 1.7rem;
  color: var(--dorado);
}
.buscador input::placeholder { color: var(--dorado); opacity: 1; }
.buscador button {
  padding: 0 4px 2px;
  border: 0;
  background: transparent;
  color: var(--dorado);
  cursor: pointer;
}

/* logo */
.logo { display: block; justify-self: center; }
.logo img { display: block; height: 110px; width: auto; }

/* crear cuenta | iniciar sesión + carrito */
.acciones {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 18px;
  font-size: 1.7rem;
}
.acciones a { transition: opacity .2s; }
.acciones a:hover { opacity: .75; }
.acciones .sep {
  width: 2px;
  height: 36px;
  background: var(--dorado);
}
.carrito { display: grid; place-items: center; margin-left: 28px; }

.logo-texto { font-size: 3rem; color: var(--rojo-logo); white-space: nowrap; }
.logo-texto small { font-size: 1rem; color: #111; }

/* ---------- Menú ---------- */
.menu {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  max-width: 931px;
  margin: 0 auto;
  padding: 6px 0 14px;
  margin-bottom: 24px;
  text-align: center;
  font-size: 2.4rem;
  line-height: 1.2;
}
.menu a:hover { text-decoration: underline; text-underline-offset: 6px; }

/* ---------- Grilla de categorías ---------- */
.categorias {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 22px 24px;
  max-width: 931px;
  margin: 0 auto;
  padding-bottom: 40px;
}

.tarjeta {
  /* reset de <button> */
  appearance: none;
  border: 0;
  padding: 0;
  margin: 0;
  font: inherit;
  cursor: pointer;
  width: 100%;
  position: relative;
  display: grid;
  place-items: center;
  aspect-ratio: 295 / 256;
  overflow: hidden;
  background: var(--gris);
  box-shadow: 0 1px 4px rgba(0, 0, 0, .25);
}

.tarjeta img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
  padding: 12px;
  transition: transform .3s;
}
/* velo gris suave sobre la foto, como en el diseño */
.tarjeta::after {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(120, 120, 120, .22);
}

.tarjeta span {
  position: relative;
  z-index: 1;
  padding: 0 14px;
  font-family: var(--titulo);
  font-weight: 800;
  font-size: 1.75rem;
  line-height: 1.05;
  text-align: center;
  text-transform: uppercase;
  color: #fff;
  text-shadow: 0 1px 4px rgba(0, 0, 0, .55);
}

.tarjeta:hover img { transform: scale(1.05); }
.tarjeta:active { transform: translateY(1px); }
.tarjeta:focus-visible { outline: 3px solid var(--dorado); outline-offset: 3px; }

/* ---------- Pantallas chicas ---------- */
@media (max-width: 900px) {
  .topbar { grid-template-columns: 1fr; height: auto; gap: 12px; padding: 14px 16px; justify-items: center; }
  .buscador { max-width: 320px; }
  .acciones { justify-content: center; font-size: 1.3rem; }
  .logo img { height: 80px; }
  .menu { font-size: 1.6rem; padding: 12px 8px; gap: 8px; }
  .categorias { padding: 0 12px 28px; gap: 12px; grid-template-columns: repeat(2, 1fr); }
  .tarjeta span { font-size: 1.2rem; }
}

</style>
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
    <?php if (file_exists(__DIR__ . '/img/logo.png')): ?>
      <img src="img/logo.png" alt="La Reliquia antigüedades">
    <?php else: ?>
      <span class="logo-texto">La Reliquia <small>antigüedades</small></span>
    <?php endif; ?>
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
        <?php if (file_exists(__DIR__ . '/img/' . $imagen)): ?>
          <img src="img/<?= htmlspecialchars($imagen) ?>" alt="">
        <?php endif; ?>
        <span><?= htmlspecialchars($nombre) ?></span>
      </button>
    <?php endforeach; ?>
  </form>
</main>

</body>
</html>