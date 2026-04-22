<?php
session_start();

// Verificar que el usuario esté logueado como cliente
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'cliente') {
    header('Location: index.php');
    exit;
}

// --- Funciones de Archivo (Compatibles) ---
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
        $imagen = $p['imagen'] ?? 'default.jpg';
        $data .= "$id|{$p['nombre']}|{$p['tipo']}|{$p['precio']}|{$p['stock']}|$imagen\n";
    }
    file_put_contents('productos.txt', $data, LOCK_EX);
}

function registrar_venta($cliente_email, $producto_id, $producto_nombre, $precio, $cantidad, $total) {
    $fecha = date('Y-m-d H:i:s');
    $registro_venta = "$fecha|$cliente_email|$producto_id|$producto_nombre|$precio|$cantidad|$total\n";
    
    // Asegurar que el archivo existe
    if (!file_exists('ventas.txt')) {
        file_put_contents('ventas.txt', '');
    }
    
    // Agregar la venta
    $resultado = file_put_contents('ventas.txt', $registro_venta, FILE_APPEND | LOCK_EX);
    
    return $resultado !== false;
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

// --- Lógica de Compra ---
$productos = leer_productos();
$error = '';
$compra_exitosa = false;
$producto_comprado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_id'])) {
    $producto_id = $_POST['producto_id'];
    
    if (!isset($productos[$producto_id])) {
        $error = "Producto no encontrado.";
    } elseif ($productos[$producto_id]['stock'] <= 0) {
        $error = "Lo sentimos, este perfume está agotado.";
    } else {
        // Realizar la compra
        $producto = $productos[$producto_id];
        $cantidad = 1; // Por defecto compramos 1 unidad
        $total = $producto['precio'] * $cantidad;
        
        // Reducir stock
        $productos[$producto_id]['stock']--;
        guardar_productos($productos);
        
        // Registrar la venta
        if (registrar_venta($_SESSION['user_email'], $producto_id, $producto['nombre'], $producto['precio'], $cantidad, $total)) {
            $compra_exitosa = true;
            $producto_comprado = $producto;
            $producto_comprado['id'] = $producto_id;
            $producto_comprado['total'] = $total;
        } else {
            $error = "Error al registrar la venta. Contacte al administrador.";
            // Restaurar stock en caso de error
            $productos[$producto_id]['stock']++;
            guardar_productos($productos);
        }
    }
} else {
    $error = "Solicitud inválida.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Realizada - Perfumes Exclusivos</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .compra-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .compra-header {
            background: <?php echo $compra_exitosa ? 'var(--success-gradient)' : 'var(--danger-gradient)'; ?>;
            color: white;
            padding: 30px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
            text-align: center;
            box-shadow: var(--shadow-strong);
        }
        
        .compra-header h1 {
            color: white;
            background: none;
            -webkit-text-fill-color: white;
            margin-bottom: 15px;
            font-size: 2.5rem;
        }
        
        .producto-detalle {
            background: white;
            border-radius: var(--border-radius);
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-medium);
            text-align: center;
        }
        
        .producto-imagen {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: var(--border-radius);
            margin: 0 auto 20px auto;
            display: block;
            box-shadow: var(--shadow-soft);
        }
        
        .producto-imagen-placeholder {
            width: 200px;
            height: 200px;
            background: var(--primary-gradient);
            border-radius: var(--border-radius);
            margin: 0 auto 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4em;
            box-shadow: var(--shadow-soft);
        }
        
        .producto-nombre {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--text-primary);
        }
        
        .producto-tipo {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--accent-gradient);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .precio-total {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--text-accent);
            margin: 20px 0;
        }
        
        .compra-info {
            background: var(--card-gradient);
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid var(--success-gradient);
        }
        
        .error-info {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .acciones {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-primario {
            background: var(--primary-gradient);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.1rem;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition);
            box-shadow: var(--shadow-soft);
        }
        
        .btn-secundario {
            background: var(--secondary-gradient);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.1rem;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition);
            box-shadow: var(--shadow-soft);
        }
        
        .btn-primario:hover, .btn-secundario:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }
        
        .factura-detalle {
            background: rgba(102, 126, 234, 0.05);
            border-radius: var(--border-radius);
            padding: 20px;
            margin: 20px 0;
            border: 1px solid rgba(102, 126, 234, 0.2);
        }
        
        .factura-detalle h4 {
            color: var(--text-accent);
            margin-bottom: 15px;
            text-align: center;
        }
        
        .factura-linea {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        }
        
        .factura-linea:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.2rem;
            margin-top: 10px;
            padding-top: 15px;
            border-top: 2px solid rgba(102, 126, 234, 0.3);
        }
        
        @media (max-width: 768px) {
            .acciones {
                flex-direction: column;
                align-items: center;
            }
            
            .producto-imagen, .producto-imagen-placeholder {
                width: 150px;
                height: 150px;
            }
            
            .compra-header h1 {
                font-size: 2rem;
            }
            
            .precio-total {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="compra-container">
        <div class="compra-header">
            <?php if ($compra_exitosa): ?>
                <h1>🎉 ¡Compra Exitosa!</h1>
                <p>Tu perfume ha sido adquirido correctamente</p>
            <?php else: ?>
                <h1>😔 Error en la Compra</h1>
                <p>No se pudo completar la transacción</p>
            <?php endif; ?>
        </div>
        
        <?php if ($compra_exitosa && $producto_comprado): ?>
            <div class="producto-detalle">
                <?php 
                $imagen_path = 'uploads/productos/' . $producto_comprado['imagen'];
                if (file_exists($imagen_path) && !empty($producto_comprado['imagen']) && $producto_comprado['imagen'] !== 'default.jpg'): 
                ?>
                    <img src="<?php echo $imagen_path; ?>" alt="<?php echo htmlspecialchars($producto_comprado['nombre']); ?>" class="producto-imagen">
                <?php else: ?>
                    <div class="producto-imagen-placeholder">
                        <?php echo obtener_icono_perfume($producto_comprado['tipo']); ?>
                    </div>
                <?php endif; ?>
                
                <h2 class="producto-nombre">
                    <?php echo obtener_icono_perfume($producto_comprado['tipo']) . ' ' . htmlspecialchars($producto_comprado['nombre']); ?>
                </h2>
                
                <div class="producto-tipo">
                    <?php echo obtener_icono_perfume($producto_comprado['tipo']); ?>
                    <span><?php echo htmlspecialchars($producto_comprado['tipo']); ?></span>
                </div>
                
                <div class="precio-total">
                    $<?php echo number_format($producto_comprado['total'], 2); ?>
                </div>
            </div>
            
            <div class="compra-info">
                <h4>✅ Detalles de la Compra</h4>
                <div class="factura-detalle">
                    <h4>📄 Recibo de Compra</h4>
                    <div class="factura-linea">
                        <span>Cliente:</span>
                        <span><?php echo htmlspecialchars($_SESSION['user_email']); ?></span>
                    </div>
                    <div class="factura-linea">
                        <span>Fecha:</span>
                        <span><?php echo date('d/m/Y H:i:s'); ?></span>
                    </div>
                    <div class="factura-linea">
                        <span>Producto:</span>
                        <span><?php echo htmlspecialchars($producto_comprado['nombre']); ?></span>
                    </div>
                    <div class="factura-linea">
                        <span>ID Producto:</span>
                        <span><?php echo htmlspecialchars($producto_comprado['id']); ?></span>
                    </div>
                    <div class="factura-linea">
                        <span>Cantidad:</span>
                        <span>1 unidad</span>
                    </div>
                    <div class="factura-linea">
                        <span>Precio unitario:</span>
                        <span>$<?php echo number_format($producto_comprado['precio'], 2); ?></span>
                    </div>
                    <div class="factura-linea">
                        <span>TOTAL PAGADO:</span>
                        <span>$<?php echo number_format($producto_comprado['total'], 2); ?></span>
                    </div>
                </div>
                
                <p style="text-align: center; margin-top: 20px; color: var(--text-secondary);">
                    🚚 Tu perfume será procesado y enviado en las próximas 24-48 horas.<br>
                    📧 Recibirás un email de confirmación con los detalles del envío.
                </p>
            </div>
            
        <?php else: ?>
            <div class="error-info">
                <h3>❌ Error en el Procesamiento</h3>
                <p><?php echo htmlspecialchars($error); ?></p>
                <p style="margin-top: 15px;">
                    Por favor, intenta nuevamente o contacta al soporte técnico si el problema persiste.
                </p>
            </div>
        <?php endif; ?>
        
        <div class="acciones">
            <a href="cliente.php" class="btn-primario">🛍️ Seguir Comprando</a>
            <a href="carrito.php" class="btn-secundario">🛒 Ver Carrito</a>
            <a href="logout.php" class="btn-secundario">🚪 Cerrar Sesión</a>
        </div>
    </div>
</body>
</html>