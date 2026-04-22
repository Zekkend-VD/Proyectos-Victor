🧴 Sistema de Perfumes – Tienda Online con PHP

🚀 Vista previa
El sistema se ejecuta sobre un servidor web con PHP (Apache recomendado).
El punto de entrada es index.php, donde los usuarios pueden iniciar sesión o registrarse como clientes.
Los administradores acceden a un panel de gestión completo, mientras que los clientes pueden navegar por el catálogo, agregar productos al carrito y realizar compras.

Nota: No se requiere base de datos – todos los datos se almacenan en archivos de texto (productos.txt, usuarios.txt, ventas.txt).

🧠 Acerca del proyecto
Este proyecto demuestra el desarrollo de un e‑commerce funcional utilizando únicamente PHP, HTML/CSS y almacenamiento en archivos planos.
Es ideal para aprender conceptos como:

Autenticación y sesiones (password_hash, password_verify, $_SESSION).

CRUD de productos (crear, leer, actualizar stock, imágenes).

Carrito de compras con actualización y eliminación de items.

Procesamiento de compras (actualización de stock y registro de ventas).

Panel de administración con estadísticas, gestión de inventario y exportación de datos.

Subida de imágenes para productos (con validación de tipo y tamaño).

Búsqueda y filtrado de productos por tipo.

El diseño es moderno y responsivo, con gradientes, glassmorfismo y animaciones suaves.

⚙️ Tecnologías utilizadas
PHP 7.4+ – Lógica del servidor, manejo de sesiones, procesamiento de formularios.

HTML5 + CSS3 – Interfaz de usuario, diseño responsivo, animaciones.

JavaScript – Mejoras interactivas (previsualización de imágenes, filtros en tiempo real, scroll suave).

Archivos de texto (.txt) – Almacenamiento de productos, usuarios y ventas.

Subida de archivos – Imágenes de productos almacenadas en uploads/productos/.

🧩 Funcionalidades principales
Módulo	Descripción
Autenticación	Login y registro de clientes. El administrador está predefinido en admin_config.php.
Catálogo de perfumes	Vista de productos con imagen, tipo, precio, stock, descripción.
Búsqueda y filtros	Por nombre/tipo y selector de categoría.
Carrito de compras	Agregar productos, modificar cantidades, eliminar items, vaciar carrito.
Proceso de compra	Confirmación, actualización de stock, registro de ventas en ventas.txt.
Panel de administración	Estadísticas generales, gestión de inventario (agregar, editar stock, cambiar imagen), listado de usuarios y ventas.
Exportación de datos	Descarga de archivos productos.txt, usuarios.txt, ventas.txt.
Subida de imágenes	Para cada producto, con validación de formato (JPG, PNG, WEBP) y tamaño (máx. 5MB).
🛠️ Instalación y uso
Requisitos del servidor
Servidor web con PHP (Apache, XAMPP, WAMP, MAMP, etc.)

PHP 7.4 o superior

Extensiones comunes: fileinfo, gd (opcional para manipulación de imágenes)

Permisos de escritura en los archivos .txt y en el directorio uploads/productos/

Pasos de instalación
Clona o descarga este repositorio en el directorio raíz de tu servidor web (ej: htdocs en XAMPP).

Asegura los permisos:

En Linux/macOS: chmod 666 productos.txt usuarios.txt ventas.txt y chmod 777 uploads/productos/ (o al menos permisos de escritura).

En Windows: los permisos suelen ser suficientes con el servidor ejecutándose como administrador.

Accede vía navegador a:

text
http://localhost/ruta-del-proyecto/index.php
