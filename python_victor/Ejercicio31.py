#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import cgi
import cgitb
import random
cgitb.enable()

print("Content-type: text/html; charset=utf-8\n\n")

form = cgi.FieldStorage()
frase = ""

frases = [
    "El éxito es la suma de pequeños esfuerzos repetidos día tras día.",
    "No importa lo lento que vayas, mientras no te detengas.",
    "Cree en ti mismo y todo será posible.",
    "El único modo de hacer un gran trabajo es amar lo que haces.",
    "Los sueños no se hacen realidad por arte de magia, se hacen con esfuerzo y dedicación.",
    "Hoy es un buen día para tener un gran día.",
    "La actitud es una pequeña cosa que hace una gran diferencia.",
    "Cada día es una nueva oportunidad para cambiar tu vida.",
    "El fracaso es la oportunidad de empezar de nuevo con más inteligencia.",
    "No te rindas, los principios son siempre los más difíciles.",
    "El secreto para avanzar es comenzar.",
    "La disciplina es el puente entre las metas y los logros.",
    "Hazlo con pasión o no lo hagas.",
    "Tu única competencia eres tú mismo.",
    "Las pequeñas acciones diarias construyen grandes resultados."
]

if "generar" in form or not form:
    frase = random.choice(frases)

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejercicio 31 — Frases motivadoras</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
    <style>
        * {{ margin:0; padding:0; box-sizing:border-box; }}
        body {{ background:#0f172a; color:#f1f5f9; font-family:'Inter',sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }}
        .container {{ max-width:600px; width:100%; background:#1e293b; border-radius:24px; padding:32px; box-shadow:0 20px 40px -10px #00000080; border:1px solid #334155; }}
        h1 {{ font-size:2rem; font-weight:600; background:linear-gradient(135deg,#2dd4bf,#f472b6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:24px; }}
        .frase-card {{ background:#0f172a; border-radius:16px; padding:30px; margin:16px 0; border-left:6px solid #f472b6; font-size:1.4rem; text-align:center; box-shadow:0 0 20px rgba(45,212,191,0.3); }}
        .frase-card p {{ font-style:italic; }}
        form {{ text-align:center; margin:20px 0; }}
        input[type="submit"] {{ background:linear-gradient(135deg,#2dd4bf,#14b8a6); color:#0f172a; border:none; padding:12px 32px; border-radius:40px; font-weight:600; cursor:pointer; font-size:1.1rem; transition:0.2s; }}
        input[type="submit"]:hover {{ background:linear-gradient(135deg,#f472b6,#ec4899); color:white; transform:translateY(-2px); box-shadow:0 8px 16px -4px rgba(244,114,182,0.4); }}
        .back-link {{ display:inline-flex; align-items:center; gap:8px; background:#334155; color:#f1f5f9; text-decoration:none; padding:12px 24px; border-radius:40px; font-weight:500; transition:0.2s; border:1px solid #475569; margin-top:24px; }}
        .back-link:hover {{ background:#2dd4bf; color:#0f172a; border-color:#2dd4bf; transform:translateY(-2px); }}
        .back-link:hover .arrow {{ transform:translateX(-4px); }}
        .arrow {{ font-size:1.4rem; transition:transform 0.2s; }}
    </style>
</head>
<body>
    <div class="container">
        <h1>💪 Ejercicio 31 — Frases motivadoras</h1>
        <div class="frase-card">
            <p>“{frase}”</p>
        </div>
        <form method="post">
            <input type="hidden" name="generar" value="1">
            <input type="submit" value="✨ Nueva frase ✨">
        </form>
        <a href="index.py" class="back-link"><span class="arrow">←</span> Volver al inicio</a>
    </div>
</body>
</html>""")
