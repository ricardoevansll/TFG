from flask import Flask, Response
from prometheus_client import Gauge, generate_latest
import random
import mysql.connector
from datetime import datetime

app = Flask(__name__)

# Métricas Prometheus
cpu_usage = Gauge('cpu_usage_percent', 'Uso de CPU en %')
power_consumption = Gauge('power_consumption_watts', 'Consumo estimado de energía (W)')
carbon_emission = Gauge('carbon_emission_kg', 'Huella de carbono estimada (kg CO2)')
pue_metric = Gauge('pue_value', 'PUE estimado del sistema')

# Conexión mysql
db_config = {
    "host": "localhost",
    "user": "revans",
    "password": "%Tuto2323",
    "database": "sostenible"
}

def get_recurso_id():
    """ Obtiene el ID de un recurso activo en la base de datos """
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()
        cursor.execute("SELECT id_recurso FROM recurso WHERE estado='activo' LIMIT 1")
        recurso = cursor.fetchone()
        cursor.close()
        conn.close()
        return recurso[0] if recurso else None
    except Exception as e:
        print("Error al obtener recurso:", e)
        return None

# Consultar si el recurso usa energía renovable
def get_energia_renovable(recurso_id):
    """ Obtiene si el recurso usa energía renovable (1 = Sí, 0 = No) """
    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()
        cursor.execute("SELECT energia_renovable FROM recurso WHERE id_recurso = %s", (recurso_id,))
        result = cursor.fetchone()
        cursor.close()
        conn.close()
        return result[0] if result else 0  # 0 si no hay dato
    except Exception as e:
        print("Error al obtener energía renovable:", e)
        return 0


def save_to_mysql(recurso_id, timestamp, pue, carbon):
    """ Guarda los valores en la base de datos MySQL """
    if recurso_id is None:
        print("No hay recursos activos en la base de datos.")
        return

    try:
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()

        query = """INSERT INTO consumo (id_recurso, timestamp, pue, carbon)
                   VALUES (%s, %s, %s, %s)"""
        timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        cursor.execute(query, (recurso_id, timestamp, pue, carbon))

        conn.commit()
        cursor.close()
        conn.close()
    except Exception as e:
        print("Error al insertar en MySQL:", e)

@app.route('/metrics')
def metrics():
    """ Genera y expone métricas en formato Prometheus """
    recurso_id = get_recurso_id()
    energia_renovable = get_energia_renovable(recurso_id)
    
    # Simulación del uso de CPU en porcentaje
    cpu = random.uniform(10, 90)
    cpu_usage.set(cpu)

    # Cálculo estimado del consumo energético
    power = ((cpu / 100) * 200) + 50  # 200W en carga, 50W en idle
    power_consumption.set(power)

   # Cálculo de la huella de carbono (0.4 kg CO₂/kWh por defecto)
    carbon = (power / 1000) * 0.4
    if energia_renovable == 1:
        carbon = 0.02  # Se reduce a un valor mínimo
    carbon_emission.set(carbon)

    # Reducción de PUE si la energía es renovable
    pue = round(random.uniform(1.5, 2.5), 2)
    if energia_renovable == 1:
        pue *= 0.8  # Reducción del 20%
        pue = round(pue, 2)
    pue_metric.set(pue)

    timestamp = datetime.now()  # Capturamos la fecha y hora actuales
    
    # Guardar en la base de datos
    save_to_mysql(recurso_id, timestamp, pue, carbon)

    return Response(generate_latest(), mimetype="text/plain")

if __name__ == '__main__':
    app.run(host="0.0.0.0", port=9105)
