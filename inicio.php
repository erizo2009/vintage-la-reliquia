<?php
session_start();
require_once __DIR__ . '/conexion.php';

// ---- Retorno de Google: Google vuelve acá con ?code=... ----
if (isset($_GET["code"])) {
    require_once __DIR__ . '/google_config.php';

    $token = $client->fetchAccessTokenWithAuthCode($_GET["code"]);

    if (!isset($token["error"])) {
        $client->setAccessToken($token);
        $oauth = new Google\Service\Oauth2($client);
        $info  = $oauth->userinfo->get();
        $email = $info->email;

        // Buscar o crear el usuario usando su email
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($fila = $res->fetch_assoc()) {
            $id = $fila["id"];
        } else {
            $hash = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("INSERT INTO usuarios (usuario, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $email, $hash);
            $stmt->execute();
            $id = $conexion->insert_id;
        }

        session_regenerate_id(true);
        $_SESSION["usuario_id"] = $id;
        header("Location: inicio.php");
        exit;
    }

    header("Location: index.php");
    exit;
}

// ---- Proteger la página ----
if (!isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit;
}

// ---- Búsqueda (consulta preparada) ----
$buscar = trim($_GET["q"] ?? "");

if ($buscar === "") {
    $resultado = $conexion->query("SELECT * FROM productos");
} else {
    $like = "%" . $buscar . "%";
    $stmt = $conexion->prepare("SELECT * FROM productos WHERE nombre LIKE ?");
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $resultado = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Reliquia - Antigüedades</title>
    <link rel="stylesheet" href="css/style2.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo-area"></div>
            <nav class="menu">

                <form method="GET" action="inicio.php">
                    <input type="text" name="q" placeholder="Buscar" value="<?php echo htmlspecialchars($buscar); ?>">
                    <button type="submit">🔍</button>
                </form>

                <ul>
                    <li><a href="inicio.php">INICIO</a></li>
                    <li><a href="inicio.php">PRODUCTOS</a></li>
                    <li><a href="#">CONTACTOS</a></li>
                    <li><a href="logout.php">CERRAR SESION</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="productos">

        <?php if ($resultado->num_rows === 0) { ?>
            <p class="aviso">No se encontraron productos.</p>
        <?php } ?>

        <?php while ($producto = $resultado->fetch_assoc()) {
            $imagen = !empty($producto["imagen"]) ? $producto["imagen"] : "img/silla.jpg";
        ?>
            <div class="producto">
                <img src="<?php echo htmlspecialchars($imagen); ?>" alt="<?php echo htmlspecialchars($producto["nombre"]); ?>">
                <p class="nombre"><?php echo htmlspecialchars($producto["nombre"]); ?></p>
                <p class="precio">$<?php echo number_format($producto["precio"], 0, ",", "."); ?> ars</p>
            </div>
        <?php } ?>

    </main>
</body>
</html>