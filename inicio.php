<?php
session_start();


$usuario = $_SESSION["usuario"] ?? "";

// Lista de productos: para agregar uno nuevo, copiá una línea y cambiá los datos
$productos = [
    ["foto" => "img/sillas.jpg", "nombre" => "Par de sillas estilo Luis XV", "precio" => "$750.000 ars"],
    ["foto" => "img/mesa.jpg", "nombre" => "Lámpara de bronce y cristal", "precio" => "$250.000 ars"],
    ["foto" => "img/mesita.jpg", "nombre" => "Reloj de pared antiguo", "precio" => "$150.000 ars"],
    ["foto" => "img/banquito.jpg", "nombre" => "Mesa de comedor estilo Art Deco", "precio" => "$1.200.000 ars"],
    ["foto" => "img/estantes.jpg", "nombre" => "Cuadro al óleo del siglo XIX", "precio" => "$500.000 ars"],
    ["foto" => "img/mesagrande.jpg", "nombre" => "mesa grande para comedor", "precio" => ""],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Reliquia Antigüedades</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style2.css">
</head>
<body>

    
    <header class="encabezado">

        <form class="buscador" action="tienda.php" method="GET">
            <input type="text" name="q" placeholder="Buscar" value="<?php echo htmlspecialchars($_GET["q"] ?? ""); ?>">
            <button type="submit" aria-label="Buscar">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="10.5" cy="10.5" r="6.5"></circle>
                    <line x1="15.5" y1="15.5" x2="21" y2="21"></line>
                </svg>
            </button>
        </form>

    
            <span class="logo-texto">La Reliquia</span>
            <span class="logo-sub">antigüedades</span>
        </a>

        <div class="acciones">
            <?php if ($usuario == "") { ?>
                <a href="index.php">Crear cuenta</a>
                <span class="divisor"></span>
                <a href="login.php">Iniciar sesión</a>
            <?php } else { ?>
                <span>Hola, <?php echo htmlspecialchars($usuario); ?></span>
                <span class="divisor"></span>
                <a href="salir.php">Cerrar sesión</a>
            <?php } ?>

            <a href="#" class="carrito" aria-label="Carrito">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 3h3l2.5 12.5a1.5 1.5 0 0 0 1.5 1.2h8.2a1.5 1.5 0 0 0 1.5-1.1L20.5 8H6"></path>
                    <circle cx="9.5" cy="20.5" r="1.2"></circle>
                    <circle cx="17.5" cy="20.5" r="1.2"></circle>
                </svg>
            </a>
        </div>

    </header>

    <!-- MENÚ -->
    <nav class="menu">
        <a href="tienda.php">Inicio</a>
        <a href="#productos">Productos</a>
        <a href="#contacto">Contacto</a>
    </nav>

    <!-- PRODUCTOS -->
    <main class="catalogo" id="productos">

        <?php foreach ($productos as $p) { ?>
            <article class="producto">
                <div class="foto">
                    <img src="<?php echo $p["foto"]; ?>" alt="<?php echo $p["nombre"]; ?>">
                </div>

                <?php if ($p["nombre"] != "") { ?>
                    <h3><?php echo $p["nombre"]; ?></h3>
                    <p class="precio"><?php echo $p["precio"]; ?></p>
                <?php } ?>
            </article>
        <?php } ?>

    </main>

    <footer id="contacto"></footer>

</body>
</html>