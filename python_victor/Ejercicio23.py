#!/usr/bin/env python3
# -*- coding: utf-8 -*-

# ============================================
# Ejercicio23.py – Conversor de temperatura
# ============================================
import cgi
import cgitb
cgitb.enable()

print("Content-type: text/html; charset=utf-8\n\n")

form = cgi.FieldStorage()
resultado = ""

if "valor" in form and "de" in form and "a" in form:
    valor = form.getvalue("valor")
    de = form.getvalue("de")
    a = form.getvalue("a")
    if valor and de and a:
        try:
            temp = float(valor)
            if de == "c" and a == "f":
                res = (temp * 9/5) + 32
                res_str = f"{temp} °C = {res:.2f} °F"
            elif de == "c" and a == "k":
                res = temp + 273.15
                res_str = f"{temp} °C = {res:.2f} K"
            elif de == "f" and a == "c":
                res = (temp - 32) * 5/9
                res_str = f"{temp} °F = {res:.2f} °C"
            elif de == "f" and a == "k":
                res = (temp - 32) * 5/9 + 273.15
                res_str = f"{temp} °F = {res:.2f} K"
            elif de == "k" and a == "c":
                res = temp - 273.15
                res_str = f"{temp} K = {res:.2f} °C"
            elif de == "k" and a == "f":
                res = (temp - 273.15) * 9/5 + 32
                res_str = f"{temp} K = {res:.2f} °F"
            else:
                res_str = "Conversión no válida"
            resultado = f"<div class='result-card'><p><strong>{res_str}</strong></p></div>"
        except ValueError:
            resultado = "<div class='result-card error'>Ingresa un número válido</div>"
    else:
        resultado = "<div class='result-card error'>Completa todos los campos</div>"

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejercicio 23 — Conversor de temperatura</title>
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
        .grupo-selects {{ display:flex; gap:10px; }}
        .grupo-selects select {{ flex:1; }}
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
        <h1>🌡️ Ejercicio 23 — Conversor de temperatura</h1>
        <form method="post">
            <label for="valor">Valor:</label>
            <input type="number" step="any" name="valor" id="valor" required>
            <div class="grupo-selects">
                <select name="de">
                    <option value="c">Celsius (°C)</option>
                    <option value="f">Fahrenheit (°F)</option>
                    <option value="k">Kelvin (K)</option>
                </select>
                <span style="align-self:center;">→</span>
                <select name="a">
                    <option value="c">Celsius (°C)</option>
                    <option value="f">Fahrenheit (°F)</option>
                    <option value="k">Kelvin (K)</option>
                </select>
            </div>
            <input type="submit" value="Convertir">
        </form>
        {resultado}
        <a href="index.py" class="back-link"><span class="arrow">←</span> Volver al inicio</a>
    </div>
</body>
</html>""")