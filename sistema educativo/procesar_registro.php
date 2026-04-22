<?php
session_start();

if ($_POST) {
    $tipo = $_POST['tipo'];
    $nombre = $_POST['nombre'];
    $sexo = $_POST['sexo'];
    $usuario = $_POST['usuario'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $especialidad = $_POST['especialidad'] ?? '';
    
    // Verificar límite de administradores
    if ($tipo === 'admin') {
        $admin_count = 0;
        if (file_exists('data/admins.txt')) {
            $admins = file('data/admins.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $admin_count = count($admins);
        }
        
        if ($admin_count >= 2) {
            echo "<script>
                alert('Límite de administradores alcanzado. Solo se permiten 2 administradores.');
                window.location.href = 'index.php';
            </script>";
            exit;
        }
    }
    
    // Crear directorio si no existe
    if (!file_exists('data')) {
        mkdir('data', 0777, true);
    }
    
    // Guardar en archivo TXT según el tipo
    $archivo = "data/{$tipo}s.txt";
    
    if ($tipo === 'profesor') {
        $datos = "$usuario|$password|$nombre|$sexo|$especialidad|" . date('Y-m-d H:i:s') . PHP_EOL;
    } else {
        $datos = "$usuario|$password|$nombre|$sexo|" . date('Y-m-d H:i:s') . PHP_EOL;
    }
    
    // Verificar si el archivo se crea correctamente
    if (file_put_contents($archivo, $datos, FILE_APPEND | LOCK_EX)) {
        echo "<script>
            alert('Registro exitoso. Ahora puedes iniciar sesión.');
            window.location.href = 'index.php';
        </script>";
    } else {
        echo "<script>
            alert('Error en el registro. Verifica los permisos del servidor.');
            window.history.back();
        </script>";
    }
} else {
    header("Location: index.php");
    exit;
}
?>