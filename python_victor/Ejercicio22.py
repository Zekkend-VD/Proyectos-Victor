#!/usr/bin/env python3
# -*- coding: utf-8 -*-

# ============================================
# Ejercicio22.py – Juego del Ahorcado (con 100+ palabras y categorías)
# ============================================
import cgi
import cgitb
import random
cgitb.enable()

print("Content-type: text/html; charset=utf-8\n\n")

def dibujar_ahorcado(errores):
    """Devuelve representación ASCII del ahorcado según el número de errores (0-6)."""
    estados = [
        # 0 errores
        """
   +---+
       |
       |
       |
       |
       |
=========
""",
        # 1 error
        """
   +---+
   |   |
       |
       |
       |
       |
=========
""",
        # 2 errores
        """
   +---+
   |   |
   O   |
       |
       |
       |
=========
""",
        # 3 errores
        """
   +---+
   |   |
   O   |
   |   |
       |
       |
=========
""",
        # 4 errores
        """
   +---+
   |   |
   O   |
  /|   |
       |
       |
=========
""",
        # 5 errores
        """
   +---+
   |   |
   O   |
  /|\\  |
       |
       |
=========
""",
        # 6 errores
        """
   +---+
   |   |
   O   |
  /|\\  |
  /    |
       |
=========
""",
        # 7 errores (opcional, pero nosotros tenemos máximo 6)
        """
   +---+
   |   |
   O   |
  /|\\  |
  / \\  |
       |
=========
"""
    ]
    return estados[errores] if errores < len(estados) else estados[-1]

# ------------------------------------------------------------
# Base de datos de palabras organizadas por categorías (100+)
# ------------------------------------------------------------
palabras_por_categoria = {
    "🖥️ Programación": [
        "python", "java", "javascript", "html", "css", "php", "ruby", "swift", "kotlin", "cobol",
        "fortran", "lisp", "perl", "bash", "sql", "rust", "go", "dart", "typescript", "scala"
    ],
    "🐾 Animales": [
        "perro", "gato", "elefante", "jirafa", "leon", "tigre", "cebra", "rinoceronte", "hipopotamo", "cocodrilo",
        "serpiente", "aguila", "delfin", "ballena", "tiburon", "pinguino", "canguro", "oso", "mono", "ardilla"
    ],
    "🍎 Frutas y Verduras": [
        "manzana", "banana", "naranja", "uva", "fresa", "sandia", "melon", "pera", "durazno", "cereza",
        "zanahoria", "brocoli", "espinaca", "tomate", "cebolla", "papa", "lechuga", "pepino", "calabaza", "remolacha"
    ],
    "🌍 Países": [
        "mexico", "españa", "argentina", "colombia", "chile", "peru", "venezuela", "brasil", "uruguay", "paraguay",
        "estadosunidos", "canada", "alemania", "francia", "italia", "reunounido", "japon", "china", "india", "australia"
    ],
    "⚽ Deportes": [
        "futbol", "baloncesto", "tenis", "beisbol", "voleibol", "natacion", "atletismo", "ciclismo", "boxeo", "golf",
        "rugby", "hockey", "esqui", "surf", "escalada", "judo", "karate", "taekwondo", "remo", "equitacion"
    ],
    "🎨 Colores": [
        "rojo", "azul", "verde", "amarillo", "negro", "blanco", "morado", "rosa", "naranja", "marron",
        "gris", "turquesa", "celeste", "violeta", "magenta", "ocre", "beige", "carmesi", "cian", "lima"
    ],
    "👩‍💼 Profesiones": [
        "medico", "ingeniero", "abogado", "maestro", "arquitecto", "programador", "enfermero", "bombero", "policia", "cocinero",
        "piloto", "marinero", "carpintero", "electricista", "plomero", "periodista", "disenador", "fotografo", "musico", "actor"
    ],
    "🎵 Música": [
        "guitarra", "piano", "violin", "bateria", "flauta", "saxofon", "trompeta", "arpa", "acordeon", "cello",
        "opera", "sinfonia", "jazz", "rock", "pop", "reggae", "clasica", "blues", "salsa", "metal"
    ],
    "🏛️ Historia": [
        "egipto", "roma", "grecia", "mayas", "aztecas", "incas", "vikingos", "persas", "mongoles", "otomano",
        "revolucion", "independencia", "guerra", "paz", "imperio", "colonia", "feudalismo", "renacimiento", "ilustracion", "democracia"
    ],
    "🔬 Ciencia": [
        "atomos", "molecula", "gravedad", "energia", "genetica", "evolucion", "celula", "planeta", "galaxia", "telescopio",
        "microscopio", "quimica", "fisica", "biologia", "astronomia", "matematicas", "algoritmo", "robotica", "inteligencia", "clonacion"
    ]
}

# Aplanar la lista para selección aleatoria (opcional, pero podemos elegir categoría primero)
def seleccionar_palabra_aleatoria():
    categoria = random.choice(list(palabras_por_categoria.keys()))
    palabra = random.choice(palabras_por_categoria[categoria])
    return palabra, categoria

# Si no hay parámetros, elegimos una nueva palabra y categoría
form = cgi.FieldStorage()
if "palabra" in form and "categoria" in form:
    palabra_secreta = form.getvalue("palabra")
    categoria = form.getvalue("categoria")
    intentos = int(form.getvalue("intentos_restantes", "6"))
    adivinadas = form.getvalue("adivinadas", "")
    letras_adivinadas = set(adivinadas) if adivinadas else set()
else:
    # Nueva partida
    palabra_secreta, categoria = seleccionar_palabra_aleatoria()
    intentos = 6
    letras_adivinadas = set()

estado = ""

if "letra" in form and form.getvalue("letra"):
    letra = form.getvalue("letra").lower()
    if letra and len(letra) == 1 and letra.isalpha():
        if letra in letras_adivinadas:
            estado = "Ya intentaste esa letra."
        else:
            letras_adivinadas.add(letra)
            if letra not in palabra_secreta:
                intentos -= 1
                estado = "Letra incorrecta."
            else:
                estado = "¡Bien! Letra correcta."
    else:
        estado = "Ingresa una sola letra."

# Calcular errores para el dibujo
letras_incorrectas = [l for l in letras_adivinadas if l not in palabra_secreta]
errores = len(letras_incorrectas)

# Comprobar si ganó o perdió
palabra_mostrada = "".join([c if c in letras_adivinadas else "_" for c in palabra_secreta])
if "_" not in palabra_mostrada:
    resultado_final = "<p style='color:#2dd4bf; font-weight:bold;'>🎉 ¡Ganaste! Has adivinado la palabra.</p>"
elif intentos <= 0:
    resultado_final = f"<p style='color:#f87171; font-weight:bold;'>💀 Perdiste. La palabra era: {palabra_secreta}</p>"
else:
    resultado_final = ""

# Generar el dibujo del ahorcado
ahorcado_dibujo = dibujar_ahorcado(errores)

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejercicio 22 — Ahorcado</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
    <style>
        * {{ margin:0; padding:0; box-sizing:border-box; }}
        body {{ background:#0f172a; color:#f1f5f9; font-family:'Inter',sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }}
        .container {{ max-width:600px; width:100%; background:#1e293b; border-radius:24px; padding:32px; box-shadow:0 20px 40px -10px #00000080; border:1px solid #334155; }}
        h1 {{ font-size:2rem; font-weight:600; background:linear-gradient(135deg,#2dd4bf,#f472b6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:24px; }}
        .game-info {{ background:#0f172a; border-radius:16px; padding:20px; margin:16px 0; border-left:6px solid #f472b6; }}
        .hangman {{ font-family:monospace; font-size:1rem; line-height:1.2; color:#94a3b8; margin:10px 0; text-align:center; }}
        .categoria {{ display:inline-block; background:#2dd4bf20; color:#2dd4bf; padding:4px 12px; border-radius:40px; font-size:0.9rem; margin-bottom:10px; border:1px solid #2dd4bf60; }}
        .word {{ font-family:monospace; font-size:2rem; letter-spacing:8px; text-align:center; margin:20px 0; }}
        .attempts {{ color:#94a3b8; margin-bottom:10px; }}
        form {{ background:#0f172a; border-radius:16px; padding:20px; margin:16px 0; }}
        label {{ display:block; margin-bottom:8px; color:#94a3b8; }}
        input[type="text"] {{ background:#1e293b; color:#f1f5f9; border:2px solid #334155; padding:12px 16px; border-radius:12px; font-size:1rem; width:100%; margin-bottom:16px; transition:border 0.2s; }}
        input[type="text"]:focus {{ border-color:#2dd4bf; outline:none; }}
        input[type="submit"] {{ background:linear-gradient(135deg,#2dd4bf,#14b8a6); color:#0f172a; border:none; padding:12px 24px; border-radius:40px; font-weight:600; cursor:pointer; width:100%; }}
        input[type="submit"]:hover {{ background:linear-gradient(135deg,#f472b6,#ec4899); color:white; transform:translateY(-2px); }}
        .result-card {{ background:#0f172a; border-radius:16px; padding:20px; margin:16px 0; border-left:6px solid #2dd4bf; }}
        .actions {{ display:flex; gap:12px; margin-top:24px; }}
        .back-link, .new-game {{ display:inline-flex; align-items:center; gap:8px; background:#334155; color:#f1f5f9; text-decoration:none; padding:12px 24px; border-radius:40px; font-weight:500; transition:0.2s; border:1px solid #475569; }}
        .back-link:hover, .new-game:hover {{ background:#2dd4bf; color:#0f172a; border-color:#2dd4bf; transform:translateY(-2px); }}
        .back-link:hover .arrow {{ transform:translateX(-4px); }}
        .new-game:hover .refresh {{ transform:rotate(180deg); }}
        .arrow {{ font-size:1.4rem; transition:transform 0.2s; }}
        .refresh {{ font-size:1.4rem; transition:transform 0.4s; }}
    </style>
</head>
<body>
    <div class="container">
        <h1>🎮 Ejercicio 22 — Ahorcado</h1>
        <div class="game-info">
            <div class="categoria">{categoria}</div>
            <div class="hangman"><pre>{ahorcado_dibujo}</pre></div>
            <div class="word">{palabra_mostrada}</div>
            <div class="attempts">Intentos restantes: {intentos}</div>
            <div><strong>Letras usadas:</strong> {''.join(sorted(letras_adivinadas)) or 'ninguna'}</div>
            {f"<p style='color:#facc15;'>{estado}</p>" if estado else ""}
            {resultado_final}
        </div>
        {'' if resultado_final else f'''
        <form method="post">
            <input type="hidden" name="palabra" value="{palabra_secreta}">
            <input type="hidden" name="categoria" value="{categoria}">
            <input type="hidden" name="intentos_restantes" value="{intentos}">
            <input type="hidden" name="adivinadas" value="{''.join(letras_adivinadas)}">
            <label for="letra">Ingresa una letra:</label>
            <input type="text" name="letra" id="letra" maxlength="1" pattern="[a-zA-Z]" required>
            <input type="submit" value="Probar letra">
        </form>
        '''}
        <div class="actions">
            <a href="index.py" class="back-link"><span class="arrow">←</span> Volver al inicio</a>
            <a href="Ejercicio22.py" class="new-game"><span class="refresh">⟳</span> Nueva Partida</a>
        </div>
    </div>
</body>
</html>""")