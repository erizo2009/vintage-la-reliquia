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
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign Up | La Reliquia Antigüedades</title>
<link href="https://fonts.googleapis.com/css2?family=Libre+Caslon+Condensed:wght@400;700&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<main class="card">
  <h1 class="title">Sign Up to<span class="brand">La Reliquia<small>antiguedades</small></span></h1>

  <form class="form" action="#" method="post">
    <div class="field">
      <label for="user">User</label>
      <input id="user" name="user" type="text" autocomplete="username" required>
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" autocomplete="new-password" required>
    </div>
    <button class="btn" type="submit">Sign Up</button>
  </form>

  <div class="divider"><span>Sign Up with</span></div>

  <div class="social">
    <button type="button" class="social-btn" aria-label="Registrarse con Gmail">
      <svg viewBox="0 0 24 24" width="28" height="28" fill="none">
        <path d="M3.5 19V6.5" stroke="#4285F4" stroke-width="3"/>
        <path d="M20.5 19V6.5" stroke="#34A853" stroke-width="3"/>
        <path d="M3.5 6.5 12 13l8.5-6.5" stroke="#EA4335" stroke-width="3"/>
        <path d="M20.5 6.5V9" stroke="#FBBC04" stroke-width="3"/>
    </svg>
    <div class="boton-google">
            <a href="google_login.php">Iniciar sesión con Google</a>
        </div>

        <p><a href="registro.php">¿No tenés cuenta? Crear cuenta</a></p>

    </div>
    </button>
  </div>

  <p class="footer">¿No tienes cuenta? <a href="#">Log In</a></p>
</main>
</body>
</html>