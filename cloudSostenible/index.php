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
    /*    nav {
            background-color: #333;
            padding: 1rem;
            margin-bottom: 20px;
        }
        nav a {
            color: white;
            margin: 0 15px;
        }
        nav a:hover {
            color: #ddd;
        }*/
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
            <a href="http://192.168.23.132:3000/d/eef0c6h1h296od/sostenible?orgId=1&from=now-6h&to=now&timezone=browser" target="_blank">Monitoreo</a>
            <a href="logout.php">Salir</a>
        </nav>
    </header>
    <h1>Sistema cloudSostenible</h1>
    <p>Seleccione una opción para gestionar</p></br>
    <a href="logout.php">Cerrar sesión</a>
    <br/>
    <br/>
    <footer>
        <p>&copy; 2025 cloudSostenible | <a href="politicas.html">Políticas</a></p>
    </footer>
    <script src="js/scripts.js"></script>
</body>
</html>