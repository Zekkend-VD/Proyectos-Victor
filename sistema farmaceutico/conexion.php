<?php
$servidor = "localhost";
$usuario = "root";
$password = "";
$basedatos = "farmacia";

$conexion = new mysqli($servidor, $usuario, $password, $basedatos);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
$conexion->set_charset("utf8mb4");

// Definición de roles y permisos
$roles_permisos = [
    'admin' => [
        'gestion_enfermedades' => true,
        'gestion_usuarios' => true,
        'gestion_compras' => true,
        'gestion_inventario' => true,
        'estadisticas' => true,
        'respaldo_bd' => true,
        'solicitar_productos' => true,
        'gestion_finanzas' => true,
        'nombre' => 'Administrador'
    ],
    'farmaceutico' => [
        'gestion_enfermedades' => true,
        'gestion_usuarios' => false,
        'gestion_compras' => false,
        'gestion_inventario' => false,
        'estadisticas' => false,
        'respaldo_bd' => false,
        'gestion_finanzas' => false,
        'solicitar_productos' => false,
        'nombre' => 'Farmacéutico'
    ],
    'almacenista' => [
        'gestion_enfermedades' => false,
        'gestion_usuarios' => false,
        'gestion_compras' => false,
        'gestion_inventario' => true,
        'estadisticas' => false,
        'respaldo_bd' => false,
        'gestion_finanzas' => false,
        'solicitar_productos' => true,
        'nombre' => 'Almacenista'
    ],
    'asistente' => [
        'gestion_enfermedades' => false,
        'gestion_usuarios' => false,
        'gestion_compras' => true,
        'gestion_inventario' => false,
        'estadisticas' => false,
        'respaldo_bd' => false,
        'gestion_finanzas' => false,
        'solicitar_productos' => false,
        'nombre' => 'Asistente de Farmacia'
    ],
    'encargado_base_datos' => [
        'gestion_enfermedades' => false,
        'gestion_usuarios' => false,
        'gestion_compras' => false,
        'gestion_inventario' => false,
        'estadisticas' => false,
        'respaldo_bd' => true,
        'solicitar_productos' => false,
        'gestion_finanzas' => false,
        'nombre' => 'Encargado de Base de Datos'
    ],
    'proveedor' => [
        'gestion_enfermedades' => false,
        'gestion_usuarios' => false,
        'gestion_compras' => false,
        'gestion_inventario' => false,
        'estadisticas' => false,
        'respaldo_bd' => false,
        'gestion_finanzas' => false,
        'gestion_pedidos_proveedor' => true,
        'solicitar_productos' => false,
        'nombre' => 'Proveedor'
]
];

// Función para verificar permisos
function tiene_permiso($seccion) {
    global $roles_permisos;
    
    if (!isset($_SESSION['rol']) || !isset($roles_permisos[$_SESSION['rol']])) {
        return false;
    }
    
    return $roles_permisos[$_SESSION['rol']][$seccion] ?? false;
}

// Función para obtener el nombre del rol
function obtener_nombre_rol($rol) {
    global $roles_permisos;
    return $roles_permisos[$rol]['nombre'] ?? 'Usuario';
}

// Función para mostrar error de permisos
function mostrar_error_permisos($seccion_intentada = '') {
    $rol_actual = $_SESSION['rol'] ?? 'invitado';
    $nombre_rol_actual = obtener_nombre_rol($rol_actual);
    
    // Nombres de las secciones
    $nombres_secciones = [
        'gestion_enfermedades' => 'Gestión de Enfermedades',
        'gestion_usuarios' => 'Gestión de Usuarios',
        'gestion_compras' => 'Gestión de Compras',
        'gestion_inventario' => 'Gestión de Inventario',
        'estadisticas' => 'estadisticas'
    ];
    
    $nombre_seccion = $nombres_secciones[$seccion_intentada] ?? 'esta sección';
    
    echo "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>Error de Permisos - Farmacia Online</title>
        <link rel='stylesheet' href='../css/estilo.css'>
    </head>
    <body>
        <header>
            <div class='container'>
                <h1>Panel de " . htmlspecialchars($nombre_rol_actual) . "</h1>
                <nav>
                    <ul>
                        <li><a href='index.php'>Inicio</a></li>
                        <li><a href='../logout.php'>Cerrar Sesión</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <main class='container'>
            <div class='error' style='text-align: center; padding: 3rem;'>
                <div style='font-size: 4rem; margin-bottom: 1rem;'>🚫</div>
                <h2>Acceso No Permitido</h2>
                <p style='font-size: 1.2rem; margin: 1.5rem 0;'>
                    <strong>Su rol de " . htmlspecialchars($nombre_rol_actual) . " no tiene permisos para acceder a la sección de " . htmlspecialchars($nombre_seccion) . ".</strong>
                </p>
                <p style='color: #666; margin-bottom: 2rem;'>
                    Esta función está restringida para su tipo de usuario. 
                    Si cree que esto es un error, contacte al administrador del sistema.
                </p>
                <a href='index.php' class='btn btn-primary'>Volver al Inicio</a>
            </div>
        </main>

        <footer>
            <div class='container'>
                <p>&copy; 2023 Farmacia Online. Todos los derechos reservados.</p>
            </div>
        </footer>
    </body>
    </html>
    ";
    exit;
}

// Función para verificar permisos y mostrar error si no tiene
function verificar_permiso($seccion) {
    if (!isset($_SESSION['rol'])) {
        header('Location: ../login.php');
        exit;
    }
    
    if (!tiene_permiso($seccion)) {
        mostrar_error_permisos($seccion);
    }
}
// Función para generar respaldo manual de la base de datos
function generarRespaldoBD($conexion, $ruta_archivo) {
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
        if ($create_result) {
            $create_row = $create_result->fetch_array();
            $contenido .= $create_row[1] . ";\n\n";
        }
        
        // Agregar datos de la tabla
        $contenido .= "--\n-- Volcado de datos para la tabla `$tabla`\n--\n";
        
        $data_result = $conexion->query("SELECT * FROM `$tabla`");
        if ($data_result && $data_result->num_rows > 0) {
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
        } else {
            $contenido .= "-- No hay datos en la tabla $tabla\n\n";
        }
    }
    
    // Escribir el archivo
    return file_put_contents($ruta_archivo, $contenido) !== false;
}
?>