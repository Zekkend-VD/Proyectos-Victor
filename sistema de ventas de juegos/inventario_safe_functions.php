<?php
/**
 * Funciones seguras para operaciones del inventario
 * Evitan problemas de concurrencia y corrupción de datos
 */

function safe_read_inventario() {
    $max_attempts = 3;
    $attempt = 0;
    
    while ($attempt < $max_attempts) {
        try {
            if (!file_exists("inventario.txt")) {
                return [];
            }
            
            $handle = fopen("inventario.txt", "r");
            if (!$handle) {
                throw new Exception("No se pudo abrir el archivo");
            }
            
            // Bloquear archivo para lectura
            if (flock($handle, LOCK_SH)) {
                $lines = [];
                while (($line = fgets($handle)) !== false) {
                    $line = trim($line);
                    if (!empty($line)) {
                        $lines[] = $line;
                    }
                }
                flock($handle, LOCK_UN);
                fclose($handle);
                return $lines;
            } else {
                fclose($handle);
                throw new Exception("No se pudo bloquear el archivo");
            }
        } catch (Exception $e) {
            $attempt++;
            if ($attempt >= $max_attempts) {
                error_log("Error leyendo inventario: " . $e->getMessage());
                return [];
            }
            usleep(100000); // Esperar 100ms antes del siguiente intento
        }
    }
    return [];
}

function safe_write_inventario($lines) {
    $max_attempts = 3;
    $attempt = 0;
    
    while ($attempt < $max_attempts) {
        try {
            $handle = fopen("inventario.txt", "w");
            if (!$handle) {
                throw new Exception("No se pudo abrir el archivo para escritura");
            }
            
            // Bloquear archivo para escritura
            if (flock($handle, LOCK_EX)) {
                foreach ($lines as $line) {
                    fwrite($handle, $line . "\n");
                }
                flock($handle, LOCK_UN);
                fclose($handle);
                return true;
            } else {
                fclose($handle);
                throw new Exception("No se pudo bloquear el archivo para escritura");
            }
        } catch (Exception $e) {
            $attempt++;
            if ($attempt >= $max_attempts) {
                error_log("Error escribiendo inventario: " . $e->getMessage());
                return false;
            }
            usleep(100000); // Esperar 100ms antes del siguiente intento
        }
    }
    return false;
}

function safe_update_stock($product_id, $quantity_change) {
    $lines = safe_read_inventario();
    $updated_lines = [];
    $product_found = false;
    
    foreach ($lines as $line) {
        $data = explode("|", $line);
        if (count($data) >= 6 && (int)$data[0] === (int)$product_id) {
            $current_stock = (int)$data[4];
            $new_stock = $current_stock + $quantity_change;
            
            // Validar que el stock no sea negativo
            if ($new_stock < 0) {
                error_log("Intento de stock negativo para producto $product_id");
                return false;
            }
            
            $data[4] = $new_stock;
            $product_found = true;
        }
        $updated_lines[] = implode("|", $data);
    }
    
    if (!$product_found) {
        error_log("Producto $product_id no encontrado");
        return false;
    }
    
    return safe_write_inventario($updated_lines);
}

function validate_product_data($name, $description, $price, $stock) {
    $errors = [];
    
    if (empty(trim($name))) {
        $errors[] = "El nombre del producto es obligatorio";
    }
    
    if (empty(trim($description))) {
        $errors[] = "La descripción es obligatoria";
    }
    
    if (!is_numeric($price) || (float)$price <= 0) {
        $errors[] = "El precio debe ser un número positivo";
    }
    
    if (!is_numeric($stock) || (int)$stock < 0) {
        $errors[] = "El stock debe ser un número entero no negativo";
    }
    
    return $errors;
}
?>