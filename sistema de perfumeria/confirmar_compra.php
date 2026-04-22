<?php
session_start();

// Verificar que el usuario esté logueado como cliente
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'cliente') {
    header('Location: index.php');
    exit;
}

// Verificar que hay productos en el carrito
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header('Location: carrito.php');
    exit;
}

// Funciones necesarias
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

function guardar_productos($productos) {
    $data = '';
    foreach ($productos as $id => $p) {
        $data .= "$id|{$p['nombre']}|{$p['tipo']}|{$p['precio']}|{$p['stock']}|{$p['imagen']}\n";
    }
    file_put_contents('productos.txt', $data, LOCK_EX);
}

function registrar_venta($cliente_email, $producto_id, $producto_nombre, $precio, $cantidad, $total) {
    $fecha = date('Y-m-d H:i:s');
    $registro_venta = "$fecha|$cliente_email|$producto_id|$producto_nombre|$precio|$cantidad|$total\n";
    
    if (!file_exists('ventas.txt')) {
        file_put_contents('ventas.txt', '');
    }
    
    return file_put_contents('ventas.txt', $registro_venta, FILE_APPEND | LOCK_EX) !== false;
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
$compra_exitosa = false;

// Procesar confirmación de compra
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_compra'])) {
    $productos_actualizados = $productos;
    $ventas_registradas = [];
    $total_general = 0;
    $error_stock = false;
    
    // Verificar stock disponible
    foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
        if (!isset($productos[$producto_id])) {
            $error = "Producto no encontrado: $producto_id";
            $error_stock = true;
            break;
        }
        
        if ($productos[$producto_id]['stock'] < $cantidad) {
            $error = "Stock insuficiente para {$productos[$producto_id]['nombre']}. Stock disponible: {$productos[$producto_id]['stock']}";
            $error_stock = true;
            break;
        }
    }
    
    if (!$error_stock) {
        // Procesar la compra
        foreach ($_SESSION['carrito'] as $producto_id => $cantidad) {
            $producto = $productos[$producto_id];
            $subtotal = $producto['precio'] * $cantidad;
            $total_general += $subtotal;
            
            // Actualizar stock
            $productos_actualizados[$producto_id]['stock'] -= $cantidad;
            
            // Registrar venta
            if (registrar_venta($_SESSION['user_email'], $producto_id, $producto['nombre'], $producto['precio'], $cantidad, $subtotal)) {
                $ventas_registradas[] = [
                    'producto' => $producto['nombre'],
                    'cantidad' => $cantidad,
                    'precio' => $producto['precio'],
                    'subtotal' => $subtotal
                ];
            }
        }
        
        // Guardar productos actualizados
        guardar_productos($productos_actualizados);
        
        // Limpiar carrito
        $_SESSION['carrito'] = [];
        $compra_exitosa = true;
        $mensaje = "¡Compra realizada exitosamente! Total: $" . number_format($total_general, 2);
    }
}

$total_carrito = calcular_total_carrito($_SESSION['carrito'], $productos);

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
    <title>Confirmar Compra - Perfumes Exclusivos</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .confirmacion-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .confirmacion-header {
            background: var(--success-gradient);
            color: white;
            padding: 25px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
            text-align: center;
            box-shadow: var(--shadow-medium);
        }
        
        .resumen-compra {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-soft);
        }
        
        .producto-resumen {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            gap: 15px;
        }
        
        .producto-resumen:last-child {
            border-bottom: none;
        }
        
        .producto-imagen-small {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            box-shadow: var(--shadow-soft);
        }
        
        .producto-detalles {
            flex: 1;
        }
        
        .producto-detalles h4 {
            margin: 0 0 5px 0;
            color: var(--text-primary);
        }
        
        .producto-detalles .tipo {
            color: var(--text-secondary);
            font-size: 0.9em;
        }
        
        .cantidad-precio {
            text-align: right;
        }
        
        .cantidad {
            color: var(--text-secondary);
            font-size: 0.9em;
        }
        
        .precio-unitario {
            color: var(--text-primary);
            font-weight: bold;
        }
        
        .subtotal {
            color: var(--text-accent);
            font-weight: bold;
            font-size: 1.1em;
        }
        
        .total-section {
            background: var(--card-gradient);
            border-radius: var(--border-radius);
            padding: 25px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: var(--shadow-soft);
        }
        
        .total-amount {
            font-size: 2em;
            font-weight: bold;
            color: var(--text-accent);
            margin: 20px 0;
        }
        
        .acciones-confirmacion {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-confirmar {
            background: var(--success-gradient);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow-soft);
        }
        
        .btn-volver {
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
        
        .compra-exitosa {
            background: var(--success-gradient);
            color: white;
            padding: 30px;
            border-radius: var(--border-radius);
            text-align: center;
            margin-bottom: 20px;
            box-shadow: var(--shadow-medium);
        }
        
        .compra-exitosa h2 {
            margin-bottom: 15px;
            font-size: 2em;
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
        
        .info-cliente {
            background: #e3f2fd;
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            border-left: 4px solid #2196F3;
        }
        
        @media (max-width: 768px) {
            .producto-resumen {
                flex-direction: column;
                text-align: center;
            }
            
            .acciones-confirmacion {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="confirmacion-container">
        <?php if ($compra_exitosa): ?>
            <div class="compra-exitosa">
                <h2>🎉 ¡Compra Realizada Exitosamente!</h2>
                <p>Gracias por tu compra. Recibirás un email de confirmación pronto.</p>
            </div>
            
            <div class="resumen-compra">
                <h3>📋 Resumen de tu Compra</h3>
                <div class="info-cliente">
                    <strong>Cliente:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?><br>
                    <strong>Fecha:</strong> <?php echo date('d/m/Y H:i:s'); ?>
                </div>
                
                <?php foreach ($ventas_registradas as $venta): ?>
                    <div class="producto-resumen">
                        <div class="producto-detalles">
                            <h4><?php echo htmlspecialchars($venta['producto']); ?></h4>
                        </div>
                        <div class="cantidad-precio">
                            <div class="cantidad">Cantidad: <?php echo $venta['cantidad']; ?></div>
                            <div class="precio-unitario">$<?php echo number_format($venta['precio'], 2); ?> c/u</div>
                            <div class="subtotal">$<?php echo number_format($venta['subtotal'], 2); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="total-section">
                    <h3>Total Pagado</h3>
                    <div class="total-amount">$<?php echo number_format($total_general, 2); ?></div>
                </div>
            </div>
            
            <div class="acciones-confirmacion">
                <a href="cliente.php" class="btn-volver">Seguir Comprando</a>
                <a href="carrito.php" class="btn-volver">Ver Carrito</a>
            </div>
            
        <?php else: ?>
            <div class="confirmacion-header">
                <h1>💳 Confirmar Compra</h1>
                <p>Revisa tu pedido antes de finalizar</p>
            </div>
            
            <?php if ($mensaje): ?>
                <div class="mensaje"><?php echo htmlspecialchars($mensaje); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="resumen-compra">
                <h3>📦 Productos a Comprar</h3>
                
                <div class="info-cliente">
                    <strong>Cliente:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?>
                </div>
                
                <?php foreach ($_SESSION['carrito'] as $producto_id => $cantidad): ?>
                    <?php if (isset($productos[$producto_id])): ?>
                        <?php $producto = $productos[$producto_id]; ?>
                        <?php $subtotal = $producto['precio'] * $cantidad; ?>
                        
                        <div class="producto-resumen">
                            <div>
                                <?php 
                                $imagen_path = 'uploads/productos/' . $producto['imagen'];
                                if (file_exists($imagen_path)): 
                                ?>
                                    <img src="<?php echo $imagen_path; ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="producto-imagen-small">
                                <?php else: ?>
                                    <div class="producto-imagen-small" style="background: var(--primary-gradient); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5em;">
                                        <?php echo obtener_icono_perfume($producto['tipo']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="producto-detalles">
                                <h4><?php echo htmlspecialchars($producto['nombre']); ?></h4>
                                <div class="tipo">
                                    <?php echo obtener_icono_perfume($producto['tipo']); ?> <?php echo htmlspecialchars($producto['tipo']); ?>
                                </div>
                                <div class="tipo">
                                    <small>Stock disponible: <?php echo $producto['stock']; ?></small>
                                </div>
                            </div>
                            
                            <div class="cantidad-precio">
                                <div class="cantidad">Cantidad: <?php echo $cantidad; ?></div>
                                <div class="precio-unitario">$<?php echo number_format($producto['precio'], 2); ?> c/u</div>
                                <div class="subtotal">$<?php echo number_format($subtotal, 2); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            
            <div class="total-section">
                <h3>💰 Total a Pagar</h3>
                <div class="total-amount">$<?php echo number_format($total_carrito, 2); ?></div>
                
                <form method="post" class="acciones-confirmacion">
                    <a href="carrito.php" class="btn-volver">🔙 Volver al Carrito</a>
                    <button type="submit" name="confirmar_compra" class="btn-confirmar" onclick="return confirm('¿Confirmar la compra por $<?php echo number_format($total_carrito, 2); ?>?')">✅ Confirmar Compra</button>
                </form>
            </div>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="logout.php" class="btn-volver">🚪 Cerrar Sesión</a>
        </div>
    </div>
</body>
</html>