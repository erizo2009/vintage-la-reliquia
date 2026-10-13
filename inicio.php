<?php
require "conexion.php";
$buscar = "";
if (isset($_GET["q"])) {
    $buscar = $_GET["q"];
}
if ($buscar == "") {
    
    $sql = "SELECT * FROM productos";
} else {
   
    $buscar_seguro = mysqli_real_escape_string($conexion, $buscar);
    $sql = "SELECT * FROM productos WHERE nombre LIKE '%$buscar_seguro%'";
}
$resultado = mysqli_query($conexion, $sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Reliquia - Antigüedades</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo-area">
            </div>
            <nav class="menu">
            <form>
            <input type="text" name="q" placeholder="Buscar">
            <button type="submit">🔍</button>
</form>

                <ul>
                    <li><a href="#">INICIO</a></li>
                    <li><a href="#">PRODUCTOS</a></li>
                    <li><a href="#">CONTACTOS</a></li>
                    <li><a href="#">CREAR CUENTA</a></li>
                    <li><a href="#">INICIAR SESION</a></li>
                </ul>
            </nav>
        </div>
</header>
   
    <main class="productos">

        <?php
        // Si no hay ningún producto, avisamos
        if (mysqli_num_rows($resultado) == 0) {
            echo "<p class='aviso'>No se encontraron productos.</p>";
        }

        // Este "while" repite el bloque de abajo UNA VEZ POR CADA producto.
        // En cada vuelta, $producto tiene los datos de un producto.
        while ($producto = mysqli_fetch_assoc($resultado)) {
        ?>

            <div class="producto">
                <!-- La foto: viene de la carpeta "img" -->
               <img src="img/silla.jpg" alt="<?php echo $producto['nombre']; ?>">

                <!-- El nombre -->
                <p class="nombre"><?php echo $producto['nombre']; ?></p>

                <!-- El precio (number_format le pone los puntos: 750.000) -->
                <p class="precio">$<?php echo number_format($producto['precio'], 0, ',', '.'); ?> ars</p>
            </div>

        <?php
        } // aquí termina el while
        ?>

    </main>

</body>
</html>