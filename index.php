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
    </button>
    <button type="button" class="social-btn" aria-label="Registrarse con Apple">
      <svg viewBox="0 0 24 24" width="28" height="28"><path fill="#000" d="M12.152 6.896c-.948 0-2.415-1.078-3.96-1.04-2.04.027-3.91 1.183-4.961 3.014-2.117 3.675-.546 9.103 1.519 12.09 1.013 1.454 2.208 3.09 3.792 3.039 1.52-.065 2.09-.987 3.935-.987 1.831 0 2.35.987 3.96.948 1.637-.026 2.676-1.48 3.676-2.948 1.156-1.688 1.636-3.325 1.662-3.415-.039-.013-3.182-1.221-3.22-4.857-.026-3.04 2.48-4.494 2.597-4.559-1.429-2.09-3.623-2.324-4.39-2.376-2-.156-3.675 1.09-4.61 1.09zM15.53 3.83c.843-1.012 1.4-2.427 1.245-3.83-1.207.052-2.662.805-3.532 1.818-.78.896-1.454 2.338-1.273 3.714 1.338.104 2.715-.688 3.559-1.701"/></svg>
    </button>
  </div>

  <p class="footer">¿No tienes cuenta? <a href="#">Log In</a></p>
</main>
</body>
</html>