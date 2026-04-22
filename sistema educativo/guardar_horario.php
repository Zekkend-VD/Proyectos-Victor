<?php
session_start();

// Verificar que el usuario esté logueado y sea administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_POST) {
    $fechas = $_POST['fechas'] ?? [];
    $eventos = $_POST['eventos'] ?? [];
    
    // Validar que haya al menos un evento
    if (empty($fechas) || empty($eventos)) {
        echo "<script>
            alert('Debe haber al menos un evento en el horario.');
            window.history.back();
        </script>";
        exit;
    }
    
    // Crear directorio si no existe
    if (!file_exists('data')) {
        mkdir('data', 0777, true);
    }
    
    // Guardar horario en archivo
    $archivo = "data/horario.txt";
    $contenido = '';
    
    for ($i = 0; $i < count($fechas); $i++) {
        if (!empty($fechas[$i]) && !empty($eventos[$i])) {
            $contenido .= $fechas[$i] . '|' . $eventos[$i] . PHP_EOL;
        }
    }
    
    if (file_put_contents($archivo, $contenido, LOCK_EX)) {
        echo "<script>
            alert('Horario académico actualizado exitosamente.');
            window.location.href = 'dashboard.php';
        </script>";
    } else {
        echo "<script>
            alert('Error al guardar el horario. Inténtalo de nuevo.');
            window.history.back();
        </script>";
    }
} else {
    // Si no es POST, redirigir al dashboard
    header("Location: dashboard.php");
    exit;
}
?>