<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener los items del carrito
$sql = "SELECT c.medicamento_id, c.cantidad, m.precio, m.stock 
        FROM carrito c 
        JOIN medicamentos m ON c.medicamento_id = m.id 
        WHERE c.usuario_id = $usuario_id";
$result = $conexion->query($sql);

$total = 0;
$items = [];
while ($item = $result->fetch_assoc()) {
    // Verificar stock
    if ($item['stock'] < $item['cantidad']) {
        die("No hay suficiente stock para: " . $item['nombre']);
    }
    $subtotal = $item['precio'] * $item['cantidad'];
    $total += $subtotal;
    $items[] = $item;
}

// Iniciar transacción
$conexion->begin_transaction();

try {
    // Insertar la compra
    $sql = "INSERT INTO compras (usuario_id, total) VALUES ($usuario_id, $total)";
    $conexion->query($sql);
    $compra_id = $conexion->insert_id;

    // Insertar detalles y actualizar stock
    foreach ($items as $item) {
        $medicamento_id = $item['medicamento_id'];
        $cantidad = $item['cantidad'];
        $precio = $item['precio'];

        $sql = "INSERT INTO compras_detalle (compra_id, medicamento_id, cantidad, precio_unitario) 
                VALUES ($compra_id, $medicamento_id, $cantidad, $precio)";
        $conexion->query($sql);

        // Actualizar stock
        $sql = "UPDATE medicamentos SET stock = stock - $cantidad WHERE id = $medicamento_id";
        $conexion->query($sql);
    }

    // Vaciar carrito
    $sql = "DELETE FROM carrito WHERE usuario_id = $usuario_id";
    $conexion->query($sql);

    $conexion->commit();
    $mensaje = "¡Compra realizada con éxito!";
} catch (Exception $e) {
    $conexion->rollback();
    $error = "Error al confirmar la compra: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Compra - Farmacia Online</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Farmacia Online</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="carrito.php">Carrito</a></li>
                    <li><a href="logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2>Confirmación de Compra</h2>
        
        <?php if (isset($mensaje)): ?>
            <div class="mensaje-exito">
                <p><?php echo $mensaje; ?></p>
                <p>Número de pedido: #<?php echo $compra_id; ?></p>
                <a href="index.php" class="btn">Volver a la tienda</a>
            </div>
        <?php elseif (isset($error)): ?>
            <div class="error">
                <p><?php echo $error; ?></p>
                <a href="carrito.php" class="btn">Volver al carrito</a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>