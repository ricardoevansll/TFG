<?php
// Inicia la sesión para gestionar variables de sesión user_id y rol
session_start();
// Requiere el fichero de conexión a la base de datos "sostenible"
require_once 'db_connect.php';

// Verifica si el formulario fue enviado mediante el método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // captura y limpia el email para evitar inyecciones sqls
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    // Obtiene la contraseña, validación más adelante 
    $password = $_POST['password'];
    // preparar la consulta SQL para buscar el usuario por email en la tabla "usuario"
    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = ?");
    // Ejecuta la consulta con el email como parámetro seguro
    $stmt->execute([$email]);
    // Recupera el primer resultado como un array asociativo
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // Verificación de aceptación de términos (1 = sí, 0 = no)    
    $terms_accepted = $user ? $user['terms_accepted'] : 0;
    // Validación si usuario existe y contraseña coincide con hash almacenado
    if ($user && password_verify($password, $user['password'])) {
        // Validación checkbox, update base de datos con la aceptación
        if (isset($_POST['accept_terms']) && !$terms_accepted) {
            $stmt = $pdo->prepare("UPDATE usuario SET terms_accepted = 1 WHERE iduser = ?");
            $stmt->execute([$user['iduser']]);
        }
        // Variables de sesión usuario autenticado
        $_SESSION['user_id'] = $user['iduser'];
        $_SESSION['rol'] = $user['rol'];
        // Muestra página (index.php)
        header("Location: index.php");
        // Termina la ejecución 
        exit();
    } else {
        // Credeneciales inválidas, definición del mensaje de error
        $error = "Credenciales inválidas";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login-cloudSostenible</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Popup de aceptación de Cookies -->
    <div id="cookie-popup" class="cookie-popup">
        <p>Este sitio utiliza cookies para mejorar tu experiencia. Al continuar navegando, aceptas nuestro uso de cookies.</p>
        <button id="accept-cookies">Aceptar</button>
    </div>
    <header>
        <div class="logo">
            <a href="index.php"><img src="assets/cloudSostenible.png" alt="Logo cloudSostenible"></a>
        </div>
    </header>
    
       <!-- Muestra mensaje de error "Credenciales inválidas"-->
       <?php if (isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Contenedor del formulario de inicio de sesión -->
    <div class="form-container">
        <!-- Formulario para envíar datos por POST a login.php-->
        <h1>Iniciar Sesión</h1>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required><br/>
            <input type="password" name="password" placeholder="Contraseña" required><br/>
            <!-- Checkbox aceptar términos, obligatorio si no está aceptado previamente -->
            <p><input type="checkbox" name="accept_terms" required> Acepto las <a href="condiciones.php" target="_blank">condiciones de uso</a><br/> y el tratamiento de datos.</p><br/>
            <!-- Botón para enviar formulario -->
            <button type="submit">Iniciar Sesión</button>
        </form><br/>
        <!-- Link/opción para recuperación de contraseña -->
        <a href="forgot_password.php">¿Olvidaste tu usuario o contraseña?</a>
    </div><br/>
    <br/>
    <footer>
        <p>&copy; 2025 cloudSostenible | <a href="condiciones.php">Condiciones de uso de datos</a></p>
    </footer>
    <script src="js/scripts.js"></script>
</body>
</html>