<?php
session_start();
include 'config.php'; // Incluye el archivo de configuración
// Verificar si es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$message = '';

// --- Lógica para AGREGAR NUEVO PRODUCTO (Se mantiene) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_name'])) {
    $name = $_POST['new_name'];
    $desc = $_POST['new_desc'];
    $price = (float)$_POST['new_price'];
    $stock = (int)$_POST['new_stock'];

    // ... (Lógica para obtener nuevo ID y añadir al inventario.txt) ...
    $inventario_lines = file_exists('inventario.txt') ? file('inventario.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    $last_id = 0;
    foreach ($inventario_lines as $line) {
        $data = explode('|', $line);
        $last_id = max($last_id, (int)(isset($data[0]) ? $data[0] : 0));
    }
    $new_id = $last_id + 1;
    $new_product_line = "$new_id|$name|$desc|$price|$stock\n";
    file_put_contents('inventario.txt', $new_product_line, FILE_APPEND);
    $message = "Producto agregado con éxito (ID: $new_id).";
}

// --- Lógica para RECARGAR STOCK (Nuevo) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reload_id'])) {
    $reload_id = (int)$_POST['reload_id'];
    $add_quantity = (int)$_POST['add_quantity'];
    $inventario_lines = file('inventario.txt', FILE_IGNORE_NEW_LINES);
    $updated_inventario = [];
    $product_found = false;

    foreach ($inventario_lines as $line) {
        $data = explode('|', $line);
        if (isset($data[0]) && (int)$data[0] === $reload_id) {
            if ($add_quantity > 0) {
                // ID|Nombre|Descripción|Precio|Stock
                $data[4] = (int)$data[4] + $add_quantity; // Sumar stock
                $product_found = true;
                $message = "Stock de '{$data[1]}' recargado. Cantidad agregada: $add_quantity.";
            } else {
                $message = "La cantidad a recargar debe ser positiva.";
            }
        }
        $updated_inventario[] = implode('|', $data);
    }

    if ($product_found) {
        // Guardar el inventario actualizado
        file_put_contents('inventario.txt', implode("\n", $updated_inventario) . "\n");
    } elseif ($add_quantity > 0) {
        $message = "Error: Producto con ID $reload_id no encontrado.";
    }
}


// --- Funciones para cargar datos (Se mantienen) ---
function get_clientes() {
    $clientes = [];
    // Nota: El administrador se maneja con config.php, por eso se excluye.
    $lines = file_exists('clientes.txt') ? file('clientes.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    foreach ($lines as $line) {
        $data = explode('|', $line);
        if (count($data) >= 5) {
            $clientes[$data[0]] = ['name' => $data[1], 'email' => $data[2], 'role' => $data[4]];
        }
    }
    return $clientes;
}

function get_inventario() {
    $inventario = [];
    $lines = file_exists('inventario.txt') ? file('inventario.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    foreach ($lines as $line) {
        $data = explode('|', $line);
        if (count($data) >= 5) {
            $inventario[$data[0]] = ['name' => $data[1], 'price' => (float)$data[3], 'stock' => (int)$data[4]];
        }
    }
    return $inventario;
}

$clientes_data = get_clientes();
$inventario_data = get_inventario();

// Cargar datos de ventas (Se mantiene)
$ventas = [];
$ventas_lines = file_exists('ventas.txt') ? file('ventas.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
foreach ($ventas_lines as $line) {
    // ... (Lógica de carga de ventas) ...
    $data = explode('|', $line);
    if (count($data) >= 4) {
        $client_id = $data[0];
        $date = $data[1];
        $product_details_str = $data[2];
        $total_cost = (float)$data[3];

        $p_details = explode(':', $product_details_str);
        if (count($p_details) >= 4) {
            list($p_id, $p_name, $p_qty, $p_price) = $p_details;

            $ventas[] = [
                'client_name' => $clientes_data[$client_id]['name'] ?? 'Desconocido',
                'product_name' => $p_name,
                'quantity' => (int)$p_qty,
                'unit_price' => (float)$p_price,
                'total_cost' => $total_cost,
                'date' => $date
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Panel de Administración</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container">
    <a href="logout.php" class="btn logout-btn">Cerrar Sesión</a>
    <h1>Panel de Administración</h1>
    <?php if ($message): ?><p class="<?php echo strpos($message, 'Error') === false ? 'success' : 'error'; ?>"><?php echo $message; ?></p><?php endif; ?>

    <h2>1. Recargar Stock Existente (Comprar más unidades)</h2>
    <form method="POST">
        <label for="reload_id">ID del Producto a recargar:</label>
        <input type="number" id="reload_id" name="reload_id" required>
        <label for="add_quantity">Cantidad a agregar:</label>
        <input type="number" id="add_quantity" name="add_quantity" required>
        <input type="submit" value="Recargar Stock">
    </form>
    
    <hr>
    
    <h2>2. Inventario de Productos Actual</h2>
    <table>
        <thead><tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Stock Actual</th></tr></thead>
        <tbody>
            <?php foreach ($inventario_data as $id => $prod): ?>
                <tr>
                    <td><?php echo $id; ?></td>
                    <td><?php echo $prod['name']; ?></td>
                    <td>$<?php echo number_format($prod['price'], 2); ?></td>
                    <td><?php echo $prod['stock']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <hr>
    
    <h2>3. Agregar Nuevo Producto</h2>
    <form method="POST">
        <label for="new_name">Nombre:</label>
        <input type="text" id="new_name" name="new_name" required>
        <label for="new_desc">Descripción:</label>
        <textarea id="new_desc" name="new_desc" required></textarea>
        <label for="new_price">Precio:</label>
        <input type="number" step="0.01" id="new_price" name="new_price" required>
        <label for="new_stock">Stock Inicial:</label>
        <input type="number" id="new_stock" name="new_stock" required>
        <input type="submit" value="Añadir Producto">
    </form>
    
    <hr>

    <h2>4. Historial de Compras y Clientes</h2>
    <table>
        <thead>
            <tr><th>Cliente</th><th>Producto</th><th>Cantidad</th><th>Precio Unitario</th><th>Total Gastado</th><th>Fecha</th></tr>
        </thead>
        <tbody>
            <?php foreach ($ventas as $venta): ?>
                <tr>
                    <td><?php echo $venta['client_name']; ?></td>
                    <td><?php echo $venta['product_name']; ?></td>
                    <td><?php echo $venta['quantity']; ?></td>
                    <td>$<?php echo number_format($venta['unit_price'], 2); ?></td>
                    <td>$<?php echo number_format($venta['total_cost'], 2); ?></td>
                    <td><?php echo $venta['date']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Lista de Clientes Registrados</h3>
    <table>
        <thead><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th></tr></thead>
        <tbody>
            <?php 
            // Añadir manualmente el administrador a la lista de clientes para visualización
            $admin_row = [1 => ['name' => 'Administrador', 'email' => ADMIN_EMAIL, 'role' => 'admin']];
            $all_users = $admin_row + $clientes_data;
            foreach ($all_users as $id => $cliente): 
            ?>
                <tr>
                    <td><?php echo $id; ?></td>
                    <td><?php echo $cliente['name']; ?></td>
                    <td><?php echo $cliente['email']; ?></td>
                    <td><?php echo $cliente['role']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <hr>
    
    <h2>5. Exportar Bases de Datos (.txt)</h2>
    <p>Haga clic para descargar el archivo de datos:</p>
    <a href="clientes.txt" download class="btn">Exportar Clientes.txt</a>
    <a href="inventario.txt" download class="btn">Exportar Inventario.txt</a>
    <a href="ventas.txt" download class="btn">Exportar Ventas.txt</a>
</div>
</body>
</html>