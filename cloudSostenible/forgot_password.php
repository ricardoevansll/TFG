<?php
session_start();
require_once 'db_connect.php';
// Ubicación del PHPMailer
require_once 'lib/PHPMailer/src/PHPMailer.php';
require_once 'lib/PHPMailer/src/SMTP.php';
require_once 'lib/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email inválido";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Generar token único
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Guardar token en la base de datos
            $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$email, $token, $expires_at]);

            // Enviar correo con PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Servidor SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'soporte@gmail.com'; // correo
                $mail->Password = 'xxxx xxxx xxxx xxxx';  //contraseña de aplicaciónn
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Remitente y destinatario
                $mail->setFrom('soytechsoporte@gmail.com', 'cloudSostenible');
                $mail->addAddress($email);

                // Contenido del correo
                $reset_link = "http://192.168.23.132/reset_password.php?token=" . $token;
                $mail->isHTML(true);
                $mail->Subject = 'Restablecer tu password';
                $mail->Body = "Hola,<br>Haz clic en el siguiente enlace para restablecer tu Password: <a href='$reset_link'>$reset_link</a><br>Este enlace expira en 1 hora.";
                $mail->AltBody = "Hola,\nHaz clic en el siguiente enlace para restablecer tu Password: $reset_link\nEste enlace expira en 1 hora.";

                $mail->send();
                $success = "Se ha enviado un enlace de restablecimiento a tu correo.";
            } catch (Exception $e) {
                $errors[] = "Error al enviar el correo: " . $mail->ErrorInfo;
            }
        } else {
            $errors[] = "No se ha encontrado un usuario con ese correo.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Restablecer Contraseña-cloudSostenible</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php"><img src="assets/cloudSostenible.png" alt="Logo cloudSostenible"></a>
        </div>
    </header>
    <h1>Restablecer Contraseña</h1>
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <div class="form-container">
        <form method="POST">
            <input type="email" name="email" placeholder="Ingresa tu correo" required><br/>
            <button type="submit">Enviar enlace</button>
        </form><br/>
        <br/>
    </div>
    <div class="cierre"> 
        <p><a href="login.php">Volver al inicio de sesión</a></p>
    </div>
</body>
</html>