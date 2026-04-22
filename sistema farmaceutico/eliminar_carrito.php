<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$id = intval($_GET['id']);
$usuario_id = $_SESSION['usuario_id'];

// Verificar que el item del carrito pertenece al usuario
$sql = "DELETE FROM carrito WHERE id = $id AND usuario_id = $usuario_id";
if ($conexion->query($sql) === TRUE) {
    header('Location: carrito.php');
} else {
    echo "Error al eliminar: " . $conexion->error;
}
?>