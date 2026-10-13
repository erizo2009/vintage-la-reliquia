<?php
// Productos - podés conectarlo a una base de datos después
$productos = [
    ["nombre" => "Par de sillas estilo Luis XV", "precio" => 750000, "img" => "img/sillas.jpg"],
    ["nombre" => "Mesa antigua de caoba", "precio" => 300000, "img" => "img/mesa_caoba.jpg"],
    ["nombre" => "Mesa redonda mármol", "precio" => 450000, "img" => "img/mesa_marmol.jpg"],
    ["nombre" => "Mesa en Nácar y Mármol", "precio" => 500000, "img" => "img/mesa_nacar.jpg"],
    ["nombre" => "Mesa oriental siglo XV en nácar y oro", "precio" => 700000, "img" => "img/mesa_oriental.jpg"],
    ["nombre" => "Par de mesitas auxiliares", "precio" => 380000, "img" => "img/mesitas.jpg"],
];

$busqueda = $_GET['q'] ?? '';
if ($busqueda) {
    $productos = array_filter($productos, fn($p) => stripos($p['nombre'], $busqueda) !== false);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>La Reliquia - Antigüedades</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<include("index.php")>
<header class="top-bar">
    <div class="search-box">
        <form method="GET">
            <input type="text" name="q" placeholder="Buscar" value="<?= htmlspecialchars($busqueda) ?>">
            <button type="submit">Q</button>

        </form>
    </div>
    <div class="logo">La Reliquia <span>antigüedades</span></div>
    <div class="account">
        <a href="#">Crear cuenta</a> | <a href="#">iniciar sesión</a> <span class="cart">🛒</span>
    </div>
</header>

<nav class="menu">
    <a href="#">Inicio</a>
    <a href="#">Productos</a>
    <a href="#">Contacto</a>
</nav>

<main class="grid">
    <?php foreach ($productos as $prod): ?>
        <div class="card">
            <div class="img-box">
                <img src="<?= $prod['img'] ?>" alt="<?= $prod['nombre'] ?>">
            </div>
            <p class="nombre"><?= $prod['nombre'] ?></p>
            <p class="precio">$<?= number_format($prod['precio'], 0, ',', '.') ?> ars</p>
        </div>
    <?php endforeach; ?>
</main>

</body>
</html>