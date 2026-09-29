<?php

$conexion = new mysqli("localhost", "root", "", "vintage");

if ($conexion->connect_error) {
    die("Error de conexión");
}

?>