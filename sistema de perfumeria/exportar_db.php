<?php
session_start();
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
    http_response_code(403);
    die("Acceso denegado.");
}

$file = $_GET['file'] ?? '';

$allowed_files = ['productos.txt', 'usuarios.txt', 'ventas.txt'];

if (in_array($file, $allowed_files) && file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    
    readfile($file);
    exit;
} else {
    die("Archivo no encontrado o acceso no permitido.");
}
?>