<?php
session_start();
require_once __DIR__ . '/conexion.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario  = trim($_POST["usuario"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conexion->prepare("SELECT id, password FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($fila = $res->fetch_assoc()) {
        if (password_verify($password, $fila["password"])) {
            session_regenerate_id(true);
            $_SESSION["usuario_id"] = $fila["id"];
            header("Location: inicio.php");
            exit;
        }
    }
    $mensaje = "Usuario o contraseña incorrectos";
}

if (isset($_GET["registrado"])) {
    $mensaje = "Cuenta creada, ya podés iniciar sesión";
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

        <h1 class="titulo">Bienvenido a <span class="marca">La Reliquia</span></h1>
        <h2>Iniciar sesión</h2>

        <?php if ($mensaje !== "") { ?>
            <p class="error"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php } ?>

        <form action="index.php" method="POST">

            <div class="usuario">
                <input type="text" name="usuario" placeholder="Usuario" required>
            </div>

            <div class="contraseña">
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>

            <div class="boton">
                <button type="submit">Iniciar sesión</button>
            </div>
        </form>

        <p>o</p>

        <div class="boton-google">
            <a href="google_login.php">Iniciar sesión con Google</a>
        </div>

        <p><a href="registro.php">¿No tenés cuenta? Crear cuenta</a></p>

    </div>

</body>
</html>