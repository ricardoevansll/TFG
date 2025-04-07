<?php
session_start();
require_once 'auth.php'; // Requiere autenticación
require_once 'db_connect.php';
require_once 'lib/PHPMailer/src/PHPMailer.php';
require_once 'lib/PHPMailer/src/SMTP.php';
require_once 'lib/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Obtener datos del usuario autenticado
$stmt = $pdo->prepare("SELECT nombre, email FROM usuario WHERE iduser = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$nombre_default = $user['nombre'] ?? '';
$email_default = $user['email'] ?? '';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
    $mensaje = filter_input(INPUT_POST, 'mensaje', FILTER_SANITIZE_STRING);

    // Validaciones básicas
    if (empty($nombre)) $errors[] = "El nombre es requerido.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "El email no es válido.";
    if (empty($telefono)) $errors[] = "El teléfono es requerido.";
    if (empty($mensaje)) $errors[] = "El mensaje es requerido.";

    if (empty($errors)) {
        // Enviar correo con PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'soytechsoporte@gmail.com'; //correo
            $mail->Password = 'fszo tmig ybug sinp';   //contraseña de aplicación
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('soytechsoporte@gmail.com', 'cloudSostenible');
            $mail->addAddress('soytechsoporte@gmail.com'); // administrador

            $mail->isHTML(true);
            $mail->Subject = 'Nuevo mensaje de contacto - ' . $nombre;
            $mail->Body = "
                <h2>Nuevo mensaje recibido</h2>
                <p><strong>Nombre:</strong> " . htmlspecialchars($nombre) . "</p>
                <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                <p><strong>Teléfono:</strong> " . htmlspecialchars($telefono) . "</p>
                <p><strong>Mensaje:</strong> " . nl2br(htmlspecialchars($mensaje)) . "</p>
                <p><strong>Enviado por usuario ID:</strong> " . $_SESSION['user_id'] . "</p>
            ";
            $mail->AltBody = "Nuevo mensaje recibido\nNombre: $nombre\nEmail: $email\nTeléfono: $telefono\nMensaje: $mensaje\nEnviado por usuario ID: " . $_SESSION['user_id'];

            $mail->send();
            $success = "Mensaje enviado exitosamente. Te contactaremos en breve.";
        } catch (Exception $e) {
            $errors[] = "Error al enviar el mensaje: " . $mail->ErrorInfo;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contáctanos</title>
    <link rel="stylesheet" href="css/styles.css">
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
    <h1>Contáctenos</h1>
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php else: ?>
        <div class="contact-container">
            <div class="contact-card">
                <h2>Envíanos un mensaje</h2>
                <form method="POST">
                    <input type="text" name="nombre" placeholder="Nombre" value="<?php echo isset($nombre) ? htmlspecialchars($nombre) : htmlspecialchars($nombre_default); ?>" required>
                    <input type="email" name="email" placeholder="Email" value="<?php echo isset($email) ? htmlspecialchars($email) : htmlspecialchars($email_default); ?>" required>
                    <input type="tel" name="telefono" placeholder="Teléfono" value="<?php echo isset($telefono) ? htmlspecialchars($telefono) : ''; ?>" required>
                    <textarea name="mensaje" placeholder="Tu mensaje" required><?php echo isset($mensaje) ? htmlspecialchars($mensaje) : ''; ?></textarea>
                    <button type="submit">Enviar</button>
                </form>
                <p>Volver a <a href="politicas.php">Póliticas y Condiciones</a></p>
            </div>
        </div>
    <?php endif; ?>
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