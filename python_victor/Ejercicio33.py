#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import cgi
import cgitb
import random
cgitb.enable()

print("Content-type: text/html; charset=utf-8\n")

form = cgi.FieldStorage()
resultado = ""
clase_animacion = ""

if "lanzar" in form:
    clase_animacion = "shake"  # Activa la animación en el contenedor
    dados = form.getvalue("dados", "1")
    if dados == "1":
        resultado_dado = random.randint(1, 6)
        resultado = f"<div class='dado-resultado'><span class='emoji-dado animar-dado'>🎲</span> <strong class='numero-dado'>{resultado_dado}</strong></div>"
    else:
        d1 = random.randint(1, 6)
        d2 = random.randint(1, 6)
        suma = d1 + d2
        resultado = f"<div class='dado-resultado'><span class='emoji-dado animar-dado'>🎲</span> {d1} + <span class='emoji-dado animar-dado'>🎲</span> {d2} = <strong>{suma}</strong></div>"
else:
    resultado = "<div class='mensaje-inicial'>Presiona un botón para lanzar los dados.</div>"

html = f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejercicio 34 — Lanzamiento de dados</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
    <style>
        * {{ margin:0; padding:0; box-sizing:border-box; }}
        body {{ background:#0f172a; color:#f1f5f9; font-family:'Inter',sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }}
        .container {{ max-width:600px; width:100%; background:#1e293b; border-radius:24px; padding:32px; box-shadow:0 20px 40px -10px #00000080; border:1px solid #334155; }}
        h1 {{ font-size:2rem; font-weight:600; background:linear-gradient(135deg,#2dd4bf,#f472b6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:24px; }}
        .botones-dados {{ display:flex; gap:20px; justify-content:center; margin:30px 0; }}
        .btn-dado {{
            background:linear-gradient(135deg,#2dd4bf,#14b8a6);
            color:#0f172a;
            border:none;
            padding:15px 30px;
            border-radius:60px;
            font-weight:600;
            font-size:1.2rem;
            cursor:pointer;
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            gap:10px;
            transition:0.2s;
            box-shadow:0 4px 10px rgba(0,0,0,0.3);
        }}
        .btn-dado:hover {{
            background:linear-gradient(135deg,#f472b6,#ec4899);
            color:white;
            transform:translateY(-3px);
            box-shadow:0 8px 20px -5px rgba(244,114,182,0.5);
        }}
        .resultado-caja {{
            background:#0f172a;
            border-radius:16px;
            padding:30px;
            margin:20px 0;
            border-left:6px solid #f472b6;
            text-align:center;
            font-size:2rem;
            animation:fadeIn 0.5s ease;
        }}
        .shake .resultado-caja {{
            animation:shake 0.5s ease, fadeIn 0.5s ease;
        }}
        .dado-resultado {{
            display:flex;
            align-items:center;
            justify-content:center;
            gap:15px;
            flex-wrap:wrap;
        }}
        .emoji-dado {{
            font-size:3rem;
            filter:drop-shadow(0 0 10px #2dd4bf);
            display:inline-block;
        }}
        .animar-dado {{
            animation:roll 0.6s ease-out;
        }}
        .numero-dado {{
            font-size:2.5rem;
            color:#f472b6;
        }}
        .mensaje-inicial {{
            color:#94a3b8;
            font-size:1.2rem;
        }}
        @keyframes shake {{
            0% {{ transform:translateX(0); }}
            20% {{ transform:translateX(-10px); }}
            40% {{ transform:translateX(10px); }}
            60% {{ transform:translateX(-5px); }}
            80% {{ transform:translateX(5px); }}
            100% {{ transform:translateX(0); }}
        }}
        @keyframes roll {{
            0% {{ transform:rotate(0deg) scale(1); }}
            50% {{ transform:rotate(180deg) scale(1.2); }}
            100% {{ transform:rotate(360deg) scale(1); }}
        }}
        @keyframes fadeIn {{
            from {{ opacity:0; transform:scale(0.9); }}
            to {{ opacity:1; transform:scale(1); }}
        }}
        .back-link {{
            display:inline-flex;
            align-items:center;
            gap:8px;
            background:#334155;
            color:#f1f5f9;
            text-decoration:none;
            padding:12px 24px;
            border-radius:40px;
            font-weight:500;
            transition:0.2s;
            border:1px solid #475569;
            margin-top:24px;
        }}
        .back-link:hover {{
            background:#2dd4bf;
            color:#0f172a;
            border-color:#2dd4bf;
            transform:translateY(-2px);
        }}
        .back-link:hover .arrow {{ transform:translateX(-4px); }}
        .arrow {{ font-size:1.4rem; transition:transform 0.2s; }}
    </style>
</head>
<body>
    <div class="container">
        <h1>🎲 Ejercicio 34 — Lanzamiento de dados</h1>
        <div class="botones-dados">
            <a href="?lanzar=1&dados=1" class="btn-dado"><span class="emoji-dado">🎲</span> Un dado</a>
            <a href="?lanzar=1&dados=2" class="btn-dado"><span class="emoji-dado">🎲🎲</span> Dos dados</a>
        </div>
        <div class="resultado-caja {clase_animacion}">
            {resultado}
        </div>
        <a href="index.py" class="back-link"><span class="arrow">←</span> Volver al inicio</a>
    </div>
</body>
</html>"""

print(html)