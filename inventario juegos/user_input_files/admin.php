<?php
session_start();
include 'config.php';
include 'upload_image.php';

// Verificar si es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$message = '';
$search_query = $_GET['search'] ?? ''; 

// --- Lógica para AGREGAR NUEVO PRODUCTO (Se mantiene) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_name'])) {
    $name = $_POST['new_name'];
    $desc = $_POST['new_desc'];
    $price = (float)$_POST['new_price'];
    $stock = (int)$_POST['new_stock'];
    $image_filename = 'no_image.png'; 

    if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = upload_product_image('new_image');
        if ($upload_result['success']) {
            $image_filename = $upload_result['filename'];
        } else {
            $message = "Error al subir imagen: " . $upload_result['message'];
        }
    }

    if (empty($message) || strpos($message, 'Error al subir imagen') === 0) {
        $inventario_lines = file_exists('inventario.txt') ? file('inventario.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
        $last_id = 0;
        foreach ($inventario_lines as $line) {
            $data = explode('|', $line);
            $last_id = max($last_id, (int)(isset($data[0]) ? $data[0] : 0));
        }
        $new_id = $last_id + 1;
        $new_product_line = "$new_id|$name|$desc|$price|$stock|$image_filename\n";
        file_put_contents('inventario.txt', $new_product_line, FILE_APPEND);
        if (empty($message)) {
            $message = "Juego '$name' agregado con éxito (ID: $new_id).";
        } else {
            $message .= " Juego '$name' agregado, pero con advertencia de imagen.";
        }
    }
}

// --- Lógica para RECARGAR STOCK (Se mantiene) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reload_id'])) {
    $reload_id = (int)$_POST['reload_id'];
    $add_quantity = (int)$_POST['add_quantity'];
    $inventario_lines = file('inventario.txt', FILE_IGNORE_NEW_LINES);
    $updated_inventario = [];
    $product_found = false;

    foreach ($inventario_lines as $line) {
        $data = explode('|', $line);
        if (count($data) >= 6 && (int)$data[0] === $reload_id) {
            if ($add_quantity > 0) {
                $data[4] = (int)$data[4] + $add_quantity;
                $product_found = true;
                $message = "Stock del juego '{$data[1]}' recargado. Cantidad agregada: $add_quantity.";
            } else {
                $message = "La cantidad a recargar debe ser positiva.";
            }
        }
        $updated_inventario[] = implode('|', $data);
    }

    if ($product_found) {
        file_put_contents('inventario.txt', implode("\n", $updated_inventario) . "\n");
    } elseif ($add_quantity > 0) {
        $message = "Error: Juego con ID $reload_id no encontrado.";
    }
}


// --- Funciones para cargar datos (Se mantienen con 6 columnas) ---
function get_inventario($search_query) {
    $inventario = [];
    $lines = file_exists('inventario.txt') ? file('inventario.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    $search_query_lower = strtolower($search_query);
    
    foreach ($lines as $line) {
        $data = explode('|', $line);
        if (count($data) >= 6) { 
            $product = ['id' => $data[0], 'name' => $data[1], 'description' => $data[2], 'price' => (float)$data[3], 'stock' => (int)$data[4], 'image' => $data[5]];
            
            $product_info = strtolower(implode(' ', array_slice($product, 0, 5)));
            if (empty($search_query) || strpos($product_info, $search_query_lower) !== false) {
                $inventario[$data[0]] = $product;
            }
        }
    }
    return $inventario;
}

function get_clientes() {
    $clientes = [];
    $lines = file_exists('clientes.txt') ? file('clientes.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    foreach ($lines as $line) {
        $data = explode('|', $line);
        if (count($data) >= 5) {
            $clientes[$data[0]] = ['name' => $data[1], 'email' => $data[2], 'role' => $data[4]];
        }
    }
    return $clientes;
}

$clientes_data = get_clientes();
$inventario_data = get_inventario($search_query); 

// Cargar datos de ventas (Se mantiene)
$ventas = [];
$ventas_lines = file_exists('ventas.txt') ? file('ventas.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
foreach ($ventas_lines as $line) {
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
<head><meta charset="UTF-8"><title>Admin - Panel de Juegos PS5</title><link rel="stylesheet" href="styles.css"></head>
<body>
<div class="container">
    <a href="logout.php" class="btn logout-btn">Cerrar Sesión</a>
    <h1>🕹️ Panel de Administración - Juegos PS5</h1>
    <?php if ($message): ?><p class="<?php echo strpos($message, 'Error') === false ? 'success' : 'error'; ?>"><?php echo $message; ?></p><?php endif; ?>

    <section class="admin-forms product-grid" style="grid-template-columns: 1fr 1fr; gap: 30px;">
        <div class="form-card">
            <h2>1. Recargar Stock Existente</h2>
            <form method="POST">
                <label for="reload_id">ID del Juego:</label>
                <input type="number" id="reload_id" name="reload_id" required>
                <label for="add_quantity">Cantidad a agregar:</label>
                <input type="number" id="add_quantity" name="add_quantity" required>
                <input type="submit" value="Recargar Stock">
            </form>
        </div>
        
        <div class="form-card">
            <h2>2. Agregar Nuevo Juego</h2>
            <form method="POST" enctype="multipart/form-data">
                <label for="new_name">Nombre del Juego:</label>
                <input type="text" id="new_name" name="new_name" required>
                <label for="new_price">Precio:</label>
                <input type="number" step="0.01" id="new_price" name="new_price" required>
                <label for="new_stock">Stock Inicial:</label>
                <input type="number" id="new_stock" name="new_stock" required>
                <label for="new_image">Imagen:</label>
                <input type="file" id="new_image" name="new_image" accept="image/*">
                <label for="new_desc">Descripción Breve:</label>
                <textarea id="new_desc" name="new_desc" required></textarea>
                <input type="submit" value="Añadir Juego">
            </form>
        </div>
    </section>
    
    <hr>
    
    <section class="inventory-section">
        <h2>3. Inventario de Juegos (<?php echo count($inventario_data); ?> resultados)</h2>
        <form method="GET" style="padding: 0; margin-bottom: 20px; border: none; box-shadow: none;">
            <input type="text" id="search" name="search" placeholder="Buscar por ID, nombre o descripción..." value="<?php echo htmlspecialchars($search_query); ?>" style="width: calc(100% - 130px); display: inline-block; margin-right: 10px;">
            <input type="submit" value="Buscar" style="width: 120px; vertical-align: top;">
        </form>

        <table>
            <thead><tr><th>ID</th><th>Imagen</th><th>Nombre</th><th>Precio</th><th>Stock Actual</th></tr></thead>
            <tbody>
                <?php foreach ($inventario_data as $id => $prod): ?>
                    <tr>
                        <td><?php echo $id; ?></td>
                        <td><img src="uploads/<?php echo htmlspecialchars($prod['image']); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"></td>
                        <td><?php echo $prod['name']; ?></td>
                        <td>$<?php echo number_format($prod['price'], 2); ?></td>
                        <td><?php echo $prod['stock']; ?></td>
                    </tr>
                <?php endforeach; ?>
                 <?php if (empty($inventario_data)): ?>
                    <tr><td colspan="5" class="error">No se encontraron juegos que coincidan con la búsqueda.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <hr>
    
    <section class="data-history-grid product-grid" style="grid-template-columns: 2fr 1fr; gap: 30px;">
        <div>
            <h2>4. Historial de Compras</h2>
            <table>
                <thead>
                    <tr><th>Cliente</th><th>Juego Comprado</th><th>Cantidad</th><th>Total Gastado</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($ventas as $venta): ?>
                        <tr>
                            <td><?php echo $venta['client_name']; ?></td>
                            <td><?php echo $venta['product_name']; ?></td>
                            <td><?php echo $venta['quantity']; ?></td>
                            <td>$<?php echo number_format($venta['total_cost'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div>
            <h2>5. Usuarios Registrados</h2>
            <table>
                <thead><tr><th>ID</th><th>Nombre</th><th>Rol</th></tr></thead>
                <tbody>
                    <?php 
                    $admin_row = [1 => ['name' => 'Administrador', 'email' => ADMIN_EMAIL, 'role' => 'admin']];
                    $all_users = $admin_row + $clientes_data;
                    foreach ($all_users as $id => $cliente): 
                    ?>
                        <tr>
                            <td><?php echo $id; ?></td>
                            <td><?php echo $cliente['name']; ?></td>
                            <td><?php echo $cliente['role']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <h2 style="margin-top: 30px;">6. Exportar Datos</h2>
            <div style="display: flex; gap: 10px; flex-direction: column;">
                <a href="clientes.txt" download class="btn">Clientes.txt</a>
                <a href="inventario.txt" download class="btn">Inventario.txt</a>
                <a href="ventas.txt" download class="btn">Ventas.txt</a>
            </div>
        </div>
    </section>
</div>
</body>
</html>