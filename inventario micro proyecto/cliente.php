<?php
session_start();
// Verificar si es cliente
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'cliente') {
    header('Location: index.php');
    exit;
}

$message = '';

// Lógica de COMPRA
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy_product_id'])) {
    $product_id = $_POST['buy_product_id'];
    $quantity = 1; // Simplificado a una unidad por compra

    $inventario_lines = file('inventario.txt', FILE_IGNORE_NEW_LINES);
    $updated_inventario = [];
    $product_found = false;
    $purchase_details = null;

    // 1. Procesar la compra y actualizar stock
    foreach ($inventario_lines as $line) {
        $data = explode('|', $line);
        // ID|Nombre|Descripción|Precio|Stock
        if (isset($data[0]) && (int)$data[0] === (int)$product_id) {
            if (isset($data[4]) && (int)$data[4] >= $quantity) {
                $data[4] = (int)$data[4] - $quantity; // Restar stock
                $product_found = true;
                $purchase_details = [
                    'id' => $data[0],
                    'name' => $data[1],
                    'price' => (float)$data[3]
                ];
            } else {
                $message = 'Stock insuficiente para el producto: ' . (isset($data[1]) ? $data[1] : 'Desconocido');
            }
        }
        $updated_inventario[] = implode('|', $data);
    }

    if ($product_found && $purchase_details) {
        // 2. Guardar el nuevo inventario
        file_put_contents('inventario.txt', implode("\n", $updated_inventario) . "\n");

        // 3. Registrar la venta
        $total_cost = $purchase_details['price'] * $quantity;
        $date = date('Y-m-d H:i:s');
        $client_id = $_SESSION['user_id'];
        
        $venta_line = "$client_id|$date|{$purchase_details['id']}:{$purchase_details['name']}:$quantity:{$purchase_details['price']}|$total_cost\n";
        file_put_contents('ventas.txt', $venta_line, FILE_APPEND);

        $message = "¡Compra realizada con éxito! Total: $" . number_format($total_cost, 2);
    }
}

// Cargar inventario para mostrar
$inventario = [];
$inventario_lines = file_exists('inventario.txt') ? file('inventario.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
foreach ($inventario_lines as $line) {
    $data = explode('|', $line);
    if (count($data) >= 5) {
        $inventario[] = [
            'id' => $data[0],
            'name' => $data[1],
            'description' => $data[2],
            'price' => (float)$data[3],
            'stock' => (int)$data[4]
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Cliente - Productos</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <a href="logout.php" class="btn logout-btn">Cerrar Sesión</a>
    <h1>Bienvenido, <?php echo $_SESSION['user_name']; ?> (Cliente)</h1>
    <?php if ($message): ?><p class="<?php echo strpos($message, 'éxito') !== false ? 'success' : 'error'; ?>"><?php echo $message; ?></p><?php endif; ?>

    <h2>Productos Disponibles</h2>
    <table>
        <thead>
            <tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Precio</th><th>Stock</th><th>Acción</th></tr>
        </thead>
        <tbody>
            <?php foreach ($inventario as $producto): ?>
                <tr>
                    <td><?php echo $producto['id']; ?></td>
                    <td><?php echo $producto['name']; ?></td>
                    <td><?php echo $producto['description']; ?></td>
                    <td>$<?php echo number_format($producto['price'], 2); ?></td>
                    <td><?php echo $producto['stock']; ?></td>
                    <td>
                        <?php if ($producto['stock'] > 0): ?>
                            <form method="POST">
                                <input type="hidden" name="buy_product_id" value="<?php echo $producto['id']; ?>">
                                <input type="submit" value="Comprar (1 unidad)">
                            </form>
                        <?php else: ?>
                            <span class="error">Agotado</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>