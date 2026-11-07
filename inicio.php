<?php
session_start();

// Categorías: nombre que se ve, foto (carpeta img/) y valor del filtro en la base de datos
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

function h($t) { return htmlspecialchars((string)$t, ENT_QUOTES, 'UTF-8'); }

function etiqueta_categoria($cat) {
  global $categorias;
  foreach ($categorias as [$nombre, $img, $filtro]) {
    if ($filtro === $cat) return $nombre;
  }
  return ucfirst($cat);
}

/* ---------- Las 6 tarjetas-botón ---------- */
function grilla_html() {
  global $categorias;
  ob_start(); ?>
  <form class="categorias" action="inicio.php" method="get">
    <?php foreach ($categorias as [$nombre, $imagen, $filtro]):
      $hayFoto = file_exists(__DIR__ . '/img/' . $imagen); ?>
      <button class="tarjeta<?= $hayFoto ? ' tiene-foto' : '' ?>" type="submit" name="categoria" value="<?= h($filtro) ?>"
        <?php if ($hayFoto): ?>style="background-image:url('img/<?= h($imagen) ?>')"<?php endif; ?>>
        <span><?= h($nombre) ?></span>
      </button>
    <?php endforeach; ?>
  </form>
  <?php
  return ob_get_clean();
}

/* ---------- Productos de una categoría o de una búsqueda ---------- */
function seccion_productos($categoria, $buscar) {
  try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = new mysqli('localhost', 'root', '', 'vintage');   // usuario, contraseña y base de XAMPP
    $db->set_charset('utf8mb4');

    $sql    = 'SELECT id, nombre, descripcion, precio, imagen FROM productos WHERE 1=1';
    $tipos  = '';
    $params = [];
    if ($categoria !== '' && $categoria !== 'todos') { $sql .= ' AND categoria = ?'; $tipos .= 's'; $params[] = $categoria; }
    if ($buscar !== '') { $sql .= ' AND (nombre LIKE ? OR descripcion LIKE ?)'; $tipos .= 'ss'; $params[] = "%$buscar%"; $params[] = "%$buscar%"; }
    $sql .= ' ORDER BY id DESC';

    $stmt = $db->prepare($sql);
    if ($params) $stmt->bind_param($tipos, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
    $filas = $res->fetch_all(MYSQLI_ASSOC);
  } catch (Throwable $e) {
    $filas = null;
  }

  if ($buscar !== '')            $titulo = 'Resultados para “' . h($buscar) . '”';
  elseif ($categoria === 'todos') $titulo = 'Todos los productos';
  else                            $titulo = h(etiqueta_categoria($categoria));

  ob_start(); ?>
  <section class="lista-productos">
    <?php if ($categoria !== 'todos' || $buscar !== ''): ?>
      <button type="button" class="volver" data-volver>← Volver a las categorías</button>
      <h1 class="titulo-seccion"><?= $titulo ?></h1>
    <?php endif; ?>

    <?php if ($filas === null): ?>
      <p class="vacio">No se pudieron cargar los productos. Revisá que MySQL esté iniciado y que exista la tabla <b>productos</b>.</p>
    <?php elseif (!$filas): ?>
      <p class="vacio">No se encontraron productos.</p>
    <?php else: ?>
      <div class="grilla-productos">
        <?php foreach ($filas as $p): ?>
          <article class="producto">
            <div class="foto">
              <?php if ($p['imagen'] && file_exists(__DIR__ . '/img/productos/' . $p['imagen'])): ?>
                <img src="img/productos/<?= h($p['imagen']) ?>" alt="<?= h($p['nombre']) ?>">
              <?php endif; ?>
            </div>
            <h2><?= h($p['nombre']) ?></h2>
            <p class="desc"><?= h($p['descripcion']) ?></p>
            <p class="precio">$<?= number_format((float)$p['precio'], 0, ',', '.') ?> ars</p>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
  <?php
  return ob_get_clean();
}

/* ---------- Qué mostrar ---------- */
$categoria = trim($_GET['categoria'] ?? '');
$buscar    = trim($_GET['q'] ?? '');
$hayFiltro = ($categoria !== '' || $buscar !== '');

// Pedido desde JavaScript: devolvemos solo el pedazo que cambia
if (isset($_GET['ajax'])) {
  echo $hayFiltro ? seccion_productos($categoria, $buscar) : grilla_html();
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>La Reliquia - Antigüedades</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Almendra:wght@400;700&family=Montserrat:wght@600;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="estilos.css">
</head>
<body>

<header class="topbar">
  <form class="buscador" action="inicio.php" method="get" role="search">
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