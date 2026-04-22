#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import cgi
import cgitb
import random
cgitb.enable()

print("Content-type: text/html; charset=utf-8\n\n")

form = cgi.FieldStorage()
resultado = ""

opciones = ["piedra", "papel", "tijera"]
eleccion_usuario = ""

if "jugada" in form:
    usuario = form.getvalue("jugada")
    if usuario in opciones:
        computadora = random.choice(opciones)
        eleccion_usuario = usuario
        # Determinar ganador
        if usuario == computadora:
            mensaje = f"🤝 Empate! Ambos eligieron {usuario}."
        elif (usuario == "piedra" and computadora == "tijera") or \
             (usuario == "papel" and computadora == "piedra") or \
             (usuario == "tijera" and computadora == "papel"):
            mensaje = f"🎉 ¡Ganaste! {usuario} vence a {computadora}."
        else:
            mensaje = f"💻 Perdiste. {computadora} vence a {usuario}."
        resultado = f"<div class='result-card'><p>{mensaje}</p></div>"
    else:
        resultado = "<div class='result-card error'>Jugada no válida</div>"

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejercicio 29 — Piedra, papel o tijera</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
    <style>
        * {{ margin:0; padding:0; box-sizing:border-box; }}
        body {{ background:#0f172a; color:#f1f5f9; font-family:'Inter',sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }}
        .container {{ max-width:600px; width:100%; background:#1e293b; border-radius:24px; padding:32px; box-shadow:0 20px 40px -10px #00000080; border:1px solid #334155; }}
        h1 {{ font-size:2rem; font-weight:600; background:linear-gradient(135deg,#2dd4bf,#f472b6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:24px; }}
        .botones {{ display:flex; gap:10px; justify-content:center; margin:20px 0; }}
        .boton-jugada {{ background:#1e293b; color:#f1f5f9; border:2px solid #334155; padding:12px 24px; border-radius:40px; font-size:1.2rem; cursor:pointer; transition:0.2s; text-decoration:none; display:inline-block; }}
        .boton-jugada:hover {{ background:#2dd4bf; color:#0f172a; border-color:#2dd4bf; transform:translateY(-2px); }}
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
        <h1>✊📄✂️ Ejercicio 29 — Piedra, papel o tijera</h1>
        <div class="botones">
            <a href="?jugada=piedra" class="boton-jugada">🪨 Piedra</a>
            <a href="?jugada=papel" class="boton-jugada">📄 Papel</a>
            <a href="?jugada=tijera" class="boton-jugada">✂️ Tijera</a>
        </div>
        {resultado}
        <a href="index.py" class="back-link"><span class="arrow">←</span> Volver al inicio</a>
    </div>
</body>
</html>""")