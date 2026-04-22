<?php
// Configuración de errores para debugging
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

// Log de errores personalizado
ini_set("log_errors", 1);
ini_set("error_log", __DIR__ . "/debug_errors.log");

// Función para debugging
function debug_log($message) {
    $timestamp = date("Y-m-d H:i:s");
    $log_message = "[$timestamp] $message" . PHP_EOL;
    file_put_contents(__DIR__ . "/debug_auth.log", $log_message, FILE_APPEND | LOCK_EX);
}

// Función mejorada de autenticación con debugging
function authenticate_user($email, $password) {
    debug_log("Intento de autenticación para: $email");
    
    // Verificar admin
    if ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
        debug_log("Autenticación admin exitosa");
        return [
            "success" => true,
            "user_id" => 1,
            "user_name" => "Administrador",
            "role" => "admin"
        ];
    }
    
    // Verificar clientes
    if (!file_exists("clientes.txt")) {
        debug_log("Error: archivo clientes.txt no existe");
        return ["success" => false, "error" => "Archivo de clientes no encontrado"];
    }
    
    $clientes = file("clientes.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    debug_log("Clientes cargados: " . count($clientes));
    
    foreach ($clientes as $line) {
        $data = explode("|", $line);
        if (count($data) >= 5 && $data[2] === $email && $data[3] === $password) {
            debug_log("Autenticación cliente exitosa: " . $data[1]);
            return [
                "success" => true,
                "user_id" => $data[0],
                "user_name" => $data[1],
                "role" => $data[4]
            ];
        }
    }
    
    debug_log("Autenticación fallida para: $email");
    return ["success" => false, "error" => "Credenciales inválidas"];
}

// Función para registro seguro
function register_user($name, $email, $password, $confirm_password) {
    debug_log("Intento de registro para: $email");
    
    // Validaciones
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        return ["success" => false, "error" => "Todos los campos son obligatorios"];
    }
    
    if ($password !== $confirm_password) {
        return ["success" => false, "error" => "Las contraseñas no coinciden"];
    }
    
    if (strlen($password) < 6) {
        return ["success" => false, "error" => "La contraseña debe tener al menos 6 caracteres"];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ["success" => false, "error" => "Email inválido"];
    }
    
    // Verificar si el email ya existe
    $clientes = file_exists("clientes.txt") ? file("clientes.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    
    foreach ($clientes as $line) {
        $data = explode("|", $line);
        if (isset($data[2]) && $data[2] === $email) {
            debug_log("Email ya existe: $email");
            return ["success" => false, "error" => "Este email ya está registrado"];
        }
    }
    
    // Obtener nuevo ID
    $last_id = 1;
    foreach ($clientes as $line) {
        $data = explode("|", $line);
        if (isset($data[0]) && is_numeric($data[0])) {
            $last_id = max($last_id, (int)$data[0]);
        }
    }
    $new_id = $last_id + 1;
    
    // Crear nueva línea
    $new_user_line = "$new_id|$name|$email|$password|cliente" . PHP_EOL;
    
    // Escribir al archivo
    if (file_put_contents("clientes.txt", $new_user_line, FILE_APPEND | LOCK_EX)) {
        debug_log("Usuario registrado exitosamente: $email (ID: $new_id)");
        return ["success" => true, "message" => "Usuario registrado exitosamente"];
    } else {
        debug_log("Error escribiendo archivo para: $email");
        return ["success" => false, "error" => "Error interno del servidor"];
    }
}
?>