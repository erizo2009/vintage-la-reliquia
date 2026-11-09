<?php
session_start();
$logueado = isset($_SESSION['usuario']);   // cambiá 'usuario' por tu variable de sesión

// ---------- Conexión a MySQL (XAMPP: usuario root, sin contraseña) ----------
$conexion = mysqli_connect("localhost", "root", "", "vintage");
if (!$conexion) {
  die("No se pudo conectar con MySQL. ¿Está iniciado en XAMPP?");
}

// ---------- Traemos los productos de la tabla de MySQL ----------
$productos = mysqli_query($conexion, "SELECT * FROM productos ORDER BY id");
if (!$productos) {
  die("Falta la tabla productos. Ejecutá productos.sql en phpMyAdmin.");
}

// ---------- Pasamos los productos de MySQL a una lista ----------
$lista = [];
while ($fila = mysqli_fetch_assoc($productos)) {
  $lista[] = $fila;
}

// ---------- Productos de VITRINAS cargados acá, en el PHP ----------
// (para agregar uno más, copiá una línea y cambiala)
$vitrinas = [
  ['nombre' => 'Importante vitrina roble Chippendale tallada', 'precio' => 2400000, 'categoria' => 'vitrinas', 'imagen' => 'vitrina-roble.jpg'],
  ['nombre' => 'Antiguo aparador ingles tallado cedro',        'precio' => 1200000, 'categoria' => 'vitrinas', 'imagen' => 'aparador-ingles.jpg'],
  ['nombre' => 'Antiguo aparador Chippendale tallado',         'precio' => 980000,  'categoria' => 'vitrinas', 'imagen' => 'aparador-chippendale.jpg'],
  ['nombre' => 'Antiguo petit chifonier frances',              'precio' => 1800000, 'categoria' => 'vitrinas', 'imagen' => 'petit-chifonier.jpg'],
];

// Los sumamos a la lista (si ya estaban en MySQL, no los repetimos)
foreach ($vitrinas as $v) {
  $yaEsta = false;
  foreach ($lista as $fila) {
    if ($fila['imagen'] == $v['imagen']) {
      $yaEsta = true;
    }
  }
  if (!$yaEsta) {
    $lista[] = $v;
  }
}

// ---------- Las 6 categorías ----------
$categorias = [
  ['clave' => 'comedor',     'nombre' => 'Comedor',                'descripcion' => 'Sillas mesas y juegos de comedor'],
  ['clave' => 'dormitorio',  'nombre' => 'Dormitorio',             'descripcion' => 'Camas, cómodas y mesas de luz'],
  ['clave' => 'living',      'nombre' => 'Living',                 'descripcion' => 'Sillones, mesas ratonas y más'],
  ['clave' => 'vitrinas',    'nombre' => 'Vitrinas y guardado',    'descripcion' => 'Vitrinas, bibliotecas y muebles de guardado'],
  ['clave' => 'escritorios', 'nombre' => 'Escritorios y ficheros', 'descripcion' => 'Escritorios y ficheros'],
  ['clave' => 'iluminacion', 'nombre' => 'Iluminación',            'descripcion' => 'Arañas, lámparas y apliques'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>La Reliquia - Antigüedades</title>
  <!-- Tipografías de Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Almendra&family=Montserrat:wght@800&display=swap" rel="stylesheet">
  <!-- Archivo de estilos (está en la carpeta css, al lado de este archivo) -->
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

  <!-- ================= ENCABEZADO ================= -->
  <header class="encabezado">

    <form class="buscador" onsubmit="buscar(); return false;">
      <input type="text" id="texto-busqueda" placeholder="Buscar">
      <button type="submit">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
          <circle cx="10.5" cy="10.5" r="6.5"/><path d="M15.5 15.5 21 21"/>
        </svg>
      </button>
    </form>

    <img class="logo" src="img/logo.png" alt="La Reliquia antigüedades">

    <div class="acciones">
      <?php if ($logueado) { ?>
        <a href="cerrar_sesion.php">Cerrar sesión</a>
      <?php } else { ?>
        <a href="registro.php">Crear cuenta</a>
        <span class="linea"></span>
        <a href="login.php">Iniciar sesión</a>
      <?php } ?>
      <a href="carrito.php" class="carrito">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
          <path d="M2 3h3l2.6 12.2a1 1 0 0 0 1 .8h9.4a1 1 0 0 0 1-.8L21 7H6"/>
          <circle cx="9.5" cy="20" r="1.4"/><circle cx="17" cy="20" r="1.4"/>
        </svg>
      </a>
    </div>

  </header>

  <!-- ================= MENÚ ================= -->
  <nav class="menu">
    <a href="#" onclick="mostrarInicio(); return false;">Inicio</a>
    <a href="#" onclick="mostrarProductos('todos'); return false;">Productos</a>
    <!-- Este enlace baja hasta el div que tiene id="contactos" -->
    <a href="#contactos">Contacto</a>
  </nav>

  <main class="principal">

    <!-- ========== PANTALLA 1: las 6 categorías (botones) ========== -->
    <section id="inicio" class="categorias">
      <?php foreach ($categorias as $c) { ?>
        <button class="tarjeta" onclick="mostrarProductos('<?= $c['clave'] ?>', '<?= $c['nombre'] ?>', '<?= $c['descripcion'] ?>')">
          <img src="img/<?= $c['clave'] ?>.png" alt="<?= $c['nombre'] ?>">
        </button>
      <?php } ?>
    </section>

    <!-- ========== PANTALLA 2: los productos ========== -->
    <section id="productos" class="oculto">

      <!-- Panel de la izquierda (solo cuando se elige una categoría) -->
      <div id="lateral">
        <p class="migas"><a href="#" onclick="mostrarInicio(); return false;">Inicio</a>/ <span id="migas-nombre"></span></p>
        <h1 id="titulo-categoria"></h1>
        <p id="descripcion-categoria"></p>
      </div>

      <!-- Lista de productos. La clase indica a qué categoría pertenece -->
      <div class="lista">
        <p id="vacio" class="oculto">No se encontraron productos.</p>

        <div class="grilla">

          <?php foreach ($lista as $p) { ?>
            <article class="producto <?= $p['categoria'] ?>">
              <img src="img/productos/<?= $p['imagen'] ?>" alt="<?= $p['nombre'] ?>">
              <h2><?= $p['nombre'] ?></h2>
              <p class="precio">$<?= number_format($p['precio'], 0, ',', '.') ?> ars</p>
            </article>
          <?php } ?>

        </div>
      </div>
    </section>

  </main>

  <!-- ================= BENEFICIOS ================= -->
  <section class="beneficios">
    <div class="beneficio">
      <svg width="56" height="56" viewBox="0 0 64 64" fill="#1c1040">
        <path d="M4 14h32v28H4z"/><path d="M38 22h12l10 10v10H38z"/>
        <circle cx="16" cy="46" r="6" fill="#fff" stroke="#1c1040" stroke-width="3"/>
        <circle cx="48" cy="46" r="6" fill="#fff" stroke="#1c1040" stroke-width="3"/>
      </svg>
      <div><p class="titulo">Enviamos tu compra</p><p class="sub">Envios a todo el país</p></div>
    </div>
    <div class="beneficio">
      <svg width="56" height="56" viewBox="0 0 64 64" fill="none" stroke="#111" stroke-width="4" stroke-linecap="round">
        <circle cx="32" cy="32" r="28"/>
        <path d="M40 22c-2-3-6-4-9-4-5 0-8 3-8 7 0 10 18 4 18 15 0 4-4 7-9 7-4 0-8-1-10-5M32 11v42" stroke-width="3.5"/>
      </svg>
      <div><p class="titulo">Medios de pago</p><p class="sub">Efectivo Transferencia</p></div>
    </div>
    <div class="beneficio">
      <svg width="56" height="56" viewBox="0 0 64 64" fill="none" stroke="#111" stroke-width="4" stroke-linejoin="round">
        <rect x="14" y="28" width="36" height="28" rx="4"/><path d="M22 28V20a10 10 0 0 1 20 0v8"/>
      </svg>
      <div><p class="titulo">Compra con seguridad</p><p class="sub">Tus datos siempre protegidos</p></div>
    </div>
  </section>

  <!-- ================= CONTACTOS (el menú "Contacto" baja hasta acá) ================= -->
  <div id="contactos">
    <div class="contactos-interior">

      <div>
        <h2>Contactanos</h2>
        <p>📞 +54 2248 94017</p>
        <p>✉️ Lareliquia@gmail.com</p>
        <p>📍 Santa Teresita, CABA</p>
      </div>

      <div>
        <h2>Redes sociales</h2>
        <p>📷 Lareliquiadeargentinantiguedades</p>
        <p>📘 Facebook</p>
      </div>

    </div>
  </div>

  <!-- JavaScript: hace que los botones muestren los productos sin cambiar de página -->
  <script>
    var pantallaInicio = document.getElementById('inicio');
    var pantallaProductos = document.getElementById('productos');
    var lateral = document.getElementById('lateral');

    // Volver a las 6 categorías
    function mostrarInicio() {
      pantallaInicio.classList.remove('oculto');
      pantallaProductos.classList.add('oculto');
      document.getElementById('texto-busqueda').value = '';
    }

    // Mostrar los productos de una categoría ('todos' = todos los productos)
    function mostrarProductos(categoria, nombre, descripcion) {
      pantallaInicio.classList.add('oculto');
      pantallaProductos.classList.remove('oculto');

      if (categoria == 'todos') {
        lateral.classList.add('oculto');
        pantallaProductos.className = 'sin-lateral';
      } else {
        lateral.classList.remove('oculto');
        pantallaProductos.className = 'con-lateral';
        document.getElementById('migas-nombre').textContent = nombre;
        document.getElementById('titulo-categoria').textContent = nombre;
        document.getElementById('descripcion-categoria').textContent = descripcion;
      }

      filtrar(categoria, '');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Buscador
    function buscar() {
      var texto = document.getElementById('texto-busqueda').value.toLowerCase();
      pantallaInicio.classList.add('oculto');
      pantallaProductos.classList.remove('oculto');
      lateral.classList.add('oculto');
      pantallaProductos.className = 'sin-lateral';
      filtrar('todos', texto);
    }

    // Muestra solo los productos que corresponden
    function filtrar(categoria, texto) {
      var lista = document.querySelectorAll('.producto');
      var visibles = 0;

      for (var i = 0; i < lista.length; i++) {
        var esDeLaCategoria = (categoria == 'todos') || lista[i].classList.contains(categoria);
        var coincide = lista[i].textContent.toLowerCase().indexOf(texto) != -1;

        if (esDeLaCategoria && coincide) {
          lista[i].classList.remove('oculto');
          visibles++;
        } else {
          lista[i].classList.add('oculto');
        }
      }

      if (visibles == 0) {
        document.getElementById('vacio').classList.remove('oculto');
      } else {
        document.getElementById('vacio').classList.add('oculto');
      }
    }
  </script>
</body>
</html>