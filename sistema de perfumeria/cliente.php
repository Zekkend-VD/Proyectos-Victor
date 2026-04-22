<?php
session_start();
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'cliente') {
    header('Location: index.php');
    exit;
}

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// --- Funciones de Manejo de Productos (DB Simulado) ---
function leer_productos() {
    $productos = [];
    $filename = 'productos.txt';

    if (file_exists($filename)) {
        $file = fopen($filename, 'r');
        if ($file) {
            while (($line = fgets($file)) !== false) {
                $line = trim($line);
                if (empty($line)) continue;
                
                @list($id, $nombre, $tipo, $precio, $stock, $imagen) = explode('|', $line);
                if ($id) {
                    $productos[$id] = [
                        'nombre' => $nombre, 
                        'tipo' => $tipo, 
                        'precio' => (float)$precio, 
                        'stock' => (int)$stock,
                        'imagen' => $imagen ?? 'default.jpg'
                    ];
                }
            }
            fclose($file);
        }
    }
    return $productos;
}

$productos = leer_productos();
$busqueda = $_GET['q'] ?? '';
$filtro_tipo = $_GET['tipo'] ?? '';
$productos_filtrados = [];
$mensaje = '';
$error = '';

// Procesar acciones del carrito (agregar productos)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_carrito'])) {
    $producto_id = $_POST['producto_id'];
    $cantidad = (int)($_POST['cantidad'] ?? 1);
    
    if (isset($productos[$producto_id]) && $cantidad > 0 && $cantidad <= $productos[$producto_id]['stock']) {
        if (isset($_SESSION['carrito'][$producto_id])) {
            $_SESSION['carrito'][$producto_id] += $cantidad;
        } else {
            $_SESSION['carrito'][$producto_id] = $cantidad;
        }
        
        // Verificar que no exceda el stock
        if ($_SESSION['carrito'][$producto_id] > $productos[$producto_id]['stock']) {
            $_SESSION['carrito'][$producto_id] = $productos[$producto_id]['stock'];
            $mensaje = "Se agregó la cantidad máxima disponible ({$productos[$producto_id]['stock']}) de {$productos[$producto_id]['nombre']}";
        } else {
            $mensaje = "{$productos[$producto_id]['nombre']} agregado al carrito";
        }
    } else {
        $error = "Error al agregar producto al carrito";
    }
}

// Obtener tipos únicos para el filtro
$tipos_disponibles = [];
foreach ($productos as $producto) {
    if (!in_array($producto['tipo'], $tipos_disponibles)) {
        $tipos_disponibles[] = $producto['tipo'];
    }
}
sort($tipos_disponibles);

// Lógica de Búsqueda y Filtrado
foreach ($productos as $id => $producto) {
    $coincide_busqueda = empty($busqueda) || stripos($producto['nombre'] . ' ' . $producto['tipo'], $busqueda) !== false;
    $coincide_tipo = empty($filtro_tipo) || $producto['tipo'] === $filtro_tipo;
    
    if ($coincide_busqueda && $coincide_tipo) {
        $productos_filtrados[$id] = $producto;
    }
}

// Función para obtener icono según el tipo de perfume
function obtener_icono_perfume($tipo) {
    $iconos = [
        'Eau de Parfum' => '🌹',
        'Eau de Toilette' => '🌼',
        'Eau de Cologne' => '🌿',
        'Parfum' => '💎',
        'Eau Fraiche' => '🌊',
        'Unisex' => '⚡',
        'Hombre' => '🕴️',
        'Mujer' => '👩‍🦳'
    ];
    return $iconos[$tipo] ?? '✨';
}

// Calcular información del carrito
$cantidad_items = array_sum($_SESSION['carrito']);
$total_carrito = 0;
foreach ($_SESSION['carrito'] as $id => $cantidad) {
    if (isset($productos[$id])) {
        $total_carrito += $productos[$id]['precio'] * $cantidad;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Perfumes Exclusivos</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .welcome-section {
            background: var(--primary-gradient);
            color: white;
            padding: 25px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
            text-align: center;
            box-shadow: var(--shadow-medium);
            position: relative;
        }
        
        .perfume-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .perfume-count {
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .carrito-info {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: var(--border-radius);
            backdrop-filter: blur(10px);
            text-align: center;
        }

        .carrito-link {
            color: white;
            text-decoration: none;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.1em;
        }

        .carrito-badge {
            background: #ff4757;
            color: white;
            border-radius: 50%;
            padding: 4px 8px;
            font-size: 0.8em;
            min-width: 20px;
            text-align: center;
        }
        
        .search-bar {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-medium);
        }
        
        .search-bar input[type="text"] {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e0e8f7;
            border-radius: var(--border-radius);
            font-size: 1.1rem;
            background: white;
            transition: var(--transition);
            margin-bottom: 20px;
        }
        
        .search-bar input[type="text"]:focus {
            border-color: var(--text-accent);
            box-shadow: 0 0 0 3px rgba(85, 60, 154, 0.1);
            outline: none;
        }
        
        .search-bar button {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            margin-right: 10px;
        }
        
        .filter-section {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-section select {
            min-width: 200px;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: var(--border-radius);
            color: var(--text-primary);
            font-size: 1rem;
            backdrop-filter: blur(10px);
            transition: var(--transition);
        }
        
        .perfume-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--accent-gradient);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin: 5px 0;
            box-shadow: var(--shadow-soft);
        }
        
        .price-highlight {
            font-size: 1.3rem;
            font-weight: 700;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .product-description {
            margin: 15px 0;
            padding: 15px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: var(--border-radius);
            border-left: 4px solid var(--primary-gradient);
            font-style: italic;
            color: var(--text-secondary);
        }
        
        .perfume-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 15px 0;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            font-size: 0.9rem;
        }
        
        .no-products {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.8), rgba(248, 250, 255, 0.6));
            border-radius: var(--border-radius-lg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .no-products h3 {
            font-size: 2rem;
            margin-bottom: 15px;
            background: var(--secondary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .producto-imagen {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: var(--border-radius);
            margin-bottom: 15px;
            box-shadow: var(--shadow-soft);
        }

        .producto-imagen-placeholder {
            width: 100%;
            height: 200px;
            background: var(--primary-gradient);
            border-radius: var(--border-radius);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3em;
            box-shadow: var(--shadow-soft);
        }

        .carrito-controls {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 15px;
        }

        .cantidad-input {
            width: 60px;
            padding: 8px;
            border: 2px solid #ddd;
            border-radius: 8px;
            text-align: center;
            font-size: 1em;
        }

        .btn-agregar-carrito {
            background: var(--success-gradient);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            flex: 1;
        }

        .btn-agregar-carrito:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .btn-comprar-directo {
            background: var(--secondary-gradient);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            font-size: 0.9em;
        }

        .mensaje, .error {
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        .mensaje {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .carrito-info {
                position: static;
                margin-bottom: 20px;
            }
            
            .carrito-controls {
                flex-direction: column;
                gap: 8px;
            }
            
            .cantidad-input {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="welcome-section">
            <div class="carrito-info">
                <a href="carrito.php" class="carrito-link">
                    🛒 Mi Carrito
                    <?php if ($cantidad_items > 0): ?>
                        <span class="carrito-badge"><?php echo $cantidad_items; ?></span>
                    <?php endif; ?>
                </a>
                <?php if ($cantidad_items > 0): ?>
                    <div style="font-size: 0.9em; margin-top: 5px;">
                        $<?php echo number_format($total_carrito, 2); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="perfume-header">
                <span style="font-size: 2.5rem;">🌸</span>
                <h1 style="color: white; background: none; -webkit-text-fill-color: white;">Catálogo de Perfumes Exclusivos</h1>
                <span style="font-size: 2.5rem;">✨</span>
            </div>
            <p>Bienvenid@, <strong><?php echo htmlspecialchars($_SESSION['user_email']); ?></strong>! 
            <a href="logout.php" style="color: white; text-decoration: underline; margin-left: 20px;">🚪 Cerrar Sesión</a></p>
            <div class="perfume-count">
                💎 <?php echo count($productos_filtrados); ?> fragancias disponibles
            </div>
        </div>

        <?php if ($mensaje): ?>
            <div class="mensaje"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php
        if (isset($_SESSION['mensaje_compra'])) {
            echo "<p class='mensaje'>{$_SESSION['mensaje_compra']}</p>";
            unset($_SESSION['mensaje_compra']);
        }
        ?>

        <div class="search-bar">
            <input type="text" 
                   name="q" 
                   placeholder="🔍 Buscar por nombre, marca o tipo..." 
                   value="<?php echo htmlspecialchars($busqueda); ?>"
                   id="search-input">
            
            <div class="filter-section">
                <select name="tipo" id="tipo-filter">
                    <option value="">🌟 Todos los tipos</option>
                    <?php foreach ($tipos_disponibles as $tipo): ?>
                        <option value="<?php echo htmlspecialchars($tipo); ?>" 
                                <?php echo ($filtro_tipo === $tipo) ? 'selected' : ''; ?>>
                            <?php echo obtener_icono_perfume($tipo) . ' ' . htmlspecialchars($tipo); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="button" onclick="aplicarFiltros()" id="filter-btn">
                    ✨ Filtrar
                </button>
                
                <button type="button" onclick="limpiarFiltros()" style="background: var(--secondary-gradient);">
                    🔄 Limpiar
                </button>
            </div>
        </div>
        
        <div class="inventory-grid">
            <?php if (empty($productos_filtrados)): ?>
                <div class="no-products" style="grid-column: 1 / -1;">
                    <h3>🔍 No se encontraron perfumes</h3>
                    <p>No hay fragancias que coincidan con tu búsqueda actual.</p>
                    <button onclick="limpiarFiltros()" style="margin-top: 20px; background: var(--primary-gradient);">
                        🌟 Ver todo el catálogo
                    </button>
                </div>
            <?php endif; ?>

            <?php foreach ($productos_filtrados as $id => $p): ?>
                <div class="product-card">
                    <?php 
                    $imagen_path = 'uploads/productos/' . $p['imagen'];
                    if (file_exists($imagen_path) && !empty($p['imagen']) && $p['imagen'] !== 'default.jpg'): 
                    ?>
                        <img src="<?php echo $imagen_path; ?>" alt="<?php echo htmlspecialchars($p['nombre']); ?>" class="producto-imagen">
                    <?php else: ?>
                        <div class="producto-imagen-placeholder">
                            <?php echo obtener_icono_perfume($p['tipo']); ?>
                        </div>
                    <?php endif; ?>

                    <h3><?php echo obtener_icono_perfume($p['tipo']) . ' ' . htmlspecialchars($p['nombre']); ?></h3>
                    
                    <div class="perfume-type-badge">
                        <?php echo obtener_icono_perfume($p['tipo']); ?>
                        <span><?php echo htmlspecialchars($p['tipo']); ?></span>
                    </div>
                    
                    <div class="perfume-details">
                        <div class="detail-item">
                            <span>💰</span>
                            <span class="price-highlight">$<?php echo number_format($p['precio'], 2); ?></span>
                        </div>
                        <div class="detail-item">
                            <span>📦</span>
                            <span>ID: <?php echo htmlspecialchars($id); ?></span>
                        </div>
                    </div>
                    
                    <div class="product-description">
                        <?php
                        $descripciones = [
                            'Eau de Parfum' => 'Fragancia intensa y duradera, perfecta para ocasiones especiales.',
                            'Eau de Toilette' => 'Frescura ligera y elegante para el uso diario.',
                            'Eau de Cologne' => 'Aroma refrescante y cítrico, ideal para el día.',
                            'Parfum' => 'La máxima concentración aromática, exclusiva y sofisticada.',
                            'Eau Fraiche' => 'Delicada y suave, perfecta para pieles sensibles.',
                            'Unisex' => 'Fragancia versátil para cualquier persona y ocasión.',
                            'Hombre' => 'Masculina y distintiva, con carácter único.',
                            'Mujer' => 'Femenina y encantadora, con notas cautivadoras.'
                        ];
                        echo $descripciones[$p['tipo']] ?? 'Fragancia exclusiva de alta calidad.';
                        ?>
                    </div>
                    
                    <?php if ($p['stock'] > 0): ?>
                        <p class="stock-ok">
                            ✅ En Stock: <?php echo $p['stock']; ?> unidades
                        </p>
                        
                        <form method="POST" class="carrito-controls">
                            <input type="hidden" name="producto_id" value="<?php echo $id; ?>">
                            <label for="cantidad_<?php echo $id; ?>" style="font-size: 0.9em;">Cantidad:</label>
                            <input type="number" 
                                   id="cantidad_<?php echo $id; ?>" 
                                   name="cantidad" 
                                   value="1" 
                                   min="1" 
                                   max="<?php echo $p['stock']; ?>" 
                                   class="cantidad-input">
                            <button type="submit" name="agregar_carrito" class="btn-agregar-carrito">
                                🛒 Agregar al Carrito
                            </button>
                        </form>

                        <form method="POST" action="compra.php" style="margin-top: 8px;"> 
                            <input type="hidden" name="producto_id" value="<?php echo $id; ?>">
                            <button type="submit" class="btn-comprar-directo">
                                💎 Comprar Directo
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="stock-out">😔 Agotado Temporalmente</p>
                        <button class="btn-agregar-carrito" style="background: var(--danger-gradient); opacity: 0.7;" disabled>
                            📭 No Disponible
                        </button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function aplicarFiltros() {
            const busqueda = document.getElementById('search-input').value;
            const tipo = document.getElementById('tipo-filter').value;
            
            const params = new URLSearchParams();
            if (busqueda) params.set('q', busqueda);
            if (tipo) params.set('tipo', tipo);
            
            window.location.href = 'cliente.php' + (params.toString() ? '?' + params.toString() : '');
        }
        
        function limpiarFiltros() {
            window.location.href = 'cliente.php';
        }
        
        // Aplicar filtros al presionar Enter en el campo de búsqueda
        document.getElementById('search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                aplicarFiltros();
            }
        });
        
        // Aplicar filtros automáticamente al cambiar el tipo
        document.getElementById('tipo-filter').addEventListener('change', function() {
            aplicarFiltros();
        });
        
        // Animación de entrada para las tarjetas
        const cards = document.querySelectorAll('.product-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    </script>
</body>
</html>