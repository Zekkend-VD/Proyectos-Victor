# Sistema de Inventario PS5 - Versión 2.1

## 🎮 Descripción
Sistema completo de inventario y ventas de juegos PS5 con interfaz moderna y funcionalidades avanzadas.

## ✨ Características
- **Autenticación segura** con funciones mejoradas
- **Panel de administración** completo
- **Catálogo de productos** para clientes
- **Gestión de inventario** con validaciones
- **Historial de ventas** detallado
- **Diseño moderno** con CSS Flexbox y animaciones
- **Sistema de monitoreo** y debugging

## 🚀 Instalación
1. Subir todos los archivos a su servidor web
2. Asegurar permisos de escritura en archivos .txt
3. Acceder via navegador web

## 👥 Credenciales
### Administrador:
- **Email:** admin@gmail.com
- **Password:** contraseña1

### Cliente de prueba:
- **Email:** cliente@mail.com
- **Password:** cliente_password

## 📁 Estructura de archivos
```
├── index.php                     # Página de inicio de sesión
├── registro.php                  # Registro de nuevos usuarios
├── admin.php                     # Panel de administración
├── cliente.php                   # Panel de cliente
├── config.php                    # Configuración principal
├── auth_improved.php             # Funciones de autenticación mejoradas
├── inventario_safe_functions.php # Funciones seguras de inventario
├── styles.css                    # Estilos CSS modernos
├── clientes.txt                  # Base de datos de clientes
├── inventario.txt                # Base de datos de inventario
├── ventas.txt                    # Base de datos de ventas
├── uploads/                      # Directorio de imágenes
│   └── no_image.png             # Imagen placeholder
└── backups/                      # Copias de seguridad automáticas
```

## 🔧 Funcionalidades del Administrador
- Agregar nuevos productos al inventario
- Recargar stock de productos existentes
- Buscar productos por ID, nombre o descripción
- Ver historial completo de ventas
- Gestionar usuarios del sistema
- Exportar datos del sistema

## 🛒 Funcionalidades del Cliente
- Ver catálogo completo de juegos
- Buscar juegos por nombre o descripción
- Comprar juegos disponibles
- Ver historial personal de compras
- Información detallada de productos

## 🔒 Características de Seguridad
- Validación de entrada en todos los formularios
- Prevención de XSS con htmlspecialchars()
- Bloqueo de archivos durante operaciones críticas
- Funciones de autenticación mejoradas con logging
- Validación de tipos de datos
- Sistema de backup automático

## 📊 Monitoreo y Debugging
- Logs de autenticación en debug_auth.log
- Logs de errores en debug_errors.log
- Monitor de inventario (monitor_inventario.php)
- Estado del sistema (system_status.json)

## 🎨 Características del Diseño
- Interfaz moderna con Google Fonts (Poppins)
- Animaciones y transiciones CSS
- Layout responsive con Flexbox
- Tema oscuro elegante
- Efectos hover y animaciones
- Cards de productos con imágenes

## 📝 Logs y Archivos de Diagnóstico
- `fix_inventario.php` - Reparación básica del inventario
- `fix_inventario_avanzado.php` - Diagnóstico avanzado
- `fix_auth.php` - Diagnóstico de autenticación
- `test_auth.php` - Tests automatizados
- `monitor_inventario.php` - Monitoreo continuo

## 🔄 Versiones
- **v1.0:** Versión básica con PHP y CSS simple
- **v2.0:** Mejoras visuales y funcionalidades
- **v2.1:** Sistema de autenticación mejorado y funciones de seguridad

## 📞 Soporte
Para reportar problemas o solicitar características:
1. Ejecutar `php fix_auth.php` para diagnóstico
2. Revisar logs en debug_auth.log
3. Ejecutar `php monitor_inventario.php` para estado del sistema

---
*Desarrollado con ❤️ para la comunidad gamer*
