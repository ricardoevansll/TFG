<?php
require_once 'auth.php';
require_once 'db_connect.php';

function getUsuarios($pdo) {
    $stmt = $pdo->query("SELECT * FROM usuario");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$errors = [];
$success = '';
$is_admin = $_SESSION['rol'] === 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_admin) {
    try {
        if (isset($_POST['add'])) {
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $rol = $_POST['rol'];

            if (empty($nombre)) $errors[] = "El nombre es requerido";
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido";
            if (strlen($password) < 8) $errors[] = "La contraseña debe tener al menos 8 caracteres";
            if (!in_array($rol, ['admin', 'usuario'])) $errors[] = "Rol inválido";

            if (empty($errors)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuario (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nombre, $email, $password_hash, $rol]);
                $success = "Usuario agregado exitosamente";
            }
        }
    } catch (PDOException $e) {
        $errors[] = "Error en la base de datos: " . $e->getMessage();
    }
}

$usuarios = $is_admin ? getUsuarios($pdo) : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Usuario-cloudSostenible</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/users.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

    <h1>Gestión de Usuarios</h1>

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

    <?php if ($is_admin): ?>
        <div class="form-container">
            <form method="POST">
                <h2>Agregar Usuario</h2>
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <select name="rol" required>
                    <option value="admin">Admin</option>
                    <option value="usuario">Usuario</option>
                </select>
                <button type="submit" name="add">Agregar</button>
            </form>
        </div>

        <div class="search-container">
            <input type="text" id="search" class="search-bar" placeholder="Buscar por nombre o email...">
        </div>
        <div class="card-container" id="users-list">
            <?php foreach ($usuarios as $usuario): ?>
                <div class="card <?php echo $usuario['rol'] === 'admin' ? 'rol-admin' : 'rol-usuario'; ?>" data-id="<?php echo $usuario['iduser']; ?>">
                    <div class="info">
                        <h3><?php echo htmlspecialchars($usuario['nombre']); ?></h3>
                        <p><i class="fas fa-envelope"></i> Email: <?php echo htmlspecialchars($usuario['email']); ?></p>
                        <p><i class="fas fa-user-tag"></i> Rol: <?php echo htmlspecialchars($usuario['rol']); ?></p>
                        <p><i class="fas fa-clock"></i> Fecha Registro: <?php echo htmlspecialchars($usuario['fecha_registro']); ?></p>
                    </div>
                    <form class="edit-form" method="POST" action="update_users.php">
                        <input type="hidden" name="iduser" value="<?php echo $usuario['iduser']; ?>">
                        <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                        <select name="rol" required>
                            <option value="admin" <?php echo $usuario['rol'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="usuario" <?php echo $usuario['rol'] === 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                        </select>
                        <button type="submit" class="save-btn">Guardar</button>
                    </form>
                    <div class="actions">
                        <button class="edit-btn"><i class="fas fa-edit"></i> Editar</button>
                        <button class="delete-btn" data-id="<?php echo $usuario['iduser']; ?>"><i class="fas fa-trash"></i> Eliminar</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No tienes permisos para gestionar usuarios.</p>
    <?php endif; ?><br/>
    <br/>
    <div class="cierre">
        <a href="index.php">Inicio</a>
        <a href="logout.php">Cerrar sesión</a>
    </div>
    <br/>
    <br/>
    <footer>
        <p>&copy; 2025 cloudSostenible | <a href="politicas.php">Políticas y Condiciones</a></p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/users.js"></script>
</body>
</html>