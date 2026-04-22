#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import cgi
import cgitb
import random
cgitb.enable()

print("Content-type: text/html; charset=utf-8\n\n")

form = cgi.FieldStorage()
resultado = ""

# Diccionario de chistes por categoría
chistes = {
    "programacion": [
        "¿Por qué los programadores confunden Halloween con Navidad? Porque Oct 31 == Dec 25.",
        "Hay 10 tipos de personas: los que entienden binario y los que no.",
        "¿Qué le dice un bit a otro? 'Nos vemos en el bus'.",
        "Un programador se queda ciego después de leer su propio código sin comentarios.",
        "¿Cómo sale de su casa el programador? ¡Por el firefox!"
    ],
    "animales": [
        "¿Qué le dice un pez a otro? Nada.",
        "¿Por qué los pájaros no usan Facebook? Porque ya tienen Twitter.",
        "¿Cómo se despiden los elefantes? 'Me voy, ¡no me trompes!'",
        "¿Qué hace una abeja en el gimnasio? ¡Zum-ba!",
        "Un perro le dice a otro: 'Mi dueño me da de comer, me saca a pasear, me baña...' El otro responde: '¡Qué suerte! El mío también, pero cree que es mi dueño'."
    ],
    "comida": [
        "¿Qué hace una uva en una fiesta? ¡Vino!",
        "¿Por qué el tomate no cuenta chistes? Porque se parte de risa.",
        "¿Cómo se llama el campeón de buceo japonés? Tokofondo.",
        "¿Qué le dijo la leche al yogur? '¡Nos vemos en el supermercado!'",
        "¿Qué hace una cuchara en la carretera? '¡A la sopa, a la sopa!'"
    ],
    "oficina": [
        "¿Por qué el jefe no juega al escondite? Porque no lo buscarían.",
        "¿Qué hace un informático en la playa? Navegar.",
        "¿Cómo se queda un empleado después de trabajar 8 horas? Hecho polvo.",
        "¿Qué es un café en la oficina? Un motivo para levantarse.",
        "¿Por qué los lunes son cortos? Porque empiezan tarde."
    ]
}

categorias = list(chistes.keys())

if "categoria" in form:
    cat = form.getvalue("categoria")
    if cat in chistes:
        chiste = random.choice(chistes[cat])
        resultado = f"<div class='result-card'><p style='font-size:1.2rem;'>😄 {chiste}</p></div>"
    else:
        resultado = "<div class='result-card error'>Categoría no válida</div>"

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejercicio 27 — Generador de chistes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
    <style>
        * {{ margin:0; padding:0; box-sizing:border-box; }}
        body {{ background:#0f172a; color:#f1f5f9; font-family:'Inter',sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }}
        .container {{ max-width:600px; width:100%; background:#1e293b; border-radius:24px; padding:32px; box-shadow:0 20px 40px -10px #00000080; border:1px solid #334155; }}
        h1 {{ font-size:2rem; font-weight:600; background:linear-gradient(135deg,#2dd4bf,#f472b6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:24px; }}
        form {{ background:#0f172a; border-radius:16px; padding:20px; margin:16px 0; }}
        label {{ display:block; margin-bottom:8px; color:#94a3b8; }}
        select {{ background:#1e293b; color:#f1f5f9; border:2px solid #334155; padding:12px 16px; border-radius:12px; font-size:1rem; width:100%; margin-bottom:16px; transition:border 0.2s; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23f1f5f9' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 16px center; }}
        select:focus {{ border-color:#2dd4bf; outline:none; }}
        input[type="submit"] {{ background:linear-gradient(135deg,#2dd4bf,#14b8a6); color:#0f172a; border:none; padding:12px 24px; border-radius:40px; font-weight:600; cursor:pointer; width:100%; }}
        input[type="submit"]:hover {{ background:linear-gradient(135deg,#f472b6,#ec4899); color:white; transform:translateY(-2px); }}
        .result-card {{ background:#0f172a; border-radius:16px; padding:20px; margin:16px 0; border-left:6px solid #2dd4bf; }}
        .result-card.error {{ border-left-color:#f87171; }}
        .back-link {{ display:inline-flex; align-items:center; gap:8px; background:#334155; color:#f1f5f9; text-decoration:none; padding:12px 24px; border-radius:40px; font-weight:500; transition:0.2s; border:1px solid #475569; margin-top:24px; }}
        .back-link:hover {{ background:#2dd4bf; color:#0f172a; border-color:#2dd4bf; transform:translateY(-2px); }}
        .back-link:hover .arrow {{ transform:translateX(-4px); }}
        .arrow {{ font-size:1.4rem; transition:transform 0.2s; }}
    </style>
</head>
<body>
    <div class="container">
        <h1>😆 Ejercicio 27 — Generador de chistes</h1>
        <form method="post">
            <label for="categoria">Elige una categoría:</label>
            <select name="categoria" id="categoria">
                <option value="programacion">Programación</option>
                <option value="animales">Animales</option>
                <option value="comida">Comida</option>
                <option value="oficina">Oficina</option>
            </select>
            <input type="submit" value="¡Dame un chiste!">
        </form>
        {resultado}
        <a href="index.py" class="back-link"><span class="arrow">←</span> Volver al inicio</a>
    </div>
</body>
</html>""")
