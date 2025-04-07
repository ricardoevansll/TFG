<?php
require_once 'auth.php'; // valida autenticación
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio-cloudSostenible</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php"><img src="assets/cloudSostenible.png" alt="Logo cloudSostenible"></a>
        </div>
        <nav>
            <a href="index.php">Inicio</a>
            <a href="usuarios.php">Usuarios</a>
            <a href="fuentes.php">Fuentes-Energía</a>
            <a href="recursos.php">Recursos</a>
            <a href="alertas.php">Alertas</a>
            <a href="http://192.168.23.132:3000/d/fegfwr5rur8xse/panel-cloudsostenible?orgId=1&from=now-30m&to=now&timezone=browser&refresh=10s" target="_blank">Monitoreo</a>
            <a href="contacto.php">Contacto</a>
            <a href="logout.php">Salir</a>
        </nav>
    </header>
    <h1>Sistema cloudSostenible</h1>
    <div class="boxes-container">
        <div class="box">
            <div class="box-image">
                <img src="assets/recursos.jpg" alt="Recursos">
            </div>
            <div class="box-content">
                <p>Gestiona los recursos de la nube</p>
                <a href="recursos.php">Ver Recursos</a>
            </div>
        </div>
        <div class="box">
            <div class="box-image">
                <img src="assets/fuentes.jpg" alt="Fuentes">
            </div>
            <div class="box-content">
                <p>Administra las fuentes de energía disponibles</p>
                <a href="fuentes.php">Ver Fuentes</a>
            </div>
        </div>
        <div class="box">
            <div class="box-image">
                <img src="assets/dashboard.jpg" alt="Monitorización">
            </div>
            <div class="box-content">
                <p>Monitorizar los recursos del sistema</p>
                <a href="http://192.168.23.132:3000/d/fegfwr5rur8xse/panel-cloudsostenible?orgId=1&from=now-30m&to=now&timezone=browser&refresh=10s">Ver Dashboard</a>
            </div>
        </div>
    </div>
    <br/>
    <div class="cierre">
        <a href="index.php">Inicio</a>
        <a href="logout.php">Cerrar sesión</a>
    </div>
    <br/>
    <footer>
        <p>&copy; 2025 cloudSostenible | <a href="politicas.php">Políticas y Condiciones</a></p>
    </footer>
    <script src="js/scripts.js"></script>
</body>
</html>