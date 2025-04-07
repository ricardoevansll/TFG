<?php
session_start();
require_once 'db_connect.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones - cloudSostenible</title>
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
        </nav>
    </header>
    <h1>Sistema cloudSostenible</h1>
     <section class="politicas">
        <h1>Términos y condiciones de uso de datos</h1>
        <p>Última actualización: 24/03/2025</p>

        <h2>1. Introducción</h2>
        <p>
            Bienvenido al sistema cloudSostenible. Al utilizar nuestra aplicación web y servicios, aceptas los siguientes términos y condiciones.
            Por favor, léelos detenidamente.
        </p>

        <h2>2. Protección de Datos Personales</h2>
        <p>
            De acuerdo con el Reglamento General de Protección de Datos (GDPR) de la Unión Europea, nos comprometemos a proteger tu
            privacidad y tus datos personales. A continuación, te explicamos cómo recopilamos, usamos y protegemos tu información.
        </p>

        <h3>2.1. Datos que Recopilamos</h3>
        <p>
            Recopilamos la siguiente información cuando te registras o utilizas nuestros servicios:
        </p>
        <ul>
            <li>Nombre y apellidos</li>
            <li>Dirección de correo electrónico</li>
            <li>Número de teléfono</li>
            <li>Dirección IP y datos de navegación</li>
        </ul>

        <h3>2.2. Uso de los Datos</h3>
        <p>
            Utilizamos tus datos personales para:
        </p>
        <ul>
            <li>Proporcionar y mejorar nuestros servicios.</li>
            <li>Comunicarnos contigo sobre actualizaciones y ofertas.</li>
            <li>Cumplir con obligaciones legales.</li>
        </ul>

        <h3>2.3. Derechos del Usuario</h3>
        <p>
            De acuerdo con el GDPR, tienes los siguientes derechos:
        </p>
        <ul>
            <li>Acceder a tus datos personales.</li>
            <li>Solicitar la rectificación o eliminación de tus datos.</li>
            <li>Oponerte al tratamiento de tus datos.</li>
            <li>Solicitar la portabilidad de tus datos.</li>
        </ul>

        <h2>3. Cookies</h2>
        <p>
            Utilizamos cookies para mejorar tu experiencia en nuestro sitio web. Puedes gestionar o desactivar las cookies
            desde la configuración de tu navegador.
        </p>

        <h2>4. Cambios en los Términos</h2>
        <p>
            Nos reservamos el derecho de modificar estos términos y condiciones en cualquier momento. Te notificaremos
            de cualquier cambio significativo.
        </p>
    </section>
    <br/>
    <div class="cierre">
        <a href="index.php">Inicio</a>
    </div>
    <br/>
    <footer>
        <p>&copy; 2025 cloudSostenible | <a href="condiciones.php">Términos y condiciones de uso de datos</a></p>
    </footer>
    <script src="js/scripts.js"></script>
</body>
</html>