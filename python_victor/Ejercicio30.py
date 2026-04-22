#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import cgi
import cgitb
import random
cgitb.enable()

print("Content-type: text/html; charset=utf-8\n\n")

form = cgi.FieldStorage()
resultado = ""
operacion_actual = ""
respuesta_correcta = None

# Generar nueva operación si no hay datos o se pide nueva
if "nuevo" in form or "respuesta" not in form:
    # Crear nueva operación
    ops = ["+", "-", "*"]
    op = random.choice(ops)
    if op == "+":
        a = random.randint(1, 20)
        b = random.randint(1, 20)
        res = a + b
        texto_op = f"{a} + {b} = ?"
    elif op == "-":
        a = random.randint(10, 30)
        b = random.randint(1, a)  # para que no dé negativo
        res = a - b
        texto_op = f"{a} - {b} = ?"
    else:  # multiplicación
        a = random.randint(2, 10)
        b = random.randint(2, 10)
        res = a * b
        texto_op = f"{a} × {b} = ?"
    operacion_actual = texto_op
    respuesta_correcta = res
else:
    # Venimos de un envío con respuesta
    if "op" in form and "res_correcta" in form:
        operacion_actual = form.getvalue("op")
        respuesta_correcta = int(form.getvalue("res_correcta"))
        if "respuesta" in form:
            try:
                resp_usuario = int(form.getvalue("respuesta"))
                if resp_usuario == respuesta_correcta:
                    resultado = "<div class='result-card'><p style='color:#2dd4bf;'>✅ ¡Correcto! Muy bien.</p></div>"
                else:
                    resultado = f"<div class='result-card error'><p style='color:#f87171;'>❌ Incorrecto. La respuesta era {respuesta_correcta}.</p></div>"
            except ValueError:
                resultado = "<div class='result-card error'>Ingresa un número válido</div>"
    else:
        # Por si acaso, generar nueva
        ops = ["+", "-", "*"]
        op = random.choice(ops)
        if op == "+":
            a = random.randint(1, 20)
            b = random.randint(1, 20)
            res = a + b
            texto_op = f"{a} + {b} = ?"
        elif op == "-":
            a = random.randint(10, 30)
            b = random.randint(1, a)
            res = a - b
            texto_op = f"{a} - {b} = ?"
        else:
            a = random.randint(2, 10)
            b = random.randint(2, 10)
            res = a * b
            texto_op = f"{a} × {b} = ?"
        operacion_actual = texto_op
        respuesta_correcta = res

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejercicio 30 — Acertijo matemático</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
    <style>
        * {{ margin:0; padding:0; box-sizing:border-box; }}
        body {{ background:#0f172a; color:#f1f5f9; font-family:'Inter',sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }}
        .container {{ max-width:600px; width:100%; background:#1e293b; border-radius:24px; padding:32px; box-shadow:0 20px 40px -10px #00000080; border:1px solid #334155; }}
        h1 {{ font-size:2rem; font-weight:600; background:linear-gradient(135deg,#2dd4bf,#f472b6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:24px; }}
        .operacion {{ background:#0f172a; border-radius:16px; padding:30px; margin:16px 0; border-left:6px solid #f472b6; font-size:2rem; text-align:center; }}
        form {{ background:#0f172a; border-radius:16px; padding:20px; margin:16px 0; }}
        label {{ display:block; margin-bottom:8px; color:#94a3b8; }}
        input[type="number"] {{ background:#1e293b; color:#f1f5f9; border:2px solid #334155; padding:12px 16px; border-radius:12px; font-size:1rem; width:100%; margin-bottom:16px; transition:border 0.2s; }}
        input[type="number"]:focus {{ border-color:#2dd4bf; outline:none; }}
        .button-group {{ display:flex; gap:10px; }}
        input[type="submit"], .nuevo-btn {{ background:linear-gradient(135deg,#2dd4bf,#14b8a6); color:#0f172a; border:none; padding:12px 24px; border-radius:40px; font-weight:600; cursor:pointer; text-align:center; text-decoration:none; display:inline-block; }}
        input[type="submit"]:hover, .nuevo-btn:hover {{ background:linear-gradient(135deg,#f472b6,#ec4899); color:white; transform:translateY(-2px); }}
        .nuevo-btn {{ background:#334155; color:#f1f5f9; }}
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
        <h1>🧮 Ejercicio 30 — Acertijo matemático</h1>
        <div class="operacion">
            {operacion_actual}
        </div>
        <form method="post">
            <input type="hidden" name="op" value="{operacion_actual}">
            <input type="hidden" name="res_correcta" value="{respuesta_correcta}">
            <label for="respuesta">Tu respuesta:</label>
            <input type="number" name="respuesta" id="respuesta" required>
            <div class="button-group">
                <input type="submit" value="Comprobar">
                <a href="?nuevo=1" class="nuevo-btn">🔄 Nueva operación</a>
            </div>
        </form>
        {resultado}
        <a href="index.py" class="back-link"><span class="arrow">←</span> Volver al inicio</a>
    </div>
</body>
</html>""")
