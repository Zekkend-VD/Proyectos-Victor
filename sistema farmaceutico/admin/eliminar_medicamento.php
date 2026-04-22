<?php
session_start();
require '../conexion.php';
verificar_permiso('gestion_enfermedades');

if (!isset($_SESSION['es_admin']) || !$_SESSION['es_admin']) {
    header('Location: ../login.php');
    exit;
}

$medicamento_id = intval($_GET['id']);
$enfermedad_id = intval($_GET['enfermedad_id']);

// Verificar que el medicamento existe y pertenece a la enfermedad
$sql_verificar = "SELECT * FROM medicamentos WHERE id = $medicamento_id AND enfermedad_id = $enfermedad_id";
$result_verificar = $conexion->query($sql_verificar);

if ($result_verificar->num_rows > 0) {
    // Eliminar el medicamento
    $sql_eliminar = "DELETE FROM medicamentos WHERE id = $medicamento_id";
    if ($conexion->query($sql_eliminar)) {
        $mensaje = "Medicamento eliminado correctamente";
    } else {
        $error = "Error al eliminar medicamento";
    }
} else {
    $error = "Medicamento no encontrado";
}

// Redirigir de vuelta a la edición de la enfermedad
if (isset($mensaje)) {
    header('Location: editar_enfermedad.php?id=' . $enfermedad_id . '&mensaje=' . urlencode($mensaje));
} else {
    header('Location: editar_enfermedad.php?id=' . $enfermedad_id . '&error=' . urlencode($error));
}
exit;
?>