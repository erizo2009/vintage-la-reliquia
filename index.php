<?php
session_start();

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $usuario = $_POST["usuario"];
    $contraseña = $_POST["contraseña"];

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Reliquia</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="login">

        <h1 class="titulo">Sign Up To <span class="marca">La Reliquia</span></h1>
        <h2>Iniciar sesión</h2>

        <?php if ($mensaje != "") { ?>
            <p class="error"><?php echo $mensaje; ?></p>
        <?php } ?>

        
        <form action="guardar.php" method="POST">

            <div class="usuario">
                <input type="text" name="usuario" placeholder="Usuario" required>
            </div>

            <div class="contraseña">
                <input type="password" name="contraseña" placeholder="Contraseña" required>
            </div>

            <div class="boton">
                <button type="submit">Iniciar sesión</button>
            </div>
          <include "inicio.php">
        </form>

        <p>o</p>

      
        <div class="boton-google">
            <a href="google_login.php">
                Iniciar sesión con Google
            </a>
        </div>

    </div>

</body>
</html>