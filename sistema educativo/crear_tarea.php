<?php
session_start();

// Verificar que el usuario esté logueado y sea profesor
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'profesor') {
    header("Location: index.php");
    exit;
}

if ($_POST) {
    $profesor = $_SESSION['usuario'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha_entrega = $_POST['fecha_entrega'];
    
    // Validar que todos los campos estén presentes
    if (empty($titulo) || empty($descripcion) || empty($fecha_entrega)) {
        echo "<script>
            alert('Todos los campos son obligatorios.');
            window.history.back();
        </script>";
        exit;
    }
    
    // Crear directorio si no existe
    if (!file_exists('data')) {
        mkdir('data', 0777, true);
    }
    
    // Guardar tarea en archivo
    $archivo = "data/tareas.txt";
    $datos = "$profesor|$titulo|$descripcion|$fecha_entrega|" . date('Y-m-d H:i:s') . PHP_EOL;
    
    if (file_put_contents($archivo, $datos, FILE_APPEND | LOCK_EX)) {
        echo "<script>
            alert('Tarea creada exitosamente.');
            window.location.href = 'dashboard.php';
        </script>";
    } else {
        echo "<script>
            alert('Error al crear la tarea. Inténtalo de nuevo.');
            window.history.back();
        </script>";
    }
} else {
    // Si no es POST, redirigir al dashboard
    header("Location: dashboard.php");
    exit;
}
?>