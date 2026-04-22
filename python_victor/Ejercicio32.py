#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import cgi
import cgitb
cgitb.enable()

print("Content-type: text/html; charset=utf-8\n")

form = cgi.FieldStorage()
resultado = ""

def decimal_a_romano(num):
    valores = [1000, 900, 500, 400, 100, 90, 50, 40, 10, 9, 5, 4, 1]
    simbolos = ["M", "CM", "D", "CD", "C", "XC", "L", "XL", "X", "IX", "V", "IV", "I"]
    romano = ''
    i = 0
    while num > 0:
        for _ in range(num // valores[i]):
            romano += simbolos[i]
            num -= valores[i]
        i += 1
    return romano

def romano_a_decimal(rom):
    valores = {'I':1, 'V':5, 'X':10, 'L':50, 'C':100, 'D':500, 'M':1000}
    total = 0
    prev = 0
    for letra in reversed(rom):
        actual = valores[letra]
        if actual < prev:
            total -= actual
        else:
            total += actual
        prev = actual
    return total

if "numero" in form or "romano" in form:
    if "numero" in form and form["numero"].value:
        try:
            num = int(form.getvalue("numero"))
            if 1 <= num <= 3999:
                resultado = f"<div class='result-card'><p>{num} en romano es <strong>{decimal_a_romano(num)}</strong></p></div>"
            else:
                resultado = "<div class='result-card error'>Número debe estar entre 1 y 3999</div>"
        except ValueError:
            resultado = "<div class='result-card error'>Ingresa un número válido</div>"
    elif "romano" in form and form["romano"].value:
        rom = form.getvalue("romano").upper()
        try:
            dec = romano_a_decimal(rom)
            resultado = f"<div class='result-card'><p>{rom} en decimal es <strong>{dec}</strong></p></div>"
        except:
            resultado = "<div class='result-card error'>Número romano no válido</div>"

# Usamos f-string con dobles llaves para escapar el CSS
html = f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejercicio 33 — Conversor de romanos</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
    <style>
        * {{ margin:0; padding:0; box-sizing:border-box; }}
        body {{ background:#0f172a; color:#f1f5f9; font-family:'Inter',sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }}
        .container {{ max-width:600px; width:100%; background:#1e293b; border-radius:24px; padding:32px; box-shadow:0 20px 40px -10px #00000080; border:1px solid #334155; }}
        h1 {{ font-size:2rem; font-weight:600; background:linear-gradient(135deg,#2dd4bf,#f472b6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:24px; }}
        form {{ background:#0f172a; border-radius:16px; padding:20px; margin:16px 0; }}
        label {{ display:block; margin-bottom:8px; color:#94a3b8; }}
        input {{ background:#1e293b; color:#f1f5f9; border:2px solid #334155; padding:12px 16px; border-radius:12px; font-size:1rem; width:100%; margin-bottom:16px; transition:border 0.2s; }}
        input:focus {{ border-color:#2dd4bf; outline:none; }}
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
        <h1>🔢 Ejercicio 33 — Conversor de números romanos</h1>
        <form method="post">
            <label for="numero">Decimal a romano (1-3999):</label>
            <input type="number" name="numero" id="numero" min="1" max="3999" step="1">
            <input type="submit" value="Convertir a romano">
        </form>
        {resultado}
        <a href="index.py" class="back-link"><span class="arrow">←</span> Volver al inicio</a>
    </div>
</body>
</html>"""

print(html)