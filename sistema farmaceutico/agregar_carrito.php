<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $medicamento_id = $_POST['medicamento_id'];
    $cantidad = $_POST['cantidad'];
    
    // Verificar si el medicamento ya está en el carrito
    $sql_verificar = "SELECT * FROM carrito WHERE usuario_id = $usuario_id AND medicamento_id = $medicamento_id";
    $result_verificar = $conexion->query($sql_verificar);
    
    if ($result_verificar->num_rows > 0) {
        // Actualizar la cantidad
        $item = $result_verificar->fetch_assoc();
        $nueva_cantidad = $item['cantidad'] + $cantidad;
        $sql = "UPDATE carrito SET cantidad = $nueva_cantidad WHERE id = " . $item['id'];
    } else {
        // Insertar nuevo item
        $sql = "INSERT INTO carrito (usuario_id, medicamento_id, cantidad) VALUES ($usuario_id, $medicamento_id, $cantidad)";
    }
    
    if ($conexion->query($sql) === TRUE) {
        header('Location: carrito.php');
    } else {
        echo "Error: " . $conexion->error;
    }
}
?>