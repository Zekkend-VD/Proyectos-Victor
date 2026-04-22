<?php
session_start();
require '../conexion.php';

// Verificar permisos para respaldo de BD
if (!isset($_SESSION['rol']) || !tiene_permiso('respaldo_bd')) {
    mostrar_error_permisos('respaldo_bd');
    exit;
}

// Procesar la solicitud de respaldo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generar_respaldo'])) {
    // Configuración
    $fecha = date("Y-m-d_H-i-s");
    $nombre_archivo = "respaldo_farmacia_$fecha.sql";
    $ruta_respaldo = "../backups/";
    
    // Crear carpeta de backups si no existe
    if (!is_dir($ruta_respaldo)) {
        mkdir($ruta_respaldo, 0755, true);
    }
    
    // Ruta completa del archivo
    $archivo = $ruta_respaldo . $nombre_archivo;
    
    // Obtener credenciales de la base de datos
    $servidor = "localhost";
    $usuario = "root";
    $password = "lab2"; // Tu contraseña de MySQL
    $basedatos = "farmacia";
    
    // Método 1: Usando mysqli dump (más confiable)
    $comando = "C:\\xampp\\mysql\\bin\\mysqldump.exe --user=$usuario --password=$password --host=$servidor $basedatos > \"$archivo\"";
    
    // Si estás en Linux/Mac, usar:
    // $comando = "mysqldump --user=$usuario --password=$password --host=$servidor $basedatos > \"$archivo\"";
    
    // Ejecutar el comando
    system($comando, $output);
    
}

// Función para generar respaldo manualmente
function generarRespaldoManual($conexion, $archivo) {
    $contenido = "-- Respaldo de Base de Datos Farmacia Online\n";
    $contenido .= "-- Generado: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Obtener todas las tablas
    $tablas = array();
    $result = $conexion->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $tablas[] = $row[0];
    }
    
    foreach ($tablas as $tabla) {
        // Agregar estructura de la tabla
        $contenido .= "--\n-- Estructura de tabla para `$tabla`\n--\n";
        $contenido .= "DROP TABLE IF EXISTS `$tabla`;\n";
        
        $create_result = $conexion->query("SHOW CREATE TABLE `$tabla`");
        $create_row = $create_result->fetch_array();
        $contenido .= $create_row[1] . ";\n\n";
        
        // Agregar datos de la tabla
        $contenido .= "--\n-- Volcado de datos para la tabla `$tabla`\n--\n";
        
        $data_result = $conexion->query("SELECT * FROM `$tabla`");
        if ($data_result->num_rows > 0) {
            $contenido .= "INSERT INTO `$tabla` VALUES ";
            $first = true;
            
            while ($data_row = $data_result->fetch_assoc()) {
                if (!$first) {
                    $contenido .= ",\n";
                }
                
                $values = array();
                foreach ($data_row as $value) {
                    if ($value === null) {
                        $values[] = "NULL";
                    } else {
                        $values[] = "'" . $conexion->real_escape_string($value) . "'";
                    }
                }
                
                $contenido .= "(" . implode(", ", $values) . ")";
                $first = false;
            }
            $contenido .= ";\n\n";
        }
    }
    
    // Escribir el archivo
    return file_put_contents($archivo, $contenido) !== false;
}

// Obtener lista de respaldos existentes
$backups = [];
$ruta_backups = "../backups/";
if (is_dir($ruta_backups)) {
    $archivos = scandir($ruta_backups);
    foreach ($archivos as $archivo) {
        if ($archivo != '.' && $archivo != '..' && pathinfo($archivo, PATHINFO_EXTENSION) == 'sql') {
            $ruta_completa = $ruta_backups . $archivo;
            $backups[] = [
                'nombre' => $archivo,
                'tamaño' => filesize($ruta_completa),
                'fecha' => filemtime($ruta_completa)
            ];
        }
    }
    
    // Ordenar por fecha (más reciente primero)
    usort($backups, function($a, $b) {
        return $b['fecha'] - $a['fecha'];
    });
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Respaldo de Base de Datos - Farmacia Online</title>
    <link rel="stylesheet" href="../css/estilo.css">
    <style>
        .estadisticas-backup {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .lista-backups {
            margin-top: 2rem;
        }
        
        .backup-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: white;
            border-radius: var(--border-radius);
            margin-bottom: 0.5rem;
            box-shadow: var(--shadow);
        }
        
        .backup-info {
            flex: 1;
        }
        
        .backup-nombre {
            font-weight: bold;
            margin-bottom: 0.3rem;
        }
        
        .backup-detalles {
            font-size: 0.9rem;
            color: #666;
        }
        
        .backup-acciones {
            display: flex;
            gap: 0.5rem;
        }
        
        .tamaño-archivo {
            font-family: monospace;
        }
        
        .archivo-vacio {
            color: var(--warning);
            font-style: italic;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Panel de <?php echo obtener_nombre_rol($_SESSION['rol']); ?></h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <?php if (tiene_permiso('gestion_enfermedades')): ?>
                        <li><a href="gestion_enfermedades.php">Gestión de Enfermedades</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('gestion_usuarios')): ?>
                        <li><a href="gestion_usuarios.php">Gestión de Usuarios</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('gestion_compras')): ?>
                        <li><a href="gestion_compras.php">Gestión de Compras</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('gestion_inventario')): ?>
                        <li><a href="gestion_inventario.php">Gestión de Inventario</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('estadisticas')): ?>
                        <li><a href="estadisticas.php">Gestión de estadisticas</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('respaldo_bd')): ?>
                        <li><a href="respaldo_bd.php" class="active">Respaldo BD</a></li>
                    <?php endif; ?>
                    <li><a href="../logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="admin-header">
            <h2>Respaldo de Base de Datos</h2>
            <p>Gestiona los respaldos de la base de datos del sistema</p>
        </div>

        <?php if (isset($mensaje)): ?>
            <div class="<?php echo $tipo_mensaje == 'error' ? 'error' : 'mensaje-exito'; ?>"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <div class="estadisticas-backup">
            <div class="tarjeta-estadistica">
                <h3>Total de Respaldos</h3>
                <p class="numero"><?php echo count($backups); ?></p>
            </div>
            <div class="tarjeta-estadistica">
                <h3>Último Respaldo</h3>
                <p class="numero">
                    <?php 
                    if (!empty($backups)) {
                        echo date('d/m/Y', $backups[0]['fecha']);
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </p>
            </div>
            <div class="tarjeta-estadistica">
                <h3>Espacio Utilizado</h3>
                <p class="numero">
                    <?php
                    $total_size = 0;
                    foreach ($backups as $backup) {
                        $total_size += $backup['tamaño'];
                    }
                    echo number_format($total_size / (1024 * 1024), 2) . ' MB';
                    ?>
                </p>
            </div>
        </div>

        <div class="card">
            <h3>Generar Nuevo Respaldo</h3>
            <p>Al hacer clic en el botón, se generará un respaldo completo de la base de datos en formato SQL.</p>
            <form method="post">
                <button type="submit" name="generar_respaldo" class="btn btn-primary" 
                        onclick="return confirm('¿Está seguro de generar un nuevo respaldo de la base de datos?')">
                    Generar Respaldo Completo
                </button>
            </form>
        </div>

        <div class="lista-backups">
            <h3>Respaldos Existentes</h3>
            
            <?php if (!empty($backups)): ?>
                <?php foreach($backups as $backup): ?>
                    <div class="backup-item">
                        <div class="backup-info">
                            <div class="backup-nombre"><?php echo htmlspecialchars($backup['nombre']); ?></div>
                            <div class="backup-detalles">
                                Creado: <?php echo date('d/m/Y H:i:s', $backup['fecha']); ?> | 
                                Tamaño: <span class="tamaño-archivo <?php echo $backup['tamaño'] == 0 ? 'archivo-vacio' : ''; ?>">
                                    <?php 
                                    if ($backup['tamaño'] == 0) {
                                        echo 'ARCHIVO VACÍO';
                                    } else {
                                        echo number_format($backup['tamaño'] / 1024, 2) . ' KB';
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="backup-acciones">
                            <a href="../backups/<?php echo urlencode($backup['nombre']); ?>" 
                               class="btn btn-success btn-sm" download>Descargar</a>
                            <a href="eliminar_backup.php?archivo=<?php echo urlencode($backup['nombre']); ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('¿Está seguro de eliminar este respaldo?')">Eliminar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="mensaje-vacio">
                    <div class="icono-vacio">💾</div>
                    <h3>No hay respaldos disponibles</h3>
                    <p>Genera el primer respaldo de la base de datos</p>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Farmacia Online. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>