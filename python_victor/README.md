🐍 Portafolio de Ejercicios Interactivos

🚀 Vista previa
El proyecto funciona a través de un servidor web con soporte CGI.
El punto de entrada es index.py, que muestra un menú principal con todas las categorías de ejercicios.
Al seleccionar un ejercicio, el servidor ejecuta el script Python y devuelve el HTML generado.

⚠️ Importante: Estos scripts están diseñados para ejecutarse en un servidor web con CGI (por ejemplo, Apache con mod_cgi o mod_wsgi). No funcionan si solo abres los archivos .py directamente en el navegador.

🧠 Acerca del proyecto
Este portafolio nace como una herramienta educativa para demostrar el uso de Python como lenguaje de servidor web mediante CGI (Common Gateway Interface). Cada ejercicio es autónomo y muestra un concepto diferente:

Manejo de formularios (cgi.FieldStorage)

Fechas y zonas horarias (datetime, pytz)

Manipulación de strings y números

Estructuras de control (bucles, condicionales)

Funciones y algoritmos (números perfectos, palíndromos, números romanos)

Juegos interactivos (ahorcado, piedra-papel-tijera, dados)

Conversores (temperatura, monedas, unidades)

Generadores (contraseñas, chistes, frases motivadoras)

El diseño visual es moderno y oscuro, con gradientes, bordes brillantes y efectos hover, todo incrustado directamente en los estilos de cada script.

⚙️ Tecnologías utilizadas
Python 3 – Lógica de servidor y generación de HTML.

módulo cgi – Para recibir datos del formulario.

módulo cgitb – Para depuración (muestra errores en el navegador).

datetime / pytz – Manejo de fechas y zonas horarias.

urllib.request + json – Para obtener tasas de cambio actualizadas (Ejercicio 28).

random – Para juegos y generadores aleatorios.

HTML5 + CSS3 – Interfaz de usuario, diseño responsivo y efectos neon.

🧩 Lista de ejercicios
ID	Nombre	Descripción
01	Hora del servidor	Muestra la fecha y hora actual del servidor.
02	Hora de Lima	Muestra la hora en la zona horaria America/Lima.
03	Zonas horarias	Selector con más de 60 países/ciudades y su hora local.
04	Conversión de número	Convierte un número (entero o decimal) a string y a entero.
05	Preguntas personalizadas	Saluda al usuario y responde preguntas predefinidas.
06	Buscar palabra	Comprueba si una palabra está dentro de una frase.
07	Eliminar espacios	Toma un texto y elimina todos los espacios en blanco.
08	Concatenar strings	Une dos cadenas de texto.
09	Contar letra	Cuenta cuántas veces aparece una letra en un string.
10	Comparar strings	Compara dos cadenas y dice cuál es mayor/menor/igual.
11	Días entre fechas	Calcula la diferencia en días entre dos fechas.
12	¿Es verano?	Indica si una fecha está dentro del verano (junio-septiembre).
13	Fin de semana	Dice si una fecha es sábado o domingo.
14	Número perfecto	Calcula divisores propios y verifica si es un número perfecto.
15	Sumar días	Añade una cantidad de días a una fecha dada.
16	Números impares	Muestra todos los números impares del 1 al 100.
17	Números pares	Muestra los pares hasta un límite ingresado por el usuario.
18	Triángulo de caracteres	Dibuja triángulos con altura, carácter y orientación configurables.
19	Seleccionar país	Selector de países (Perú, Argentina, etc.) y muestra el nombre.
20	Color y número PRO	Combina un color y un número, cambia el fondo según el color.
21	Calculadora	Suma, resta, multiplica o divide dos números.
22	Ahorcado	Juego completo con más de 100 palabras y dibujo ASCII.
22.5	Loop Processor	Demostración de for, continue y break con interfaz neon.
23	Conversor de temperatura	Convierte entre Celsius, Fahrenheit y Kelvin.
24	Generador de contraseñas	Crea contraseñas seguras con al menos una minúscula, mayúscula, dígito y símbolo.
25	Tabla de multiplicar	Muestra la tabla del 1 al 10 para un número dado.
26	Verificador de palíndromos	Detecta si una palabra o frase es palíndroma (ignorando espacios y signos).
27	Generador de chistes	Devuelve un chiste aleatorio por categoría (programación, animales, comida, oficina).
28	Conversor de monedas	Obtiene tasas de cambio en tiempo real desde una API gratuita.
29	Piedra, papel o tijera	Juego contra la computadora.
30	Acertijo matemático	Operación aleatoria (+, -, ×) que el usuario debe resolver.
31	Frases motivadoras	Muestra una frase inspiradora aleatoria.
32	Números romanos	Convierte de decimal a romano (1-3999).
33	Lanzamiento de dados	Lanza 1 o 2 dados con animación CSS.
34	Calculadora de IMC	Calcula el Índice de Masa Corporal y muestra categoría.
Nota: El ejercicio 22.5 es una variante del 22 (bucles) y el 32 está numerado como 33 en el código, pero en la lista se ha unificado.

🛠️ Requisitos del servidor
Servidor web con soporte CGI (Apache, Nginx con fcgiwrap, XAMPP, etc.)

Python 3.6 o superior

Módulos adicionales (instalar con pip):

pytz (para zonas horarias)

cgitb (viene incluido)

Permisos de ejecución para los archivos .py (en Linux: chmod +x *.py)

Configuración CGI:

En Apache, descomentar o añadir:

apache
ScriptAlias /cgi-bin/ /ruta/a/tu/cgi-bin/
<Directory "/ruta/a/tu/cgi-bin">
    Options +ExecCGI
    AddHandler cgi-script .py
    Require all granted
</Directory>
Colocar todos los .py dentro del directorio cgi-bin.

📥 Instalación y uso
Clona o descarga este repositorio.

Copia todos los archivos .py al directorio cgi-bin de tu servidor web.

Instala dependencias (si usas Linux/macOS):

bash
pip install pytz
Ajusta los permisos (en Linux):

bash
chmod +x /ruta/cgi-bin/*.py
Accede vía navegador a:

text
http://localhost/cgi-bin/index.py
Usa el menú para navegar entre todos los ejercicios.

Si no tienes un servidor local, puedes usar XAMPP (Windows) o MAMP (macOS) y colocar los archivos en la carpeta cgi-bin correspondiente.
