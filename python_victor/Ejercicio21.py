#!/usr/bin/env python3
# -*- coding: utf-8 -*-

# ============================================
# Ejercicio21.py – Calculadora básica
# ============================================
import cgi
import cgitb
cgitb.enable()

print("Content-type: text/html; charset=utf-8\n\n")

form = cgi.FieldStorage()
resultado = ""

if all(k in form for k in ("num1", "num2", "operacion")):
    num1 = form.getvalue("num1")
    num2 = form.getvalue("num2")
    op = form.getvalue("operacion")
    if num1 and num2:
        try:
            n1 = float(num1)
            n2 = float(num2)
            if op == "sumar":
                res = n1 + n2
                simbolo = "+"
            elif op == "restar":
                res = n1 - n2
                simbolo = "-"
            elif op == "multiplicar":
                res = n1 * n2
                simbolo = "×"
            elif op == "dividir":
                if n2 == 0:
                    res = "Error: división por cero"
                else:
                    res = n1 / n2
                simbolo = "÷"
            else:
                res = "Operación no válida"
                simbolo = "?"
            
            if isinstance(res, float):
                # Mostrar sin decimales si es entero
                if res.is_integer():
                    res = int(res)
                resultado = f"<div class='result-card'><p>{n1} {simbolo} {n2} = <strong>{res}</strong></p></div>"
            else:
                resultado = f"<div class='result-card error'><p>{res}</p></div>"
        except ValueError:
            resultado = "<div class='result-card error'>Ingresa números válidos</div>"
    else:
        resultado = "<div class='result-card error'>Completa todos los campos</div>"

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejercicio 21 — Calculadora</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
    <style>
        * {{ margin:0; padding:0; box-sizing:border-box; }}
        body {{ background:#0f172a; color:#f1f5f9; font-family:'Inter',sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }}
        .container {{ max-width:600px; width:100%; background:#1e293b; border-radius:24px; padding:32px; box-shadow:0 20px 40px -10px #00000080; border:1px solid #334155; }}
        h1 {{ font-size:2rem; font-weight:600; background:linear-gradient(135deg,#2dd4bf,#f472b6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:24px; }}
        form {{ background:#0f172a; border-radius:16px; padding:20px; margin:16px 0; }}
        label {{ display:block; margin-bottom:8px; color:#94a3b8; }}
        input, select {{ background:#1e293b; color:#f1f5f9; border:2px solid #334155; padding:12px 16px; border-radius:12px; font-size:1rem; width:100%; margin-bottom:16px; transition:border 0.2s; }}
        input:focus, select:focus {{ border-color:#2dd4bf; outline:none; }}
        input[type="submit"] {{ background:linear-gradient(135deg,#2dd4bf,#14b8a6); color:#0f172a; font-weight:600; border:none; cursor:pointer; }}
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
        <h1>🧮 Ejercicio 21 — Calculadora</h1>
        <form method="post">
            <label for="num1">Número 1:</label>
            <input type="number" step="any" name="num1" id="num1" required>
            <label for="num2">Número 2:</label>
            <input type="number" step="any" name="num2" id="num2" required>
            <label for="operacion">Operación:</label>
            <select name="operacion" id="operacion">
                <option value="sumar">Sumar</option>
                <option value="restar">Restar</option>
                <option value="multiplicar">Multiplicar</option>
                <option value="dividir">Dividir</option>
            </select>
            <input type="submit" value="Calcular">
        </form>
        {resultado}
        <a href="index.py" class="back-link"><span class="arrow">←</span> Volver al inicio</a>
    </div>
</body>
</html>""")