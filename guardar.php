<?php
include("conexion.php");

$usuario = $_POST["usuario"];
$password = password_hash($_POST["password"], PASSWORD_DEFAULT);

$stmt = $conexion->prepare("INSERT INTO usuarios (usuario, password) VALUES (?, ?)");
$stmt->bind_param("ss", $usuario, $password);

if ($stmt->execute()) {
    echo "Datos guardados correctamente";
} else {
    echo "Error al guardar";
}
?>