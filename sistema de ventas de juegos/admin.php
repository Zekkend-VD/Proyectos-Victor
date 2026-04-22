<?php
session_start();
include 'config.php';
include 'inventario_safe_functions.php';

// Verificar si es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$message = '';
$search_query = $_GET['search'] ?? ''; 

// --- Lógica para AGREGAR NUEVO PRODUCTO ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_name'])) {
    $name = trim($_POST['new_name']);
    $desc = trim($_POST['new_desc']);
    $price = $_POST['new_price'];
    $stock = $_POST['new_stock'];
    $image_filename = 'no_image.png'; // Imagen por defecto

    // Validaciones usando función segura
    $validation_errors = validate_product_data($name, $desc, $price, $stock);
    
    if (!empty($validation_errors)) {
        $message = "❌ Error: " . implode(', ', $validation_errors);
    } else {
        $price = (float)$price;
        $stock = (int)$stock;
        
        $inventario_lines = safe_read_inventario();
        $last_id = 100; // ID inicial
        
        foreach ($inventario_lines as $line) {
            $data = explode('|', $line);
            $last_id = max($last_id, (int)(isset($data[0]) ? $data[0] : 0));
        }
        $new_id = $last_id + 1;
        
        $new_product_line = "$new_id|$name|$desc|$price|$stock|$image_filename";
        $inventario_lines[] = $new_product_line;
        
        if (safe_write_inventario($inventario_lines)) {
            $message = "✅ Juego '$name' agregado con éxito (ID: $new_id).";
        } else {
            $message = "❌ Error al guardar el producto. Inténtalo de nuevo.";
        }
    }
}

// --- Lógica para RECARGAR STOCK ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reload_id'])) {
    $reload_id = (int)$_POST['reload_id'];
    $add_quantity = (int)$_POST['add_quantity'];
    
    if ($add_quantity <= 0) {
        $message = "❌ La cantidad a recargar debe ser positiva.";
    } else {
        $inventario_lines = file('inventario.txt', FILE_IGNORE_NEW_LINES);
        $updated_inventario = [];
        $product_found = false;

        foreach ($inventario_lines as $line) {
            $data = explode('|', $line);
            if (count($data) >= 6 && (int)$data[0] === $reload_id) {
                $data[4] = (int)$data[4] + $add_quantity;
                $product_found = true;
                $message = "✅ Stock del juego '{$data[1]}' recargado. Cantidad agregada: $add_quantity.";
            }
            $updated_inventario[] = implode('|', $data);
        }

        if ($product_found) {
            file_put_contents('inventario.txt', implode("\n", $updated_inventario) . "\n");
        } else {
            $message = "❌ Error: Juego con ID $reload_id no encontrado.";
        }
    }
}

// --- Funciones para cargar datos ---
function get_inventario($search_query) {
    $inventario = [];
    $lines = file_exists('inventario.txt') ? file('inventario.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    $search_query_lower = strtolower($search_query);
    
    foreach ($lines as $line) {
        $data = explode('|', $line);
        if (count($data) >= 6) { 
            $product = [
                'id' => $data[0], 
                'name' => $data[1], 
                'description' => $data[2], 
                'price' => (float)$data[3], 
                'stock' => (int)$data[4], 
                'image' => $data[5]
            ];
            
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

// Cargar datos de ventas
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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Panel de Juegos PS5</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container fade-in-up">
        <header class="header-main">
            <div>
                <h1>🎮 Panel de Administración</h1>
                <p>Gestiona tu inventario de juegos PS5</p>
            </div>
            <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
        </header>

        <?php if ($message): ?>
            <div class="alert <?php echo strpos($message, '✅') !== false ? 'alert-success' : 'alert-error'; ?>">
                <span class="alert-icon"><?php echo strpos($message, '✅') !== false ? '✅' : '⚠️'; ?></span>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="admin-grid">
            <!-- Recargar Stock -->
            <div class="form-card">
                <h2>🔄 Recargar Stock</h2>
                <form method="POST">
                    <label for="reload_id">ID del Juego:</label>
                    <input type="number" id="reload_id" name="reload_id" required min="1">
                    
                    <label for="add_quantity">Cantidad a agregar:</label>
                    <input type="number" id="add_quantity" name="add_quantity" required min="1">
                    
                    <button type="submit" class="btn">Recargar Stock</button>
                </form>
            </div>
            
            <!-- Agregar Nuevo Juego -->
            <div class="form-card">
                <h2>➕ Agregar Nuevo Juego</h2>
                <form method="POST">
                    <label for="new_name">Nombre del Juego:</label>
                    <input type="text" id="new_name" name="new_name" required>
                    
                    <label for="new_desc">Descripción:</label>
                    <textarea id="new_desc" name="new_desc" required rows="3"></textarea>
                    
                    <label for="new_price">Precio ($):</label>
                    <input type="number" step="0.01" id="new_price" name="new_price" required min="0.01">
                    
                    <label for="new_stock">Stock Inicial:</label>
                    <input type="number" id="new_stock" name="new_stock" required min="0">
                    
                    <button type="submit" class="btn">Añadir Juego</button>
                </form>
            </div>
        </div>
        
        <!-- Búsqueda de Inventario -->
        <section class="search-section">
            <div class="form-card">
                <h2>🔍 Buscar en Inventario (<?php echo count($inventario_data); ?> resultados)</h2>
                <form method="GET">
                    <div style="display: flex; gap: 1rem; align-items: end;">
                        <div style="flex: 1;">
                            <label for="search">Buscar por ID, nombre o descripción:</label>
                            <input type="text" id="search" name="search" 
                                   placeholder="Escribe aquí para buscar..." 
                                   value="<?php echo htmlspecialchars($search_query); ?>">
                        </div>
                        <button type="submit" class="btn" style="height: fit-content;">Buscar</button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Tabla de Inventario -->
        <section>
            <h2>📦 Inventario Actual</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($inventario_data)): ?>
                            <tr>
                                <td colspan="6" class="text-center error">
                                    No se encontraron juegos que coincidan con la búsqueda.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($inventario_data as $id => $prod): ?>
                                <tr>
                                    <td><strong><?php echo $id; ?></strong></td>
                                    <td>
                                        <img src="uploads/<?php echo htmlspecialchars($prod['image']); ?>" 
                                            alt="<?php echo htmlspecialchars($prod['name']); ?>" 
                                            style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"
                                            onerror="this.src='uploads/dcu623d-8e66eb73-81d8-4549-aa50-6ca9a4ad86c5.jpg';">
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($prod['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($prod['description']); ?></td>
                                    <td class="price-tag">$<?php echo number_format($prod['price'], 2); ?></td>
                                    <td>
                                        <span class="<?php echo $prod['stock'] > 0 ? 'success' : 'error'; ?>">
                                            <?php echo $prod['stock']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <div class="admin-grid mt-lg">
            <!-- Historial de Compras -->
            <div class="form-card" style="grid-column: 1 / -1;">
                <h2>📊 Historial de Compras Recientes</h2>
                <?php if (empty($ventas)): ?>
                    <p class="text-center">No hay ventas registradas aún.</p>
                <?php else: ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Juego</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Total</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($ventas, -10) as $venta): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($venta['client_name']); ?></td>
                                        <td><?php echo htmlspecialchars($venta['product_name']); ?></td>
                                        <td><?php echo $venta['quantity']; ?></td>
                                        <td>$<?php echo number_format($venta['unit_price'], 2); ?></td>
                                        <td class="price-tag">$<?php echo number_format($venta['total_cost'], 2); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($venta['date'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Usuarios Registrados -->
            <div class="form-card">
                <h2>👥 Usuarios del Sistema</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Rol</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Administrador</td>
                                <td><span class="error">admin</span></td>
                            </tr>
                            <?php foreach ($clientes_data as $id => $cliente): ?>
                                <tr>
                                    <td><?php echo $id; ?></td>
                                    <td><?php echo htmlspecialchars($cliente['name']); ?></td>
                                    <td><span class="success"><?php echo $cliente['role']; ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Exportar Datos -->
            <div class="form-card">
                <h2>📥 Exportar Datos</h2>
                <p>Descarga los archivos de datos del sistema:</p>
                <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;">
                    <a href="clientes.txt" download class="btn">📄 Descargar Clientes</a>
                    <a href="inventario.txt" download class="btn">📦 Descargar Inventario</a>
                    <a href="ventas.txt" download class="btn">💰 Descargar Ventas</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>