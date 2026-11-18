<?php
session_start();
require_once 'datos.php';   

function h($t) { return htmlspecialchars((string)$t, ENT_QUOTES, 'UTF-8'); }
$productos = [];
foreach (productos_lista() as $p) {
    $productos[(int)$p['id']] = $p;
}

if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $accion = $_POST['accion'] ?? '';
  $id     = (int)($_POST['id'] ?? -1);
  $existe = isset($productos[$id]);

  switch ($accion) {
    case 'agregar':
      if ($existe) $_SESSION['carrito'][$id] = min(99, ($_SESSION['carrito'][$id] ?? 0) + 1);
      break;
    case 'sumar':
      if ($existe && isset($_SESSION['carrito'][$id])) $_SESSION['carrito'][$id] = min(99, $_SESSION['carrito'][$id] + 1);
      break;
    case 'restar':
      if (isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id]--;
        if ($_SESSION['carrito'][$id] <= 0) unset($_SESSION['carrito'][$id]);
      }
      break;
    case 'quitar':
      unset($_SESSION['carrito'][$id]);
      break;
    case 'vaciar':
      $_SESSION['carrito'] = [];
      break;
  }
  header('Location: carrito.php');
  exit;
}


$items = [];
$total = 0;
$cantidadTotal = 0;
foreach ($_SESSION['carrito'] as $id => $cant) {
  if (!isset($productos[$id])) continue;
  $p = $productos[$id];
  $sub = $p['precio'] * $cant;
  $items[] = ['id' => $id, 'p' => $p, 'cant' => $cant, 'sub' => $sub];
  $total += $sub;
  $cantidadTotal += $cant;
}

function precio($n) { return '$' . number_format((float)$n, 0, ',', '.'); }


$msg = "Hola! Quiero consultar por estos productos:\n";
foreach ($items as $it) {
  $msg .= "- {$it['cant']} x {$it['p']['nombre']} (" . precio($it['p']['precio']) . ")\n";
}
$msg .= "Total: " . precio($total);
$wa = 'https://wa.me/54224894017?text=' . rawurlencode($msg);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Carrito - La Reliquia</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="icon" type="image/png" href="favicon-16x16.png">
  <link href="https://fonts.googleapis.com/css2?family=Almendra:wght@400;700&family=Montserrat:wght@600;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/carrito.css">
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
    <?php if ($logo = imagen('logo.png')): ?>
      <img src="<?= $logo ?>" alt="La Reliquia antigüedades">
    <?php else: ?>
      <span class="logo-texto">La Reliquia <small>antigüedades</small></span>
    <?php endif; ?>
  </a>

  <div class="acciones">
    <a class="carrito" href="carrito.php" aria-label="Carrito">
      <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
        <path d="M2 3h3l2.6 12.2a1 1 0 0 0 1 .8h9.4a1 1 0 0 0 1-.8L21 7H6"/>
        <circle cx="9.5" cy="20" r="1.4"/><circle cx="17" cy="20" r="1.4"/>
      </svg>
      <?php if ($cantidadTotal > 0): ?><span class="badge"><?= (int)$cantidadTotal ?></span><?php endif; ?>
    </a>
  </div>
</header>


<main class="fondo-carrito">
<?php if (!$items): ?>

  <div class="tarjeta-c vacio" style="max-width:700px;margin:0 auto;">
    <svg viewBox="0 0 24 24" width="84" height="84" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
      <path d="M2 3h3l2.6 12.2a1 1 0 0 0 1 .8h9.4a1 1 0 0 0 1-.8L21 7H6"/>
      <circle cx="9.5" cy="20" r="1.4"/><circle cx="17" cy="20" r="1.4"/>
    </svg>
    <h2>Tu carrito está vacío</h2>
    <p>¿No sabés qué comprar? ¡Mirá nuestras antigüedades!</p>
    <a class="btn-continuar" href="inicio.php?categoria=todos">Ver productos</a>
  </div>

<?php else: ?>

  <div class="carrito-grid">

    <div>
      <section class="tarjeta-c">
        <div class="c-cabecera">
          <h1>Carrito <span class="n">(<?= (int)$cantidadTotal ?> <?= $cantidadTotal === 1 ? 'producto' : 'productos' ?>)</span></h1>
          <form method="post" action="carrito.php">
            <input type="hidden" name="accion" value="vaciar">
            <button type="submit" class="link-btn">Vaciar carrito</button>
          </form>
        </div>

        <?php foreach ($items as $it): $p = $it['p']; ?>
          <article class="item">
            <div class="foto">
              <?php if ($src = imagen($p['imagen'])): ?>
                <img src="<?= $src ?>" alt="<?= h($p['nombre']) ?>">
              <?php endif; ?>
            </div>

            <div class="info">
              <h2><?= h($p['nombre']) ?></h2>
              <p class="cat"><?= h($p['categoria']) ?></p>

              <form method="post" action="carrito.php" class="cant">
                <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
                <button type="submit" name="accion" value="restar" aria-label="Restar uno">−</button>
                <span><?= (int)$it['cant'] ?></span>
                <button type="submit" name="accion" value="sumar" aria-label="Sumar uno">+</button>
              </form>
            </div>

            <div class="derecha">
              <form method="post" action="carrito.php">
                <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
                <button type="submit" name="accion" value="quitar" class="quitar" aria-label="Quitar del carrito">
                  <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 7h16M10 11v6M14 11v6M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12M9 7V4h6v3"/>
                  </svg>
                </button>
              </form>
              <div>
                <div class="precio"><?= precio($it['sub']) ?></div>
                <?php if ($it['cant'] > 1): ?><div class="unit"><?= precio($p['precio']) ?> c/u</div><?php endif; ?>
              </div>
            </div>
          </article>
        <?php endforeach; ?>

        <div class="c-envio">
          <span>Envío</span>
          <span class="ok">A coordinar con el vendedor</span>
        </div>
      </section>
    </div>

    <aside class="tarjeta-c resumen">
      <h2>Resumen de compra</h2>
      <div class="fila"><span>Productos (<?= (int)$cantidadTotal ?>)</span><span><?= precio($total) ?></span></div>
      <div class="fila"><span>Envío</span><span>A coordinar</span></div>
      <div class="fila total"><strong>Total</strong><strong><?= precio($total) ?> ars</strong></div>

      <a class="btn-continuar" href="<?= h($wa) ?>" target="_blank" rel="noopener">Continuar (<?= (int)$cantidadTotal ?>)</a>
      <a class="seguir" href="inicio.php?categoria=todos">Seguir comprando</a>
      <p class="nota">Medios de pago: efectivo o transferencia. Te contactamos para coordinar el pago y el envío.</p>
    </aside>

  </div>

<?php endif; ?>
</main>

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

</body>
</html>