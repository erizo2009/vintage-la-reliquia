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

            <input type="text" name="q" placeholder="Buscar">
            <button type="submit">🔍</button>

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
       
        if (mysqli_num_rows($resultado) == 0) {
            echo "<p class='aviso'>No se encontraron productos.</p>";
        }

        while ($producto = mysqli_fetch_assoc($resultado)) {
        ?>

            <div class="producto">
                
               <img src="img/silla.jpg" alt="<?php echo $producto['nombre']; ?>">
                <p class="nombre"><?php echo $producto['sillas']; ?></p>
                <p class="precio">$<?php echo number_format($producto['75.000'], 3, ',', '.'); ?> ars</p>
            </div>

        <?php
        }
        ?>

    </main>

</body>
</html>