from flask import Flask, Response
from prometheus_client import Gauge, generate_latest
import random
import mysql.connector
from datetime import datetime
import socket  # nombre_svr
import time

app = Flask(__name__)

# Metricas Prometheus
cpu_usage = Gauge('cpu_usage_percent', 'Uso de CPU en %')
power_consumption = Gauge('power_consumption_watts', 'Consumo estimado de energía (W)')
carbon_emission = Gauge('carbon_emission_kg', 'Huella de carbono estimada (kg CO2)')
pue_metric = Gauge('pue_value', 'PUE estimado del sistema')

# Conexion mysql
db_config = {
    "host": "192.168.23.132",
    "user": "revans",
    "password": "%Tuto2323",
    "database": "sostenible"
}

def get_recurso_info():
    """ Obtiene id_recurso y energia_renovable en una sola consulta """
    try:
        hostname = socket.gethostname()
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor(dictionary=True)
        
        cursor.execute("SELECT id_recurso, energia_renovable FROM recurso WHERE nombre = %s", (hostname,))
        recurso = cursor.fetchone()
        
        cursor.close()
        conn.close()
        return recurso if recurso else {"id_recurso": None, "energia_renovable": 0}
    except Exception as e:
        print("Error al obtener recurso:", e)
        return {"id_recurso": None, "energia_renovable": 0}

def save_to_mysql(recurso_id, pue, carbon):
    """ Guarda valores en MySQL de manera optimizada """
    if not recurso_id:
        return
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()
        
        query = "INSERT INTO consumo (id_recurso, timestamp, pue, carbon) VALUES (%s, NOW(), %s, %s)"
        cursor.execute(query, (recurso_id, pue, carbon))
        
        conn.commit()
        cursor.close()
        conn.close()
    except Exception as e:
        print("Error al insertar en MySQL:", e)

@app.route('/metrics')
def metrics():
    """ Expone métricas en formato Prometheus """
    recurso = get_recurso_info()
    recurso_id, energia_renovable = recurso["id_recurso"], recurso["energia_renovable"]
    
    # Simulación de uso de CPU
    cpu = random.uniform(10, 90)
    cpu_usage.set(cpu)

    # Cálculo de consumo de energía
    power = ((cpu / 100) * 200) + 50
    power_consumption.set(power)

    # Cálculo de huella de carbono
    carbon = (power / 1000) * 0.4
    if energia_renovable == 1:
        carbon = 0.02
    carbon_emission.set(carbon)

    # Cálculo del PUE
    pue = round(random.uniform(1.5, 2.5), 2)
    if energia_renovable == 1:
        pue = round(pue * 0.8, 2)
    pue_metric.set(pue)

    # Guardar en la base de datos
    save_to_mysql(recurso_id, pue, carbon)

    return Response(generate_latest(), mimetype="text/plain")

if __name__ == '__main__':
    from waitress import serve  # Servidor más eficiente que Flask por sí solo
    serve(app, host="0.0.0.0", port=9105)
