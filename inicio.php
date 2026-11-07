
<?php
// Muestra los errores en pantalla (borrá estas 2 líneas cuando todo ande)
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
  $errorDb = '';
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
    $errorDb = $e->getMessage();
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
      <p class="vacio"><small>Error de MySQL: <?= h($errorDb) ?></small></p>
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
  <style>
:root {
  --bordo: #4f2622;
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
.logo img { display: block; height: 118px; width: auto; }

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

/* ---------- Tarjetas con foto de fondo ---------- */
.tarjeta.tiene-foto {
  background-color: var(--gris);
  background-repeat: no-repeat;
  background-position: center;
  background-size: 100% 100%;
  transition: filter .2s, transform .1s;
}
.tarjeta.tiene-foto::after { display: none; }
/* el texto ya viene en la foto: queda solo para lectores de pantalla */
.tarjeta.tiene-foto span {
  position: absolute;
  width: 1px; height: 1px;
  overflow: hidden;
  clip-path: inset(50%);
  white-space: nowrap;
}
.tarjeta.tiene-foto:hover { filter: brightness(1.08); }
/* la segunda fila es más alta, como en el diseño */
.tarjeta:nth-child(n+4) { aspect-ratio: 295 / 294; }

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

/* ---------- Página de productos ---------- */
.lista-productos { max-width: 1100px; margin: 0 auto; padding: 0 20px 56px; }
.titulo-seccion { margin: 8px 0 28px; text-align: center; font-weight: 400; font-size: 2.6rem; color: var(--dorado); }
.vacio { text-align: center; color: #333; font-family: var(--titulo); font-weight: 600; }

.grilla-productos {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 40px 48px;
  padding-top: 12px;
}
.producto { color: #2b1d1a; font-family: var(--deco); }
.producto .foto {
  display: flex;
  align-items: flex-end;
  min-height: 210px;
}
.producto .foto img {
  display: block;
  max-width: 100%;
  max-height: 210px;
  width: auto;
  height: auto;
}
.producto h2 { margin: 10px 0 6px; font-size: 1.6rem; font-weight: 400; line-height: 1.15; }
.producto .precio { margin: 0; font-size: 1.9rem; font-weight: 400; color: #2b1d1a; }
.producto .desc { display: none; }

@media (max-width: 900px) {
  .grilla-productos { grid-template-columns: repeat(2, 1fr); gap: 28px 20px; }
}
@media (max-width: 560px) {
  .grilla-productos { grid-template-columns: 1fr; }
}

/* ---------- Carga en la misma página ---------- */
.volver {
  display: inline-block;
  margin: 4px 0 8px;
  padding: 8px 16px;
  border: 1.5px solid var(--dorado);
  border-radius: 8px;
  background: transparent;
  font-family: var(--deco);
  font-size: 1.15rem;
  color: #8a6d1a;
  cursor: pointer;
}
.volver:hover { background: var(--dorado); color: #fff; }
#contenido { transition: opacity .15s; }
#contenido.cargando { opacity: .45; pointer-events: none; }


/* ---------- Pie de página: beneficios + contacto ---------- */
html { scroll-behavior: smooth; }
body { min-height: 100vh; display: flex; flex-direction: column; }
#contenido { flex: 1; }

.beneficios {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  max-width: 1300px;
  width: 100%;
  margin: 0 auto;
  padding: 56px 24px;
}
.beneficio {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 14px;
  padding: 0 16px;
  color: #111;
}
.beneficio + .beneficio::before {
  content: "";
  position: absolute;
  left: 0;
  top: 50%;
  width: 6px;
  height: 104px;
  transform: translateY(-50%);
  background:
    radial-gradient(circle, #111 2.5px, transparent 3px) top / 6px 6px no-repeat,
    radial-gradient(circle, #111 2.5px, transparent 3px) bottom / 6px 6px no-repeat,
    linear-gradient(#111, #111) center / 2px 100% no-repeat;
}
.beneficio svg { flex: none; width: 56px; height: 56px; }
.beneficio .titulo { margin: 0; font-family: var(--deco); font-size: 2rem; line-height: 1.1; }
.beneficio .sub    { margin: 2px 0 0; font-family: var(--deco); font-size: 1.55rem; line-height: 1.1; color: #c4a93c; }

.pie { background: #804d49; color: #111; font-family: var(--deco); scroll-margin-top: 8px; }
.pie-interior {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  max-width: 1000px;
  margin: 0 auto;
  padding: 18px 24px 26px;
}
.pie h2 { margin: 0 0 8px; font-size: 2.4rem; font-weight: 400; line-height: 1.1; }
.pie ul { list-style: none; margin: 0; padding: 0; }
.pie li { display: flex; align-items: center; gap: 10px; margin: 10px 0; font-size: 1.3rem; }
.pie li svg { flex: none; width: 24px; height: 24px; }
.pie a:hover { text-decoration: underline; }
.pie .red-icono { width: 32px; height: 32px; }

@media (max-width: 900px) {
  .beneficios { grid-template-columns: 1fr; gap: 28px; padding: 36px 16px; }
  .beneficio + .beneficio::before { display: none; }
  .pie-interior { grid-template-columns: 1fr; }
}

</style>
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
  <a href="inicio.php" data-cargar>Inicio</a>
  <a href="inicio.php?categoria=todos" data-cargar>Productos</a>
  <a href="#contacto">Contacto</a>
</nav>

<main id="contenido">
  <?= $hayFiltro ? seccion_productos($categoria, $buscar) : grilla_html() ?>
</main>

<section class="beneficios" aria-label="Beneficios">
  <div class="beneficio">
    <svg viewBox="0 0 64 64" fill="#1c1040" aria-hidden="true">
      <path d="M4 14h32v28H4z"/><path d="M38 22h12l10 10v10H38z"/>
      <circle cx="16" cy="46" r="6" fill="#fff" stroke="#1c1040" stroke-width="3"/>
      <circle cx="48" cy="46" r="6" fill="#fff" stroke="#1c1040" stroke-width="3"/>
    </svg>
    <div><p class="titulo">Enviamos tu compra</p><p class="sub">Envios a todo el país</p></div>
  </div>
  <div class="beneficio">
    <svg viewBox="0 0 64 64" fill="none" stroke="#111" stroke-width="4" stroke-linecap="round" aria-hidden="true">
      <circle cx="32" cy="32" r="28"/>
      <path d="M40 22c-2-3-6-4-9-4-5 0-8 3-8 7 0 10 18 4 18 15 0 4-4 7-9 7-4 0-8-1-10-5M32 11v42" stroke-width="3.5"/>
    </svg>
    <div><p class="titulo">Medios de pago</p><p class="sub">Efectivo Transferencia</p></div>
  </div>
  <div class="beneficio">
    <svg viewBox="0 0 64 64" fill="none" stroke="#111" stroke-width="4" stroke-linejoin="round" aria-hidden="true">
      <rect x="14" y="28" width="36" height="28" rx="4"/>
      <path d="M22 28V20a10 10 0 0 1 20 0v8"/>
    </svg>
    <div><p class="titulo">Compra con seguridad</p><p class="sub">Tus datos siempre protegidos</p></div>
  </div>
</section>

<footer class="pie" id="contacto">
  <div class="pie-interior">
    <div>
      <h2>Contactanos</h2>
      <ul>
        <li>
          <svg viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="1.5" stroke-linejoin="round" aria-hidden="true"><path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2z"/></svg>
          <a href="tel:+54224894017">+54 2248 94017</a>
        </li>
        <li>
          <svg viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="1.5" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="1.5"/><path d="m3 7 9 7 9-7"/></svg>
          <a href="mailto:Lareliquia@gmail.com">Lareliquia@gmail.com</a>
        </li>
        <li>
          <svg viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="1.5" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s7-6.5 7-12a7 7 0 0 0-14 0c0 5.5 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>
          Santa Teresita, CABA
        </li>
      </ul>
    </div>
    <div>
      <h2>Redes sociales</h2>
      <ul>
        <li>
          <svg class="red-icono" viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="16" fill="#111"/><rect x="8.5" y="8.5" width="15" height="15" rx="4.5" fill="none" stroke="#fff" stroke-width="2"/><circle cx="16" cy="16" r="3.6" fill="none" stroke="#fff" stroke-width="2"/><circle cx="20.6" cy="11.4" r="1.1" fill="#fff"/></svg>
          <a href="https://www.instagram.com/lareliquiadeargentinantiguedades" target="_blank" rel="noopener">Lareliquiadeargentinantiguedades</a>
        </li>
        <li>
          <svg class="red-icono" viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="16" fill="#111"/><path d="M17.6 25v-8h2.7l.4-3.1h-3.1v-2c0-.9.3-1.5 1.6-1.5h1.6V7.6c-.3 0-1.2-.1-2.4-.1-2.4 0-4 1.4-4 4.1v2.3h-2.7V17h2.7v8z" fill="#fff"/></svg>
          <a href="#" target="_blank" rel="noopener">Facebook</a>
        </li>
      </ul>
    </div>
  </div>
</footer>

<script>
const contenido = document.getElementById('contenido');

// Pide el contenido al servidor y lo cambia en la misma página, sin recargar
async function cargar(params, guardarHistorial = true) {
  const url   = params ? 'inicio.php?' + params : 'inicio.php';
  const pedido = url + (params ? '&' : '?') + 'ajax=1';
  contenido.classList.add('cargando');
  try {
    const r = await fetch(pedido);
    if (!r.ok) throw new Error(r.status);
    contenido.innerHTML = await r.text();
    if (guardarHistorial) history.pushState(null, '', url);
    contenido.scrollIntoView({ behavior: 'smooth', block: 'start' });
  } catch (e) {
    window.location.href = url;   // si algo falla, carga normal
  }
  contenido.classList.remove('cargando');
}

// Toque en una tarjeta o búsqueda
document.addEventListener('submit', (e) => {
  const f = e.target;
  if (!f.matches('.categorias, .buscador')) return;
  e.preventDefault();
  const datos = new URLSearchParams();
  if (f.matches('.categorias')) {
    if (!e.submitter) return;
    datos.set('categoria', e.submitter.value);
  } else {
    const q = f.q.value.trim();
    if (!q) return;
    datos.set('q', q);
  }
  cargar(datos.toString());
});

// "Volver a las categorías" y enlaces del menú
document.addEventListener('click', (e) => {
  if (e.target.closest('[data-volver]')) { e.preventDefault(); cargar(''); return; }
  const a = e.target.closest('a[data-cargar]');
  if (a) {
    e.preventDefault();
    cargar(new URL(a.href).search.replace(/^\?/, ''));
  }
});

// Botón "atrás" del navegador
window.addEventListener('popstate', () => {
  const p = new URLSearchParams(location.search);
  const datos = new URLSearchParams();
  if (p.get('categoria')) datos.set('categoria', p.get('categoria'));
  if (p.get('q')) datos.set('q', p.get('q'));
  cargar(datos.toString(), false);
});
</script>

</body>
</html>