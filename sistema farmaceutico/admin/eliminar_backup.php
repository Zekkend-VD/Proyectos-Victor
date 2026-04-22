<?php
session_start();
require '../conexion.php';

// Verificar permisos para respaldo de BD
if (!isset($_SESSION['rol']) || !tiene_permiso('respaldo_bd')) {
    mostrar_error_permisos('respaldo_bd');
    exit;
}

$archivo = $_GET['archivo'] ?? '';

// Validar que el archivo sea seguro
if (empty($archivo) || strpos($archivo, '..') !== false) {
    header('Location: respaldo_bd.php?error=Archivo no válido');
    exit;
}

$ruta_archivo = "../backups/" . $archivo;

// Verificar que el archivo existe y es un archivo SQL
if (file_exists($ruta_archivo) && pathinfo($ruta_archivo, PATHINFO_EXTENSION) == 'sql') {
    if (unlink($ruta_archivo)) {
        header('Location: respaldo_bd.php?mensaje=Respaldo eliminado correctamente');
    } else {
        header('Location: respaldo_bd.php?error=Error al eliminar el respaldo');
    }
} else {
    header('Location: respaldo_bd.php?error=Archivo no encontrado');
}
exit;