<?php
session_start();

// Verificar que el usuario sea administrador
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// --- Funciones para manejar datos ---
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

function leer_usuarios() {
    $usuarios = [];
    $filename = 'usuarios.txt';

    if (file_exists($filename)) {
        $file = fopen($filename, 'r');
        if ($file) {
            while (($line = fgets($file)) !== false) {
                $line = trim($line);
                if (empty($line)) continue;
                
                @list($email, $hash, $rol) = explode('|', $line);
                if ($email && $rol) {
                    $usuarios[] = ['email' => $email, 'rol' => $rol];
                }
            }
            fclose($file);
        }
    }
    return $usuarios;
}

function leer_ventas() {
    $ventas = [];
    $filename = 'ventas.txt';

    if (file_exists($filename)) {
        $file = fopen($filename, 'r');
        if ($file) {
            while (($line = fgets($file)) !== false) {
                $line = trim($line);
                if (empty($line)) continue;
                
                @list($fecha, $cliente, $producto_id, $producto_nombre, $precio, $cantidad, $total) = explode('|', $line);
                if ($fecha) {
                    $ventas[] = [
                        'fecha' => $fecha,
                        'cliente' => $cliente,
                        'producto_id' => $producto_id,
                        'producto_nombre' => $producto_nombre,
                        'precio' => (float)$precio,
                        'cantidad' => (int)$cantidad,
                        'total' => (float)$total
                    ];
                }
            }
            fclose($file);
        }
    }
    
    // Ordenar por fecha descendente
    usort($ventas, function($a, $b) {
        return strtotime($b['fecha']) - strtotime($a['fecha']);
    });
    
    return $ventas;
}

function guardar_productos($productos) {
    $data = '';
    foreach ($productos as $id => $p) {
        $imagen = $p['imagen'] ?? 'default.jpg';
        $data .= "$id|{$p['nombre']}|{$p['tipo']}|{$p['precio']}|{$p['stock']}|$imagen\n";
    }
    file_put_contents('productos.txt', $data, LOCK_EX);
}

function subir_imagen($archivo, $producto_id) {
    if (!isset($archivo) || $archivo['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $tipos_permitidos = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    if (!in_array($archivo['type'], $tipos_permitidos)) {
        return false;
    }
    
    $tamaño_maximo = 5 * 1024 * 1024; // 5MB
    if ($archivo['size'] > $tamaño_maximo) {
        return false;
    }
    
    // Crear directorio si no existe
    if (!is_dir('uploads/productos')) {
        mkdir('uploads/productos', 0755, true);
    }
    
    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $nombre_archivo = $producto_id . '.' . $extension;
    $ruta_destino = 'uploads/productos/' . $nombre_archivo;
    
    if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
        return $nombre_archivo;
    }
    
    return false;
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

// Manejar acciones del administrador
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['agregar_producto'])) {
        $id = 'perfume' . str_pad(rand(1, 9999), 3, '0', STR_PAD_LEFT);
        $nombre = trim($_POST['nombre']);
        $tipo = trim($_POST['tipo']);
        $precio = (float)$_POST['precio'];
        $stock = (int)$_POST['stock'];
        
        if (!empty($nombre) && !empty($tipo) && $precio > 0 && $stock >= 0) {
            $imagen = 'default.jpg';
            
            // Procesar imagen si se subió
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $imagen_subida = subir_imagen($_FILES['imagen'], $id);
                if ($imagen_subida) {
                    $imagen = $imagen_subida;
                } else {
                    $mensaje = "Producto agregado pero hubo error al subir la imagen. Formatos permitidos: JPG, PNG, WEBP (máx. 5MB)";
                }
            }
            
            $productos = leer_productos();
            $productos[$id] = [
                'nombre' => $nombre,
                'tipo' => $tipo,
                'precio' => $precio,
                'stock' => $stock,
                'imagen' => $imagen
            ];
            guardar_productos($productos);
            
            if (empty($mensaje)) {
                $mensaje = "Perfume agregado exitosamente con ID: $id";
            }
        } else {
            $mensaje = "Error: Complete todos los campos correctamente";
        }
    }
    
    if (isset($_POST['actualizar_stock'])) {
        $producto_id = $_POST['producto_id'];
        $nuevo_stock = (int)$_POST['nuevo_stock'];
        
        $productos = leer_productos();
        if (isset($productos[$producto_id]) && $nuevo_stock >= 0) {
            $productos[$producto_id]['stock'] = $nuevo_stock;
            guardar_productos($productos);
            $mensaje = "Stock actualizado para {$productos[$producto_id]['nombre']}";
        } else {
            $mensaje = "Error: Producto no encontrado o stock inválido";
        }
    }
    
    if (isset($_POST['actualizar_imagen'])) {
        $producto_id = $_POST['producto_id'];
        $productos = leer_productos();
        
        if (isset($productos[$producto_id]) && isset($_FILES['nueva_imagen']) && $_FILES['nueva_imagen']['error'] === UPLOAD_ERR_OK) {
            $imagen_subida = subir_imagen($_FILES['nueva_imagen'], $producto_id);
            if ($imagen_subida) {
                // Eliminar imagen anterior si no es default.jpg
                if ($productos[$producto_id]['imagen'] !== 'default.jpg') {
                    $imagen_anterior = 'uploads/productos/' . $productos[$producto_id]['imagen'];
                    if (file_exists($imagen_anterior)) {
                        unlink($imagen_anterior);
                    }
                }
                
                $productos[$producto_id]['imagen'] = $imagen_subida;
                guardar_productos($productos);
                $mensaje = "Imagen actualizada para {$productos[$producto_id]['nombre']}";
            } else {
                $mensaje = "Error al subir la imagen. Formatos permitidos: JPG, PNG, WEBP (máx. 5MB)";
            }
        } else {
            $mensaje = "Error: Producto no encontrado o no se seleccionó imagen";
        }
    }
}

// Obtener datos para mostrar
$productos = leer_productos();
$usuarios = leer_usuarios();
$ventas = leer_ventas();

// Calcular estadísticas
$total_productos = count($productos);
$total_usuarios = count($usuarios);
$total_ventas = count($ventas);
$ingresos_totales = array_sum(array_column($ventas, 'total'));

// Tipos de perfumes disponibles
$tipos_perfumes = [
    'Eau de Parfum',
    'Eau de Toilette', 
    'Eau de Cologne',
    'Parfum',
    'Eau Fraiche',
    'Unisex',
    'Hombre',
    'Mujer'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Perfumes</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-header {
            background: var(--primary-gradient);
            color: white;
            padding: 30px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
            text-align: center;
            box-shadow: var(--shadow-strong);
        }
        
        .admin-header h1 {
            color: white;
            background: none;
            -webkit-text-fill-color: white;
            margin-bottom: 15px;
        }
        
        .perfume-icon-large {
            font-size: 3rem;
            margin: 0 15px;
        }
        
        .admin-nav {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .admin-nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            backdrop-filter: blur(10px);
            transition: var(--transition);
        }
        
        .admin-nav a:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        
        .quick-add-form {
            background: var(--gold-gradient);
            color: white;
            padding: 25px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
        }
        
        .quick-add-form h2 {
            color: white;
            background: none;
            -webkit-text-fill-color: white;
            text-align: left;
        }
        
        .form-grid-perfume {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .perfume-select {
            background: rgba(255, 255, 255, 0.9);
            color: var(--text-primary);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .inventory-alert {
            padding: 15px;
            background: var(--danger-gradient);
            color: white;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }
        
        .imagen-preview {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: var(--shadow-soft);
        }
        
        .imagen-placeholder {
            width: 80px;
            height: 80px;
            background: var(--primary-gradient);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2em;
            box-shadow: var(--shadow-soft);
        }
        
        .imagen-upload {
            background: rgba(255, 255, 255, 0.9);
            border: 2px dashed rgba(255, 255, 255, 0.5);
            padding: 20px;
            border-radius: var(--border-radius);
            text-align: center;
            margin-top: 15px;
        }
        
        .imagen-upload input[type="file"] {
            margin: 10px 0;
        }
        
        .btn-imagen {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9em;
            margin: 5px;
        }
        
        .imagen-control {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        
        .file-input-custom {
            display: none;
        }
        
        .file-input-label {
            background: var(--accent-gradient);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9em;
            transition: var(--transition);
        }
        
        .file-input-label:hover {
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .form-grid-perfume {
                grid-template-columns: 1fr;
            }
            
            .admin-nav {
                flex-direction: column;
                align-items: center;
            }
            
            table {
                font-size: 0.9em;
            }
            
            .imagen-preview, .imagen-placeholder {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="admin-header">
            <h1>
                <span class="perfume-icon-large">🌸</span>
                Panel de Administración
                <span class="perfume-icon-large">✨</span>
            </h1>
            <p>Gestión Completa del Sistema de Perfumes Exclusivos</p>
            <p>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['user_email']); ?></strong>!</p>
            
            <div class="admin-nav">
                <a href="#estadisticas">📊 Estadísticas</a>
                <a href="#inventario">🧴 Inventario</a>
                <a href="#usuarios">👥 Usuarios</a>
                <a href="#ventas">💰 Ventas</a>
                <a href="logout.php">🚪 Cerrar Sesión</a>
            </div>
        </div>

        <?php if ($mensaje): ?>
            <p class="mensaje-alerta"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

        <!-- Estadísticas Generales -->
        <div class="admin-section" id="estadisticas">
            <h2>📊 Dashboard de Estadísticas</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo $total_productos; ?></h3>
                    <p>🧴 Perfumes en Catálogo</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $total_usuarios; ?></h3>
                    <p>👥 Clientes Registrados</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $total_ventas; ?></h3>
                    <p>🛍️ Ventas Realizadas</p>
                </div>
                <div class="stat-card">
                    <h3>$<?php echo number_format($ingresos_totales, 2); ?></h3>
                    <p>💰 Ingresos Totales</p>
                </div>
            </div>
            
            <?php
            // Verificar stock bajo
            $productos_bajo_stock = array_filter($productos, function($p) { return $p['stock'] <= 5; });
            if (!empty($productos_bajo_stock)):
            ?>
                <div class="inventory-alert">
                    ⚠️ ALERTA: <?php echo count($productos_bajo_stock); ?> producto(s) con stock bajo (≤5 unidades)
                </div>
            <?php endif; ?>
        </div>

        <!-- Agregar Producto -->
        <div class="quick-add-form">
            <h2>➕ Agregar Nuevo Perfume al Catálogo</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid-perfume">
                    <input type="text" name="nombre" placeholder="🏷️ Nombre del Perfume (ej: Chanel No. 5)" required>
                    <select name="tipo" required class="perfume-select">
                        <option value="">🌟 Seleccionar Tipo</option>
                        <?php foreach ($tipos_perfumes as $tipo): ?>
                            <option value="<?php echo htmlspecialchars($tipo); ?>">
                                <?php echo obtener_icono_perfume($tipo) . ' ' . htmlspecialchars($tipo); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="precio" step="0.01" min="0" placeholder="💰 Precio USD" required>
                    <input type="number" name="stock" min="0" placeholder="📦 Stock inicial" required>
                </div>
                
                <div class="imagen-upload">
                    <h4>📷 Imagen del Producto (Opcional)</h4>
                    <p>Formatos permitidos: JPG, PNG, WEBP | Tamaño máximo: 5MB</p>
                    <input type="file" name="imagen" accept="image/*" class="file-input-custom" id="nueva-imagen">
                    <label for="nueva-imagen" class="file-input-label">📁 Seleccionar Imagen</label>
                    <div id="preview-nueva" style="margin-top: 10px;"></div>
                </div>
                
                <button type="submit" name="agregar_producto" style="background: white; color: var(--text-primary); margin-top: 15px;">
                    ✨ Agregar al Catálogo
                </button>
            </form>
        </div>

        <!-- Inventario -->
        <div class="admin-section" id="inventario">
            <h2>🧴 Gestión de Inventario de Perfumes</h2>
            <?php if (empty($productos)): ?>
                <p style="text-align: center; padding: 40px; color: var(--text-secondary);">
                    No hay perfumes en el catálogo. ¡Agrega el primer perfume!
                </p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>🖼️ Imagen</th>
                            <th>🏷️ ID</th>
                            <th>🧴 Nombre</th>
                            <th>🌟 Tipo</th>
                            <th>💰 Precio</th>
                            <th>📦 Stock</th>
                            <th>⚙️ Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $id => $p): ?>
                            <tr style="<?php echo $p['stock'] <= 5 ? 'background-color: rgba(252, 70, 107, 0.1);' : ''; ?>">
                                <td>
                                    <div class="imagen-control">
                                        <?php 
                                        $imagen_path = 'uploads/productos/' . $p['imagen'];
                                        if (file_exists($imagen_path) && !empty($p['imagen']) && $p['imagen'] !== 'default.jpg'): 
                                        ?>
                                            <img src="<?php echo $imagen_path; ?>" alt="<?php echo htmlspecialchars($p['nombre']); ?>" class="imagen-preview">
                                        <?php else: ?>
                                            <div class="imagen-placeholder">
                                                <?php echo obtener_icono_perfume($p['tipo']); ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <form method="POST" enctype="multipart/form-data" style="margin-top: 8px;">
                                            <input type="hidden" name="producto_id" value="<?php echo $id; ?>">
                                            <input type="file" name="nueva_imagen" accept="image/*" class="file-input-custom" id="imagen-<?php echo $id; ?>">
                                            <label for="imagen-<?php echo $id; ?>" class="file-input-label" style="font-size: 0.8em; padding: 4px 8px;">📷</label>
                                            <button type="submit" name="actualizar_imagen" class="btn-imagen" style="font-size: 0.8em; padding: 4px 8px;">💾</button>
                                        </form>
                                    </div>
                                </td>
                                <td><strong><?php echo htmlspecialchars($id); ?></strong></td>
                                <td>
                                    <?php echo obtener_icono_perfume($p['tipo']); ?>
                                    <strong><?php echo htmlspecialchars($p['nombre']); ?></strong>
                                </td>
                                <td>
                                    <span class="perfume-type-badge">
                                        <?php echo obtener_icono_perfume($p['tipo']); ?>
                                        <?php echo htmlspecialchars($p['tipo']); ?>
                                    </span>
                                </td>
                                <td><strong>$<?php echo number_format($p['precio'], 2); ?></strong></td>
                                <td class="<?php echo $p['stock'] <= 5 ? 'stock-out' : 'stock-ok'; ?>">
                                    <?php echo $p['stock']; ?>
                                    <?php if ($p['stock'] <= 5): ?>
                                        <span style="margin-left: 8px;">⚠️</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" style="display: flex; gap: 8px; align-items: center;">
                                        <input type="hidden" name="producto_id" value="<?php echo $id; ?>">
                                        <input type="number" name="nuevo_stock" min="0" value="<?php echo $p['stock']; ?>" 
                                               style="width: 70px; margin: 0; padding: 6px; font-size: 0.9em;">
                                        <button type="submit" name="actualizar_stock" 
                                                style="padding: 6px 10px; font-size: 0.8rem;">
                                            ✏️ Stock
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Usuarios -->
        <div class="admin-section" id="usuarios">
            <h2>👥 Clientes Registrados</h2>
            <?php if (empty($usuarios)): ?>
                <p style="text-align: center; padding: 40px; color: var(--text-secondary);">
                    No hay usuarios registrados aún.
                </p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>📧 Email</th>
                            <th>🏷️ Rol</th>
                            <th>🛍️ Total Compras</th>
                            <th>💰 Total Gastado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <?php
                            // Calcular estadísticas del usuario
                            $compras_usuario = array_filter($ventas, function($venta) use ($usuario) {
                                return $venta['cliente'] === $usuario['email'];
                            });
                            $total_compras = count($compras_usuario);
                            $total_gastado = array_sum(array_column($compras_usuario, 'total'));
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td>
                                    <span style="background: var(--accent-gradient); color: white; padding: 5px 10px; border-radius: 15px; font-size: 0.8rem;">
                                        <?php echo $usuario['rol'] === 'cliente' ? '👤' : '👨‍💼'; ?> 
                                        <?php echo htmlspecialchars($usuario['rol']); ?>
                                    </span>
                                </td>
                                <td><strong><?php echo $total_compras; ?></strong></td>
                                <td><strong>$<?php echo number_format($total_gastado, 2); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Ventas Recientes -->
        <div class="admin-section" id="ventas">
            <h2>🛍️ Historial de Ventas Recientes</h2>
            <?php if (empty($ventas)): ?>
                <p style="text-align: center; padding: 40px; color: var(--text-secondary);">
                    No hay ventas registradas aún.
                </p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>📅 Fecha</th>
                            <th>👤 Cliente</th>
                            <th>🧴 Perfume</th>
                            <th>📦 Cantidad</th>
                            <th>💰 Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($ventas, 0, 15) as $venta): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($venta['fecha'])); ?></td>
                                <td><?php echo htmlspecialchars($venta['cliente']); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($venta['producto_nombre']); ?></strong>
                                    <br>
                                    <small style="color: var(--text-secondary);">ID: <?php echo $venta['producto_id']; ?></small>
                                </td>
                                <td><?php echo $venta['cantidad']; ?></td>
                                <td><strong>$<?php echo number_format($venta['total'], 2); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Exportar Datos -->
        <div class="admin-section">
            <h2>📥 Exportar Base de Datos</h2>
            <p>Descarga los archivos de la base de datos del sistema:</p>
            <div style="margin-top: 20px;">
                <a href="exportar_db.php?file=productos.txt" class="export-link">
                    🧴 Catálogo de Perfumes
                </a>
                <a href="exportar_db.php?file=usuarios.txt" class="export-link">
                    👥 Base de Usuarios
                </a>
                <a href="exportar_db.php?file=ventas.txt" class="export-link">
                    💰 Registro de Ventas
                </a>
            </div>
        </div>
    </div>

    <script>
        // Animación suave para navegación interna
        document.querySelectorAll('.admin-nav a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Destacar elementos con stock bajo
        document.addEventListener('DOMContentLoaded', function() {
            const stockCells = document.querySelectorAll('.stock-out');
            stockCells.forEach(cell => {
                cell.parentElement.style.animation = 'pulse 2s infinite';
            });
        });
        
        // Preview de imagen al seleccionar archivo
        document.getElementById('nueva-imagen').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('preview-nueva');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; margin-top: 10px;">`;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '';
            }
        });
        
        // Preview para cada input de imagen en la tabla
        document.querySelectorAll('input[type="file"][id^="imagen-"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                const productId = this.id.replace('imagen-', '');
                
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Encontrar la imagen o placeholder correspondiente
                        const row = input.closest('tr');
                        const imageContainer = row.querySelector('.imagen-control');
                        const existingImg = imageContainer.querySelector('.imagen-preview, .imagen-placeholder');
                        
                        if (existingImg) {
                            existingImg.outerHTML = `<img src="${e.target.result}" class="imagen-preview" style="border: 2px solid #4CAF50;">`;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
        
        // Agregar animación de pulso
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0% { box-shadow: 0 0 0 0 rgba(252, 70, 107, 0.3); }
                70% { box-shadow: 0 0 0 10px rgba(252, 70, 107, 0); }
                100% { box-shadow: 0 0 0 0 rgba(252, 70, 107, 0); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>