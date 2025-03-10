<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    if (!isset($_POST['accept_terms'])) {
        $error = "Debes aceptar las condiciones para continuar.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['iduser'];
            $_SESSION['rol'] = $user['rol'];
            header("Location: index.php"); // goto index.php en lugar de usuarios.php
            exit();
        } else {
        $error = "Credenciales inválidas";
        }
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
    <!-- Popup de Cookies -->
    <div id="cookie-popup" class="cookie-popup">
        <p>Este sitio utiliza cookies para mejorar tu experiencia. Al continuar navegando, aceptas nuestro uso de cookies.</p>
        <button id="accept-cookies">Aceptar</button>
    </div>
    <header>
        <div class="logo">
            <a href="index.php"><img src="assets/cloudSostenible.png" alt="Logo cloudSostenible"></a>
        </div>
    </header>
       <?php if (isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <div class="form-container">
    <h1>Iniciar Sesión</h1>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required><br/>
            <input type="password" name="password" placeholder="Contraseña" required><br/>
            <p><input type="checkbox" name="accept_terms" required> Acepto las <a href="terms.php" target="_blank">condiciones de uso</a><br/> y el tratamiento de datos.</p><br/>
            <button type="submit">Iniciar Sesión</button>
        </form><br/>
        <a href="recuperar_password.php">¿Olvidaste tu usuario o contraseña?</a>
    </div><br/>
    <br/>
    <footer>
        <p>&copy; 2025 cloudSostenible | <a href="politicas.html">Políticas</a></p>
    </footer>
    <script src="js/scripts.js"></script>
</body>
</html>