#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import cgi
import cgitb
import urllib.request
import json
cgitb.enable()

print("Content-type: text/html; charset=utf-8\n")

form = cgi.FieldStorage()
resultado = ""
error_conexion = ""
monedas_destacadas = ["USD", "EUR", "GBP", "JPY", "CHF", "CAD", "AUD", "CNY", "MXN", "BRL", "PEN", "COP", "VES", "ARS", "CLP", "UYU"]

# Tasas de respaldo por si falla la API (actualizadas aproximadamente)
TASAS_RESGUARDO = {
    "USD": 1.0, "EUR": 0.92, "GBP": 0.78, "JPY": 148.50, "CHF": 0.89,
    "CAD": 1.36, "AUD": 1.52, "CNY": 7.24, "MXN": 17.58, "BRL": 5.12,
    "PEN": 3.71, "COP": 3850.00, "VES": 36.50, "ARS": 870.00, "CLP": 960.00,
    "UYU": 38.50, "BOB": 6.91, "PYG": 7300.00, "CRC": 520.00, "DOP": 58.50,
    "GTQ": 7.75, "HNL": 24.70, "NIO": 36.50, "PAB": 1.00, "SVC": 8.75
}

def obtener_tasas():
    """Obtiene tasas actualizadas desde la API gratuita"""
    try:
        url = "https://api.exchangerate.host/latest?base=USD"
        with urllib.request.urlopen(url, timeout=5) as response:
            data = json.loads(response.read().decode())
            if data.get("success") or data.get("rates"):
                return data["rates"]
    except:
        pass
    return None  # Si falla, se usarán las tasas fijas

# Obtener tasas actualizadas o usar respaldo
tasas_api = obtener_tasas()
if not tasas_api:
    tasas_api = TASAS_RESGUARDO
    error_conexion = "<p class='warning'>⚠️ Usando tasas fijas (sin conexión a internet)</p>"
else:
    # Aseguramos que las monedas destacadas estén presentes (por si acaso)
    for moneda in monedas_destacadas:
        if moneda not in tasas_api:
            tasas_api[moneda] = TASAS_RESGUARDO.get(moneda, 1.0)

if "monto" in form and "de" in form and "a" in form:
    monto_str = form.getvalue("monto")
    de = form.getvalue("de").upper()
    a = form.getvalue("a").upper()
    
    if monto_str and de and a:
        try:
            monto = float(monto_str)
            # Convertir a USD como base intermedia
            if de in tasas_api and a in tasas_api:
                en_usd = monto / tasas_api[de]
                convertido = en_usd * tasas_api[a]
                resultado = f"""
                <div class='result-card'>
                    <p class='resultado-principal'>{monto:,.2f} {de} = <strong>{convertido:,.2f} {a}</strong></p>
                    <p class='tasa-info'>1 {de} = {(tasas_api[a]/tasas_api[de]):.4f} {a}</p>
                </div>
                """
            else:
                resultado = "<div class='result-card error'>Moneda no disponible</div>"
        except ValueError:
            resultado = "<div class='result-card error'>Ingresa un monto válido</div>"
        except Exception as e:
            resultado = f"<div class='result-card error'>Error en conversión</div>"
    else:
        resultado = "<div class='result-card error'>Completa todos los campos</div>"

# Generar opciones HTML para el select
opciones_select = ""
for codigo in sorted(tasas_api.keys()):
    seleccionado_de = ' selected' if codigo == 'USD' else ''
    seleccionado_a = ' selected' if codigo == 'EUR' else ''
    opciones_select += f"<option value='{codigo}'{seleccionado_de}>{codigo}</option>"

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ejercicio 28 — Conversor de monedas en tiempo real</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
    <style>
        * {{ margin:0; padding:0; box-sizing:border-box; }}
        body {{ background:#0f172a; color:#f1f5f9; font-family:'Inter',sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }}
        .container {{ max-width:600px; width:100%; background:#1e293b; border-radius:24px; padding:32px; box-shadow:0 20px 40px -10px #00000080; border:1px solid #334155; }}
        h1 {{ font-size:2rem; font-weight:600; background:linear-gradient(135deg,#2dd4bf,#f472b6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:24px; }}
        .info-badge {{ 
            background:#0f172a; 
            border-radius:12px; 
            padding:10px 15px; 
            margin:10px 0; 
            border-left:4px solid #f472b6;
            font-size:0.9rem;
            color:#94a3b8;
            display:flex;
            align-items:center;
            gap:10px;
        }}
        .info-badge span {{ color:#2dd4bf; font-weight:600; }}
        .warning {{ color:#facc15; }}
        form {{ background:#0f172a; border-radius:16px; padding:20px; margin:16px 0; }}
        label {{ display:block; margin-bottom:8px; color:#94a3b8; font-weight:500; }}
        input, select {{ 
            background:#1e293b; 
            color:#f1f5f9; 
            border:2px solid #334155; 
            padding:12px 16px; 
            border-radius:12px; 
            font-size:1rem; 
            width:100%; 
            margin-bottom:16px; 
            transition:all 0.2s;
        }}
        input:focus, select:focus {{ border-color:#2dd4bf; outline:none; box-shadow:0 0 0 3px rgba(45,212,191,0.2); }}
        .fila-selectores {{
            display:flex;
            gap:10px;
            align-items:center;
            margin-bottom:16px;
        }}
        .fila-selectores select {{
            flex:1;
            margin-bottom:0;
        }}
        .fila-selectores span {{
            font-size:1.5rem;
            color:#94a3b8;
        }}
        input[type="submit"] {{ 
            background:linear-gradient(135deg,#2dd4bf,#14b8a6); 
            color:#0f172a; 
            font-weight:600; 
            border:none; 
            cursor:pointer;
            font-size:1.1rem;
            letter-spacing:0.5px;
        }}
        input[type="submit"]:hover {{ 
            background:linear-gradient(135deg,#f472b6,#ec4899); 
            color:white; 
            transform:translateY(-2px);
            box-shadow:0 8px 16px -4px rgba(244,114,182,0.4);
        }}
        .result-card {{ 
            background:#0f172a; 
            border-radius:16px; 
            padding:20px; 
            margin:16px 0; 
            border-left:6px solid #2dd4bf;
        }}
        .result-card.error {{ border-left-color:#f87171; }}
        .resultado-principal {{
            font-size:1.5rem;
            margin-bottom:10px;
        }}
        .resultado-principal strong {{
            color:#2dd4bf;
            font-size:1.8rem;
        }}
        .tasa-info {{
            color:#94a3b8;
            font-size:0.9rem;
            border-top:1px solid #334155;
            padding-top:10px;
            margin-top:10px;
        }}
        .monedas-destacadas {{
            display:flex;
            flex-wrap:wrap;
            gap:8px;
            margin:15px 0;
        }}
        .moneda-tag {{
            background:#1e293b;
            border:1px solid #334155;
            border-radius:20px;
            padding:4px 12px;
            font-size:0.85rem;
            color:#94a3b8;
        }}
        .moneda-tag strong {{ color:#2dd4bf; }}
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
        <h1>💱 Ejercicio 28 — Conversor de monedas</h1>
        
        <div class="info-badge">
            <span>📡 {len(tasas_api)} monedas disponibles</span>
            {error_conexion}
        </div>

        <div class="monedas-destacadas">
            <span style="color:#94a3b8; margin-right:5px;">Destacadas:</span>
            {"".join([f"<span class='moneda-tag'><strong>{m}</strong></span>" for m in monedas_destacadas])}
        </div>

        <form method="post">
            <label for="monto">Cantidad:</label>
            <input type="number" step="0.01" name="monto" id="monto" value="1.00" required>

            <div class="fila-selectores">
                <select name="de" id="de">
                    {"".join([f"<option value='{c}' {'selected' if c == 'USD' else ''}>{c}</option>" for c in sorted(tasas_api.keys())])}
                </select>
                <span>→</span>
                <select name="a" id="a">
                    {"".join([f"<option value='{c}' {'selected' if c == 'EUR' else ''}>{c}</option>" for c in sorted(tasas_api.keys())])}
                </select>
            </div>

            <input type="submit" value="Convertir">
        </form>

        {resultado}

        <a href="index.py" class="back-link"><span class="arrow">←</span> Volver al inicio</a>
    </div>
</body>
</html>""")