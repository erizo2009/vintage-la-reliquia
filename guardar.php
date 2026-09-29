<?php

include("conexion.php");

$id = $_POST["id"];
$usuario = $_POST["usuario"];
$contraseña = $_POST["contraseña"];

$sql = "INSERT INTO usuarios (id, usuario, contraseña)
VALUES('$id','$usuario','$contraseña')";

if($conexion->query($sql)){
    echo "Datos guardados correctamente";
}else{
    echo "Error al guardar";
}

?>