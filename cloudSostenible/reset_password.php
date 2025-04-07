<?php
session_start();
require_once 'db_connect.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reset) {
        $errors[] = "El enlace es inválido o ha expirado.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reset) {
            $email = $reset['email'];
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuario SET password = ? WHERE email = ?");
            $stmt->execute([$password_hash, $email]);

            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->execute([$token]);

            $success = "Contraseña restablecida exitosamente. <a href='login.php'>Inicia sesión</a>";
        } else {
            $errors[] = "El enlace es inválido o ha expirado.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Cambiar Contraseña-cloudSostenible</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php"><img src="assets/cloudSostenible.png" alt="Logo cloudSostenible"></a>
        </div>
    </header>

    <h1>Cambiar Contraseña</h1>
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?php echo $success; ?></div>        
    <?php else: ?>
        <div class="form-container">
            <form method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token ?? ''); ?>">
                <input type="password" name="password" placeholder="Nueva contraseña" required>
                <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required>
                <br/>
                <button type="submit">Cambiar contraseña</button>
            </form>
        </div>
    <?php endif; ?>
    <br/>
    <br/>
    <div class="cierre"> 
        <p><a href="login.php">Volver al inicio de sesión</a></p>
    </div>
  </body>
</html>