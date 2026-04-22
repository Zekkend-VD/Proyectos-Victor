<?php
session_start();
// ... (Toda la lógica PHP de cliente.php se mantiene igual) ...
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'cliente') {
    header('Location: index.php');
    exit;
}

$message = '';
$search_query = $_GET['search'] ?? '';

// Lógica de COMPRA (Se mantiene igual)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy_product_id'])) {
    $product_id = $_POST['buy_product_id'];
    $quantity = 1; 

    $inventario_lines = file('inventario.txt', FILE_IGNORE_NEW_LINES);
    $updated_inventario = [];
    $product_found = false;
    $purchase_details = null;

    foreach ($inventario_lines as $line) {
        $data = explode('|', $line);
        if (count($data) >= 6 && (int)$data[0] === (int)$product_id) { 
            if ((int)$data[4] >= $quantity) {
                $data[4] = (int)$data[4] - $quantity;
                $product_found = true;
                $purchase_details = ['id' => $data[0], 'name' => $data[1], 'price' => (float)$data[3]];
            } else {
                $message = 'Stock insuficiente para el juego: ' . (isset($data[1]) ? $data[1] : 'Desconocido');
            }
        }
        $updated_inventario[] = implode('|', $data);
    }

    if ($product_found && $purchase_details) {
        file_put_contents('inventario.txt', implode("\n", $updated_inventario) . "\n");
        $total_cost = $purchase_details['price'] * $quantity;
        $date = date('Y-m-d H:i:s');
        $client_id = $_SESSION['user_id'];
        $venta_line = "$client_id|$date|{$purchase_details['id']}:{$purchase_details['name']}:$quantity:{$purchase_details['price']}|$total_cost\n";
        file_put_contents('ventas.txt', $venta_line, FILE_APPEND);

        $message = "¡Juego '{$purchase_details['name']}' comprado con éxito! Total: $" . number_format($total_cost, 2);
    }
}


// Cargar inventario para mostrar (con filtro de búsqueda y manejo de imagen)
$inventario = [];
$inventario_lines = file_exists('inventario.txt') ? file('inventario.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
$search_query_lower = strtolower($search_query);

foreach ($inventario_lines as $line) {
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
            $inventario[] = $product;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cliente - Catálogo de Juegos PS5</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <header class="header-main">
        <h1>🎮 Catálogo de Juegos PS5</h1>
        <p>Bienvenido, **<?php echo $_SESSION['user_name']; ?>**. ¡Explora los últimos lanzamientos!</p>
        <a href="logout.php" class="btn logout-btn">Cerrar Sesión</a>
    </header>

    <?php if ($message): ?><p class="<?php echo strpos($message, 'éxito') !== false ? 'success' : 'error'; ?>"><?php echo $message; ?></p><?php endif; ?>

    <hr>
    
    <section class="search-section form-card">
        <h3>Buscar Juegos (<?php echo count($inventario); ?> resultados)</h3>
        <form method="GET" style="padding: 0; border: none; box-shadow: none; margin-bottom: 0;">
            <div style="display: flex; gap: 10px;">
                <input type="text" id="search" name="search" placeholder="Escribe nombre o descripción..." value="<?php echo htmlspecialchars($search_query); ?>" style="flex-grow: 1; margin-bottom: 0;">
                <input type="submit" value="Buscar" style="width: 120px;">
            </div>
        </form>
    </section>
    
    <hr>

    <section class="catalog-grid">
        <div class="product-grid">
            <?php foreach ($inventario as $juego): ?>
                <div class="product-card">
                    <img src="uploads/<?php echo htmlspecialchars($juego['image']); ?>" alt="<?php echo htmlspecialchars($juego['name']); ?>" onerror="this.onerror=null;this.src='uploads/no_image.png';">
                    
                    <div class="card-details">
                        <h3 class="game-title"><?php echo $juego['name']; ?></h3>
                        <p class="description-text"><?php echo $juego['description']; ?></p>
                        <div class="price-stock-info">
                            <p class="price-tag">$<?php echo number_format($juego['price'], 2); ?></p>
                            <p class="stock-status">Stock: **<?php echo $juego['stock']; ?>**</p>
                        </div>
                    </div>
                    
                    <div class="card-actions">
                        <?php if ($juego['stock'] > 0): ?>
                            <form method="POST" style="padding: 0; border: none; box-shadow: none;">
                                <input type="hidden" name="buy_product_id" value="<?php echo $juego['id']; ?>">
                                <input type="submit" value="Comprar 🛒" class="btn" style="width: 100%; margin-top: 10px;">
                            </form>
                        <?php else: ?>
                            <span class="error" style="display: block; text-align: center; padding: 10px; margin-top: 10px;">AGOTADO</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($inventario)): ?>
                <p class="error" style="grid-column: 1 / -1; padding: 20px;">No se encontraron juegos que coincidan con la búsqueda.</p>
            <?php endif; ?>
        </div>
    </section>
</div>
</body>
</html>