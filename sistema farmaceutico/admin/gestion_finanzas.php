<?php
session_start();
require '../conexion.php';

// Verificar permisos para gestión de finanzas
if (!isset($_SESSION['rol']) || !tiene_permiso('gestion_finanzas')) {
    mostrar_error_permisos('gestion_finanzas');
    exit;
}

// Obtener estadísticas financieras
$sql_ingresos = "SELECT SUM(total) as total_ingresos FROM compras WHERE estado = 'completada'";
$result_ingresos = $conexion->query($sql_ingresos);
$ingresos = $result_ingresos->fetch_assoc();
$total_ingresos = $ingresos['total_ingresos'] ?? 0;

$sql_gastos = "SELECT SUM(monto) as total_gastos FROM pagos_trabajadores WHERE estado = 'pagado'";
$result_gastos = $conexion->query($sql_gastos);
$gastos = $result_gastos->fetch_assoc();
$total_gastos = $gastos['total_gastos'] ?? 0;

$ganancias_netas = $total_ingresos - $total_gastos;

// Obtener compras del mes actual para gráfico
$sql_compras_mes = "SELECT 
                    DATE_FORMAT(fecha_compra, '%Y-%m-%d') as fecha,
                    SUM(total) as total_dia
                    FROM compras 
                    WHERE estado = 'completada' 
                    AND fecha_compra >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY fecha 
                    ORDER BY fecha";
$result_compras_mes = $conexion->query($sql_compras_mes);
$compras_por_dia = [];
while($row = $result_compras_mes->fetch_assoc()) {
    $compras_por_dia[$row['fecha']] = $row['total_dia'];
}

// Procesar pago a trabajador
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['realizar_pago'])) {
    $trabajador_nombre = $conexion->real_escape_string($_POST['trabajador_nombre']);
    $trabajador_rol = $conexion->real_escape_string($_POST['trabajador_rol']);
    $monto = floatval($_POST['monto']);
    $concepto = $conexion->real_escape_string($_POST['concepto']);
    $admin_id = $_SESSION['usuario_id'];

    $sql_pago = "INSERT INTO pagos_trabajadores (trabajador_nombre, trabajador_rol, monto, concepto, admin_id, estado) 
                 VALUES ('$trabajador_nombre', '$trabajador_rol', $monto, '$concepto', $admin_id, 'pagado')";

}

// Obtener historial de pagos
$sql_pagos = "SELECT * FROM pagos_trabajadores ORDER BY fecha_pago DESC";
$result_pagos = $conexion->query($sql_pagos);

// Obtener trabajadores frecuentes para sugerencias
$trabajadores_frecuentes = [
    ['nombre' => 'Farmacéutico Principal', 'rol' => 'farmaceutico'],
    ['nombre' => 'Almacenista', 'rol' => 'almacenista'],
    ['nombre' => 'Asistente de Farmacia', 'rol' => 'asistente'],
    ['nombre' => 'Encargado de BD', 'rol' => 'encargado_ba se_datos']
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión Financiera - Farmacia Online</title>
    <link rel="stylesheet" href="../css/estilo.css">
    <style>
        .grafico-simple {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }
        
        .barra-dia {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .barra-dia .fecha {
            width: 100px;
            font-size: 0.9rem;
        }
        
        .barra-dia .barra {
            flex: 1;
            height: 20px;
            background: var(--gradient-primary);
            border-radius: 10px;
            margin: 0 1rem;
            position: relative;
        }
        
        .barra-dia .monto {
            width: 80px;
            text-align: right;
            font-weight: bold;
        }
        
        .resumen-financiero {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .tarjeta-financiera {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
        }
        
        .tarjeta-financiera.ingresos {
            border-left: 4px solid var(--success);
        }
        
        .tarjeta-financiera.gastos {
            border-left: 4px solid var(--danger);
        }
        
        .tarjeta-financiera.ganancias {
            border-left: 4px solid var(--primary);
        }
        
        .sugerencia-trabajador {
            background: #f8f9fa;
            border: 1px dashed #dee2e6;
            border-radius: 8px;
            padding: 0.8rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .sugerencia-trabajador:hover {
            background: var(--primary-glow);
            border-color: var(--primary);
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Panel de <?php echo obtener_nombre_rol($_SESSION['rol']); ?></h1>
            <nav>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <?php if (tiene_permiso('gestion_enfermedades')): ?>
                        <li><a href="gestion_enfermedades.php">Gestión de Enfermedades</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('gestion_usuarios')): ?>
                        <li><a href="gestion_usuarios.php">Gestión de Usuarios</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('gestion_compras')): ?>
                        <li><a href="gestion_compras.php">Gestión de Compras</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('gestion_inventario')): ?>
                        <li><a href="gestion_inventario.php">Gestión de Inventario</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('estadisticas')): ?>
                        <li><a href="estadisticas.php">Estadísticas</a></li>
                    <?php endif; ?>
                    <?php if (tiene_permiso('gestion_finanzas')): ?>
                        <li><a href="gestion_finanzas.php" class="active">Gestión Financiera</a></li>
                    <?php endif; ?>
                    <li><a href="../logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="admin-header">
            <h2>Gestión Financiera</h2>
            <p>Controla los ingresos, gastos y realiza pagos a trabajadores</p>
        </div>

        <?php if (isset($mensaje)): ?>
            <div class="mensaje-exito"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Resumen Financiero -->
        <div class="resumen-financiero">
            <div class="tarjeta-financiera ingresos">
                <h3>Ingresos Totales</h3>
                <p class="numero" style="color: var(--success);">$<?php echo number_format($total_ingresos, 2); ?></p>
                <p class="etiqueta">Ventas completadas</p>
            </div>
            
            <div class="tarjeta-financiera gastos">
                <h3>Gastos en Nómina</h3>
                <p class="numero" style="color: var(--danger);">$<?php echo number_format($total_gastos, 2); ?></p>
                <p class="etiqueta">Pagos a trabajadores</p>
            </div>
            
            <div class="tarjeta-financiera ganancias">
                <h3>Ganancias Netas</h3>
                <p class="numero" style="color: var(--primary);">$<?php echo number_format($ganancias_netas, 2); ?></p>
                <p class="etiqueta">Ingresos - Gastos</p>
            </div>
        </div>

        <div class="grafico-simple">
            <h3>Ingresos Últimos 30 Días</h3>
            <?php if (!empty($compras_por_dia)): ?>
                <?php 
                $max_venta = max($compras_por_dia);
                foreach($compras_por_dia as $fecha => $total): 
                    $porcentaje = $max_venta > 0 ? ($total / $max_venta) * 100 : 0;
                ?>
                    <div class="barra-dia">
                        <div class="fecha"><?php echo date('d/m', strtotime($fecha)); ?></div>
                        <div class="barra" style="width: <?php echo $porcentaje; ?>%"></div>
                        <div class="monto">$<?php echo number_format($total, 2); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="texto-centro">No hay ingresos en los últimos 30 días</p>
            <?php endif; ?>
        </div>

        <!-- Formulario para pagar trabajadores -->
        <div class="form-container">
            <h3>Realizar Pago a Trabajador</h3>
            
            <div style="margin-bottom: 1.5rem;">
                <h4>Trabajadores Frecuentes:</h4>
                <?php foreach($trabajadores_frecuentes as $trabajador): ?>
                    <div class="sugerencia-trabajador" onclick="seleccionarTrabajador('<?php echo $trabajador['nombre']; ?>', '<?php echo $trabajador['rol']; ?>')">
                        <strong><?php echo $trabajador['nombre']; ?></strong> - <?php echo ucfirst($trabajador['rol']); ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <form method="post">
                <div class="campo">
                    <label for="trabajador_nombre">Nombre del Trabajador:</label>
                    <input type="text" id="trabajador_nombre" name="trabajador_nombre" required 
                           placeholder="Nombre completo del trabajador">
                </div>

                <div class="campo">
                    <label for="trabajador_rol">Rol/Cargo:</label>
                    <input type="text" id="trabajador_rol" name="trabajador_rol" required 
                           placeholder="Ej: Farmacéutico, Almacenista, etc.">
                </div>

                <div class="campo">
                    <label for="monto">Monto del Pago ($):</label>
                    <input type="number" id="monto" name="monto" step="0.01" min="0.01" required 
                           placeholder="0.00">
                </div>

                <div class="campo">
                    <label for="concepto">Concepto del Pago:</label>
                    <select id="concepto" name="concepto" required>
                        <option value="">Seleccionar concepto...</option>
                        <option value="Salario mensual">Salario mensual</option>
                        <option value="Bono por desempeño">Bono por desempeño</option>
                        <option value="Pago de horas extras">Pago de horas extras</option>
                        <option value="Viáticos">Viáticos</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <div class="acciones-form">
                    <button type="submit" name="realizar_pago" class="btn btn-confirmar">Realizar Pago</button>
                    <button type="reset" class="btn btn-outline">Limpiar Formulario</button>
                </div>
            </form>
        </div>

        <!-- Historial de pagos -->
        <h3>Historial de Pagos</h3>
        <div class="tabla-contenedor">
            <table class="tabla-admin">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Trabajador</th>
                        <th>Rol</th>
                        <th>Monto</th>
                        <th>Concepto</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_pagos && $result_pagos->num_rows > 0): ?>
                        <?php while($pago = $result_pagos->fetch_assoc()): ?>
                            <tr>
                                <td class="texto-centro"><?php echo $pago['id']; ?></td>
                                <td><?php echo htmlspecialchars($pago['trabajador_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($pago['trabajador_rol']); ?></td>
                                <td class="texto-derecha">
                                    <strong>$<?php echo number_format($pago['monto'], 2); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($pago['concepto']); ?></td>
                                <td class="texto-centro">
                                    <?php echo date('d/m/Y H:i', strtotime($pago['fecha_pago'])); ?>
                                </td>
                                <td class="texto-centro">
                                    <span class="estado <?php echo $pago['estado']; ?>">
                                        <?php echo ucfirst($pago['estado']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="texto-centro">
                                <div class="mensaje-vacio">
                                    <p>No hay pagos registrados</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2023 Farmacia Online. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        function seleccionarTrabajador(nombre, rol) {
            document.getElementById('trabajador_nombre').value = nombre;
            document.getElementById('trabajador_rol').value = rol;
            
            // Sugerir monto basado en el rol
            let montoSugerido = 0;
            switch(rol) {
                case 'farmaceutico':
                    montoSugerido = 1200;
                    break;
                case 'almacenista':
                    montoSugerido = 800;
                    break;
                case 'asistente':
                    montoSugerido = 600;
                    break;
                case 'encargado_base_datos':
                    montoSugerido = 1000;
                    break;
            }
            
            if (montoSugerido > 0) {
                document.getElementById('monto').value = montoSugerido;
                document.getElementById('concepto').value = 'Salario mensual';
            }
        }

        // Calcular totales al cambiar el monto
        document.getElementById('monto')?.addEventListener('change', function() {
            const monto = parseFloat(this.value) || 0;
            const totalGastos = <?php echo $total_gastos; ?>;
            const totalIngresos = <?php echo $total_ingresos; ?>;
            
            if (monto > 0) {
                const nuevoTotalGastos = totalGastos + monto;
                const nuevasGanancias = totalIngresos - nuevoTotalGastos;
                
                console.log(`Si realizas este pago:
                - Gastos totales: $${nuevoTotalGastos.toFixed(2)}
                - Ganancias netas: $${nuevasGanancias.toFixed(2)}`);
            }
        });
    </script>
</body>
</html>