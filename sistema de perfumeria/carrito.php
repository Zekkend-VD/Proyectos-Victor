<?php
session_start();

// Verificar que el usuario esté logueado como cliente
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'cliente') {
    header('Location: index.php');
    exit;
}

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Funciones para el carrito
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

function calcular_total_carrito($carrito, $productos) {
    $total = 0;
    foreach ($carrito as $id => $cantidad) {
        if (isset($productos[$id])) {
            $total += $productos[$id]['precio'] * $cantidad;
        }
    }
    return $total;
}

$productos = leer_productos();
$mensaje = '';
$error = '';

// Procesar acciones del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['agregar_carrito'])) {
        $producto_id = $_POST['producto_id'];
        $cantidad = (int)$_POST['cantidad'];
        
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
    } elseif (isset($_POST['actualizar_cantidad'])) {
        $producto_id = $_POST['producto_id'];
        $nueva_cantidad = (int)$_POST['nueva_cantidad'];
        
        if ($nueva_cantidad <= 0) {
            unset($_SESSION['carrito'][$producto_id]);
            $mensaje = "Producto eliminado del carrito";
        } else {
            if (isset($productos[$producto_id]) && $nueva_cantidad <= $productos[$producto_id]['stock']) {
                $_SESSION['carrito'][$producto_id] = $nueva_cantidad;
                $mensaje = "Cantidad actualizada";
            } else {
                $error = "Cantidad no válida o sin stock suficiente";
            }
        }
    } elseif (isset($_POST['eliminar_producto'])) {
        $producto_id = $_POST['producto_id'];
        unset($_SESSION['carrito'][$producto_id]);
        $mensaje = "Producto eliminado del carrito";
    } elseif (isset($_POST['vaciar_carrito'])) {
        $_SESSION['carrito'] = [];
        $mensaje = "Carrito vaciado";
    }
}

$total_carrito = calcular_total_carrito($_SESSION['carrito'], $productos);
$cantidad_items = array_sum($_SESSION['carrito']);

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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito - Perfumes Exclusivos</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .carrito-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .carrito-header {
            background: var(--primary-gradient);
            color: white;
            padding: 25px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
            text-align: center;
            box-shadow: var(--shadow-medium);
        }
        
        .carrito-items {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-soft);
        }
        
        .carrito-item {
            display: grid;
            grid-template-columns: 80px 1fr 150px 120px 100px 80px;
            gap: 15px;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .carrito-item:last-child {
            border-bottom: none;
        }
        
        .producto-imagen {
            width: 70px;
            height: 70px;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: var(--shadow-soft);
        }
        
        .producto-info h4 {
            margin: 0 0 5px 0;
            color: var(--text-primary);
        }
        
        .producto-tipo {
            color: var(--text-secondary);
            font-size: 0.9em;
        }
        
        .cantidad-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .cantidad-control input {
            width: 60px;
            padding: 8px;
            border: 2px solid #ddd;
            border-radius: 8px;
            text-align: center;
        }
        
        .btn-actualizar {
            background: var(--accent-gradient);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9em;
        }
        
        .btn-eliminar {
            background: var(--danger-gradient);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9em;
        }
        
        .precio {
            font-weight: bold;
            color: var(--text-accent);
        }
        
        .subtotal {
            font-weight: bold;
            font-size: 1.1em;
            color: var(--text-primary);
        }
        
        .carrito-resumen {
            background: var(--card-gradient);
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--shadow-soft);
            text-align: center;
        }
        
        .total-general {
            font-size: 1.5em;
            font-weight: bold;
            color: var(--text-accent);
            margin: 20px 0;
        }
        
        .carrito-acciones {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .btn-primary {
            background: var(--success-gradient);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition);
        }
        
        .btn-secondary {
            background: var(--secondary-gradient);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition);
        }
        
        .btn-danger {
            background: var(--danger-gradient);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .carrito-vacio {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }
        
        .carrito-vacio h3 {
            margin-bottom: 20px;
            font-size: 1.5em;
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
            .carrito-item {
                grid-template-columns: 1fr;
                gap: 10px;
                text-align: center;
            }
            
            .carrito-acciones {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="carrito-container">
        <div class="carrito-header">
            <h1>🛒 Mi Carrito de Compras</h1>
            <p>Perfumes Exclusivos Seleccionados</p>
            <div class="perfume-count">
                <?php echo $cantidad_items; ?> productos | Total: $<?php echo number_format($total_carrito, 2); ?>
            </div>
        </div>
        
        <?php if ($mensaje): ?>
            <div class="mensaje"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (empty($_SESSION['carrito'])): ?>
            <div class="carrito-items">
                <div class="carrito-vacio">
                    <h3>🛍️ Tu carrito está vacío</h3>
                    <p>¡Explora nuestro catálogo de perfumes exclusivos!</p>
                    <a href="cliente.php" class="btn-primary">Ir al Catálogo</a>
                </div>
            </div>
        <?php else: ?>
            <div class="carrito-items">
                <h3>📦 Productos en tu carrito</h3>
                
                <?php foreach ($_SESSION['carrito'] as $producto_id => $cantidad): ?>
                    <?php if (isset($productos[$producto_id])): ?>
                        <?php $producto = $productos[$producto_id]; ?>
                        <?php $subtotal = $producto['precio'] * $cantidad; ?>
                        
                        <div class="carrito-item">
                            <div>
                                <?php 
                                $imagen_path = 'uploads/productos/' . $producto['imagen'];
                                if (file_exists($imagen_path)): 
                                ?>
                                    <img src="<?php echo $imagen_path; ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="producto-imagen">
                                <?php else: ?>
                                    <div class="producto-imagen" style="background: var(--primary-gradient); display: flex; align-items: center; justify-content: center; color: white; font-size: 2em;">
                                        <?php echo obtener_icono_perfume($producto['tipo']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="producto-info">
                                <h4><?php echo htmlspecialchars($producto['nombre']); ?></h4>
                                <div class="producto-tipo">
                                    <?php echo obtener_icono_perfume($producto['tipo']); ?> <?php echo htmlspecialchars($producto['tipo']); ?>
                                </div>
                            </div>
                            
                            <div class="precio">
                                $<?php echo number_format($producto['precio'], 2); ?>
                            </div>
                            
                            <form method="post" class="cantidad-control">
                                <input type="hidden" name="producto_id" value="<?php echo $producto_id; ?>">
                                <input type="number" name="nueva_cantidad" value="<?php echo $cantidad; ?>" min="1" max="<?php echo $producto['stock']; ?>">
                                <button type="submit" name="actualizar_cantidad" class="btn-actualizar">✓</button>
                            </form>
                            
                            <div class="subtotal">
                                $<?php echo number_format($subtotal, 2); ?>
                            </div>
                            
                            <form method="post">
                                <input type="hidden" name="producto_id" value="<?php echo $producto_id; ?>">
                                <button type="submit" name="eliminar_producto" class="btn-eliminar" onclick="return confirm('¿Eliminar este producto del carrito?')">🗑️</button>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            
            <div class="carrito-resumen">
                <h3>📋 Resumen del Pedido</h3>
                <div class="total-general">
                    Total: $<?php echo number_format($total_carrito, 2); ?>
                </div>
                
                <div class="carrito-acciones">
                    <a href="cliente.php" class="btn-secondary">Seguir Comprando</a>
                    <a href="confirmar_compra.php" class="btn-primary">Confirmar Compra 💳</a>
                    <form method="post" style="display: inline;">
                        <button type="submit" name="vaciar_carrito" class="btn-danger" onclick="return confirm('¿Vaciar todo el carrito?')">Vaciar Carrito</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="logout.php" class="btn-secondary">🚪 Cerrar Sesión</a>
        </div>
    </div>
</body>
</html>