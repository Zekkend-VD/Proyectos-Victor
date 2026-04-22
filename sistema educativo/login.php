<?php
session_start();

if ($_POST) {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    
    // Tipos de usuarios a verificar
    $tipos_usuarios = ['admin', 'profesor', 'estudiante'];
    $login_exitoso = false;
    $tipo_encontrado = '';
    $datos_usuario = [];
    
    // Verificar en todos los archivos de usuarios
    foreach ($tipos_usuarios as $tipo) {
        $archivo = "data/{$tipo}s.txt";
        
        // Verificar si el archivo existe y tiene contenido
        if (file_exists($archivo) && filesize($archivo) > 0) {
            $lineas = file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lineas as $linea) {
                $datos = explode('|', $linea);
                // Verificar que la línea tenga al menos 4 campos (usuario, password, nombre, sexo)
                if (count($datos) >= 4 && $datos[0] === $usuario) {
                    if (password_verify($password, $datos[1])) {
                        // Login exitoso
                        $_SESSION['usuario'] = $usuario;
                        $_SESSION['tipo'] = $tipo;
                        $_SESSION['nombre'] = $datos[2];
                        
                        if ($tipo === 'profesor' && isset($datos[4])) {
                            $_SESSION['especialidad'] = $datos[4];
                        }
                        
                        $login_exitoso = true;
                        $tipo_encontrado = $tipo;
                        $datos_usuario = $datos;
                        break 2; // Salir de ambos bucles
                    }
                }
            }
        }
    }
    
    if ($login_exitoso) {
        // Login exitoso - redirigir al dashboard
        header("Location: dashboard.php");
        exit;
    } else {
        // Usuario o contraseña incorrectos
        echo "<script>
            alert('Usuario o contraseña incorrectos.');
            window.location.href = 'index.php';
        </script>";
    }
} else {
    // Si no es POST, redirigir al index
    header("Location: index.php");
    exit;
}
?>