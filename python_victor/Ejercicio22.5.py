# -*- coding: utf-8 -*-
import cgi
import cgitb
cgitb.enable()

print("Content-type: text/html; charset=utf-8\n\n")

form = cgi.FieldStorage()
# --- LÓGICA DE PROCESAMIENTO DE LOOPS ---
nodos_html = ""
color_accent = "#00fbff" # Aqua por defecto

if ('parsed_data' in locals() and 'limite' in parsed_data):
    # Extraemos valores del formulario
    try:
        limite = int(parsed_data['limite'][0])   # Hasta dónde llega el for
        saltar = int(parsed_data['saltar'][0])   # El número que usará 'continue'
        detener = int(parsed_data['detener'][0]) # El número que usará 'break'
        color_val = parsed_data.get('color', ['aqua'])[0]
    except:
        limite, saltar, detener, color_val = 10, 0, 0, "aqua"

    colores_neon = {
        "rojo": "#ff003c", "verde": "#00ff66", "aqua": "#00f2ff", "violeta": "#bc13fe"
    }
    color_accent = colores_neon.get(color_val, "#00fbff")
    
    # --- EL MOTOR DE LOOPS ---
    nodos_html = "<div class='loop-container'>"
    
    # Iniciamos un bucle FOR
    for i in range(1, limite + 1):
        
        # Uso de CONTINUE: Si el número es el elegido para saltar, no lo dibuja
        if i == saltar:
            nodos_html += f"<div class='node skip' style='border-color: #555;'>SKIP {i}</div>"
            continue
            
        # Uso de BREAK: Si llegamos al número de parada, cortamos el bucle
        if i == detener:
            nodos_html += f"<div class='node break' style='border-color: {color_accent}; background: {color_accent}33;'>BREAK {i}</div>"
            break
            
        # Dibujamos un nodo normal
        nodos_html += f"<div class='node' style='border-color: {color_accent}; box-shadow: 0 0 10px {color_accent}44;'>{i}</div>"
    
    nodos_html += "</div>"

print(f"""<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Loop Processor v4.0</title>

</head>
<body>
    <canvas id="canvas-bg"></canvas>

    <div class="glass-card">
        <h1>LOOP·PROCESSOR</h1>
        <div style="color: var(--neon); letter-spacing: 4px; font-size: 0.7rem; margin-bottom: 20px;">FOR | CONTINUE | BREAK</div>

        <form method="post">
            <div class="grid-inputs">
                <input type="number" name="limite" placeholder="Límite (ej. 20)" required>
                <input type="number" name="saltar" placeholder="Saltar con CONTINUE">
                <input type="number" name="detener" placeholder="Parar con BREAK">
                <select name="color">
                    <option value="aqua">CYBER AQUA</option>
                    <option value="rojo">ROJO NEÓN</option>
                    <option value="verde">BIO-VERDE</option>
                    <option value="violeta">VIOLETA</option>
                </select>
            </div>
            <button type="submit" class="btn-cyber">EJECUTAR SECUENCIA ⚡</button>
        </form>

        {nodos_html}

        <a href="index.py" style="display:block; margin-top: 25px; color: #555; text-decoration: none; font-size: 0.8rem;">REINICIAR NÚCLEO</a>
    </div>

   
</body>
</html>""")
