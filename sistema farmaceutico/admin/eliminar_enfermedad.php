<?php
session_start();
require '../conexion.php';
verificar_permiso('gestion_enfermedades');
// Verificar permisos para gestión de enfermedades
if (!isset($_SESSION['rol']) || !tiene_permiso('gestion_enfermedades')) {
    header('Location: ../login.php');
    exit;
}

$enfermedad_id = intval($_GET['id']);

// Verificar que la enfermedad existe
$sql_verificar = "SELECT * FROM enfermedades WHERE id = $enfermedad_id";
$result_verificar = $conexion->query($sql_verificar);

if ($result_verificar->num_rows > 0) {
    // Primero eliminar los medicamentos asociados
    $sql_eliminar_medicamentos = "DELETE FROM medicamentos WHERE enfermedad_id = $enfermedad_id";
    $conexion->query($sql_eliminar_medicamentos);
    
    // Luego eliminar la enfermedad
    $sql_eliminar = "DELETE FROM enfermedades WHERE id = $enfermedad_id";
    if ($conexion->query($sql_eliminar)) {
        $mensaje = "Enfermedad y sus medicamentos eliminados correctamente";
    } else {
        $error = "Error al eliminar enfermedad";
    }
} else {
    $error = "Enfermedad no encontrada";
}

// Redirigir de vuelta a la gestión de enfermedades
if (isset($mensaje)) {
    header('Location: gestion_enfermedades.php?mensaje=' . urlencode($mensaje));
} else {
    header('Location: gestion_enfermedades.php?error=' . urlencode($error));
}
exit;
?>