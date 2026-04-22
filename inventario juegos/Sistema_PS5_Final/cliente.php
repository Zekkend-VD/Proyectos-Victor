<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'cliente') {
    header('Location: index.php');
    exit;
}

$message = '';
$search_query = $_GET['search'] ?? '';

// Lógica de COMPRA
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy_product_id'])) {
    $product_id = (int)$_POST['buy_product_id'];
    $quantity = 1; 

    $inventario_lines = file('inventario.txt', FILE_IGNORE_NEW_LINES);
    $updated_inventario = [];
    $product_found = false;
    $purchase_details = null;

    foreach ($inventario_lines as $line) {
        $data = explode('|', $line);
        if (count($data) >= 6 && (int)$data[0] === $product_id) { 
            if ((int)$data[4] >= $quantity) {
                $data[4] = (int)$data[4] - $quantity;
                $product_found = true;
                $purchase_details = [
                    'id' => $data[0], 
                    'name' => $data[1], 
                    'price' => (float)$data[3]
                ];
            } else {
                $message = '❌ Stock insuficiente para el juego: ' . htmlspecialchars($data[1]);
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

        $message = "🎉 ¡Juego '{$purchase_details['name']}' comprado con éxito! Total: $" . number_format($total_cost, 2);
    }
}

// Cargar inventario para mostrar
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

// Cargar historial de compras del usuario
$mis_compras = [];
$ventas_lines = file_exists('ventas.txt') ? file('ventas.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
foreach ($ventas_lines as $line) {
    $data = explode('|', $line);
    if (count($data) >= 4 && $data[0] == $_SESSION['user_id']) {
        $product_details_str = $data[2];
        $p_details = explode(':', $product_details_str);
        if (count($p_details) >= 4) {
            $mis_compras[] = [
                'date' => $data[1],
                'product_name' => $p_details[1],
                'quantity' => (int)$p_details[2],
                'unit_price' => (float)$p_details[3],
                'total_cost' => (float)$data[3]
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
    <title>Catálogo PS5 - <?php echo htmlspecialchars($_SESSION['user_name']); ?></title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container fade-in-up">
        <header class="header-main">
            <div>
                <h1>🎮 Catálogo de Juegos PS5</h1>
                <p>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong> 🎯</p>
            </div>
            <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
        </header>

        <?php if ($message): ?>
            <div class="alert <?php echo strpos($message, '🎉') !== false ? 'alert-success' : 'alert-error'; ?>">
                <span class="alert-icon"><?php echo strpos($message, '🎉') !== false ? '🎉' : '⚠️'; ?></span>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Búsqueda -->
        <section class="search-section">
            <div class="form-card">
                <h2>🔍 Buscar Juegos (<?php echo count($inventario); ?> disponibles)</h2>
                <form method="GET">
                    <div style="display: flex; gap: 1rem; align-items: end;">
                        <div style="flex: 1;">
                            <label for="search">Buscar por nombre o descripción:</label>
                            <input type="text" id="search" name="search" 
                                   placeholder="¿Qué juego buscas?" 
                                   value="<?php echo htmlspecialchars($search_query); ?>">
                        </div>
                        <button type="submit" class="btn">Buscar</button>
                    </div>
                </form>
            </div>
        </section>
        
        <!-- Catálogo de Juegos -->
        <section>
            <h2>🎯 Juegos Disponibles</h2>
            <?php if (empty($inventario)): ?>
                <div class="form-card text-center">
                    <h3>😅 No se encontraron juegos</h3>
                    <p>No hay juegos que coincidan con tu búsqueda.</p>
                    <a href="?" class="btn">Ver todos los juegos</a>
                </div>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($inventario as $juego): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="uploads/<?php echo htmlspecialchars($juego['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($juego['name']); ?>" 
                                     onerror="this.src='uploads/no_image.png';">
                                <?php if ($juego['stock'] <= 0): ?>
                                    <div class="stock-overlay">AGOTADO</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($juego['name']); ?></h3>
                                <p class="product-description"><?php echo htmlspecialchars($juego['description']); ?></p>
                                
                                <div class="price-stock-container">
                                    <div class="price-tag">$<?php echo number_format($juego['price'], 2); ?></div>
                                    <div class="stock-info">
                                        <span class="<?php echo $juego['stock'] > 0 ? 'success' : 'error'; ?>">
                                            <?php if ($juego['stock'] > 0): ?>
                                                ✅ Stock: <?php echo $juego['stock']; ?>
                                            <?php else: ?>
                                                ❌ Agotado
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="product-actions">
                                <?php if ($juego['stock'] > 0): ?>
                                    <form method="POST" onsubmit="return confirm('¿Confirmas la compra de <?php echo htmlspecialchars($juego['name']); ?> por $<?php echo number_format($juego['price'], 2); ?>?')">
                                        <input type="hidden" name="buy_product_id" value="<?php echo $juego['id']; ?>">
                                        <button type="submit" class="btn pulse">
                                            🛒 Comprar Ahora
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn" disabled style="opacity: 0.5; cursor: not-allowed;">
                                        😞 No Disponible
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Historial de Compras -->
        <?php if (!empty($mis_compras)): ?>
            <section class="mt-lg">
                <div class="form-card">
                    <h2>🛍️ Mis Compras Recientes</h2>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Juego</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Total</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_reverse(array_slice($mis_compras, -5)) as $compra): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($compra['product_name']); ?></strong></td>
                                        <td><?php echo $compra['quantity']; ?></td>
                                        <td>$<?php echo number_format($compra['unit_price'], 2); ?></td>
                                        <td class="price-tag">$<?php echo number_format($compra['total_cost'], 2); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($compra['date'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php 
                    $total_gastado = array_sum(array_column($mis_compras, 'total_cost'));
                    ?>
                    <div style="text-align: right; margin-top: 1rem; padding: 1rem; background: rgba(102, 126, 234, 0.1); border-radius: 8px;">
                        <strong>💰 Total invertido: $<?php echo number_format($total_gastado, 2); ?></strong>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <style>
        .product-image {
            position: relative;
            overflow: hidden;
            border-radius: var(--border-radius-md);
            margin-bottom: var(--spacing-md);
        }

        .product-image img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: var(--transition-normal);
        }

        .product-card:hover .product-image img {
            transform: scale(1.08);
        }

        .stock-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(244, 67, 54, 0.9);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            backdrop-filter: blur(2px);
        }

        .product-info {
            flex-grow: 1;
            margin-bottom: var(--spacing-md);
        }

        .product-info h3 {
            color: var(--color-text);
            margin-bottom: var(--spacing-sm);
            font-size: 1.3rem;
            font-weight: 600;
        }

        .product-description {
            color: var(--color-text-light);
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: var(--spacing-md);
        }

        .price-stock-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: var(--spacing-sm);
        }

        .price-tag {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--color-primary);
        }

        .stock-info {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .product-actions {
            margin-top: auto;
        }

        .product-actions .btn {
            width: 100%;
            margin: 0;
        }

        @media (max-width: 768px) {
            .price-stock-container {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }
        }
    </style>
</body>
</html>