<?php
session_start();
if (!isset($_SESSION['usuario'])) { header('Location: inicio.php'); exit; }

function h($t) { return htmlspecialchars((string)$t, ENT_QUOTES, 'UTF-8'); }

// Carpeta (relativa a este archivo) donde están las fotos de los productos.
// Cambiala por la misma que usa imagen() en datos.php.
const CARPETA_IMG = 'img';

$categorias = ['comedor','dormitorio','living','vitrinas','escritorios','iluminacion'];

// Si datos.php ya tiene una conexión, usá esa en vez de esta
$pdo = new PDO('mysql:host=localhost;dbname=vintage;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    if ($accion === 'eliminar' && $id > 0) {
        $pdo->prepare('DELETE FROM productos WHERE id = ?')->execute([$id]);
        header('Location: inicio.php?categoria=todos'); exit;
    }

    if ($accion === 'guardar') {
        $nombre      = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio      = (float)str_replace(['.', ','], ['', '.'], $_POST['precio'] ?? '0');
        $categoria   = $_POST['categoria'] ?? '';
        $imagen      = trim($_POST['imagen_actual'] ?? '');

        if ($nombre === '' || !in_array($categoria, $categorias, true) || $precio <= 0) {
            $error = 'Completá nombre, categoría y un precio válido.';
        } else {
            // Subida de imagen (opcional)
            if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) {
                    $error = 'La imagen debe ser JPG, PNG o WEBP.';
                } else {
                    $base = preg_replace('/[^a-z0-9\-]+/', '-', strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_FILENAME)));
                    $imagen = $base . '-' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/' . CARPETA_IMG . '/' . $imagen);
                }
            }

            if ($error === '') {
                if ($id > 0) {
                    $pdo->prepare('UPDATE productos SET nombre=?, descripcion=?, precio=?, categoria=?, imagen=? WHERE id=?')
                        ->execute([$nombre, $descripcion, $precio, $categoria, $imagen, $id]);
                } else {
                    $pdo->prepare('INSERT INTO productos (nombre, descripcion, precio, categoria, imagen) VALUES (?,?,?,?,?)')
                        ->execute([$nombre, $descripcion, $precio, $categoria, $imagen]);
                }
                header('Location: inicio.php?categoria=todos'); exit;
            }
        }
    }
}

// Cargar producto si se está editando
$p = ['id'=>0,'nombre'=>'','descripcion'=>'','precio'=>'','categoria'=>'living','imagen'=>''];
if (isset($_GET['editar'])) {
    $st = $pdo->prepare('SELECT * FROM productos WHERE id = ?');
    $st->execute([(int)$_GET['editar']]);
    $p = $st->fetch(PDO::FETCH_ASSOC) ?: $p;
}
// Si hubo error, conservar lo escrito
if ($error !== '') {
    $p = array_merge($p, array_intersect_key($_POST, $p));
}
$editando = ((int)$p['id'] > 0);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $editando ? 'Editar' : 'Agregar' ?> producto - La Reliquia</title>
  <link href="https://fonts.googleapis.com/css2?family=Almendra:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: "Almendra", Georgia, serif; background:#f4eeee; margin:0; padding:30px 16px; color:#2b1d1a; }
    .caja { max-width:560px; margin:0 auto; background:#fff; padding:28px; border-radius:10px; box-shadow:0 1px 6px rgba(0,0,0,.2); }
    h1 { margin-top:0; font-weight:400; }
    label { display:block; margin:14px 0 4px; font-size:1.2rem; }
    input, select, textarea { width:100%; padding:10px; font:inherit; border:1px solid #999; border-radius:6px; box-sizing:border-box; }
    .error { background:#fde2e2; color:#8a1f1f; padding:10px; border-radius:6px; }
    .acciones { margin-top:22px; display:flex; gap:12px; }
    button, .cancelar { padding:10px 22px; border:0; border-radius:8px; font:inherit; font-size:1.2rem; cursor:pointer; text-decoration:none; }
    button { background:#4f2622; color:#fff; }
    .cancelar { background:#ddd; color:#222; }
    .actual { margin-top:8px; font-size:1rem; }
  </style>
</head>
<body>
<div class="caja">
  <h1><?= $editando ? 'Editar producto' : 'Agregar producto' ?></h1>
  <?php if ($error): ?><p class="error"><?= h($error) ?></p><?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="accion" value="guardar">
    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
    <input type="hidden" name="imagen_actual" value="<?= h($p['imagen']) ?>">

    <label for="nombre">Nombre</label>
    <input id="nombre" name="nombre" value="<?= h($p['nombre']) ?>" required>

    <label for="descripcion">Descripción</label>
    <textarea id="descripcion" name="descripcion" rows="3"><?= h($p['descripcion']) ?></textarea>

    <label for="precio">Precio (ARS)</label>
    <input id="precio" name="precio" inputmode="decimal" value="<?= $p['precio'] !== '' ? h((int)$p['precio']) : '' ?>" required>

    <label for="categoria">Categoría</label>
    <select id="categoria" name="categoria">
      <?php foreach ($categorias as $c): ?>
        <option value="<?= h($c) ?>" <?= $p['categoria'] === $c ? 'selected' : '' ?>><?= h(ucfirst($c)) ?></option>
      <?php endforeach; ?>
    </select>

    <label for="foto">Imagen</label>
    <input id="foto" type="file" name="foto" accept=".jpg,.jpeg,.png,.webp">
    <?php if ($p['imagen']): ?><p class="actual">Actual: <?= h($p['imagen']) ?> (dejá vacío para conservarla)</p><?php endif; ?>

    <div class="acciones">
      <button type="submit"><?= $editando ? 'Guardar cambios' : 'Agregar' ?></button>
      <a class="cancelar" href="inicio.php?categoria=todos">Cancelar</a>
    </div>
  </form>
</div>
</body>
</html>