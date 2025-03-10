<?php
require_once 'auth.php';
require_once 'db_connect.php';

function getUsuarios($pdo) {
    $stmt = $pdo->query("SELECT * FROM usuario");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$errors = [];
$success = '';
$is_admin = $_SESSION['rol'] === 'admin'; // validación si es admin

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_admin) { // Solo admin puede procesar POST
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

        if (isset($_POST['edit'])) {
            $id = filter_input(INPUT_POST, 'iduser', FILTER_VALIDATE_INT);
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $rol = $_POST['rol'];

            if (!$id) $errors[] = "ID inválido";
            if (empty($nombre)) $errors[] = "El nombre es requerido";
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido";
            if (!in_array($rol, ['admin', 'usuario'])) $errors[] = "Rol inválido";

            if (empty($errors)) {
                $stmt = $pdo->prepare("UPDATE usuario SET nombre = ?, email = ?, rol = ? WHERE iduser = ?");
                $stmt->execute([$nombre, $email, $rol, $id]);
                $success = "Usuario actualizado exitosamente";
            }
        }

        if (isset($_POST['delete'])) {
            $id = filter_input(INPUT_POST, 'iduser', FILTER_VALIDATE_INT);
            if (!$id) {
                $errors[] = "ID inválido";
            } else {
                $stmt = $pdo->prepare("DELETE FROM usuario WHERE iduser = ?");
                $stmt->execute([$id]);
                $success = "Usuario eliminado exitosamente";
            }
        }
    } catch (PDOException $e) {
        $errors[] = "Error en la base de datos: " . $e->getMessage();
    }
}

$usuarios = $is_admin ? getUsuarios($pdo) : []; // Solo listar si es admin
?>

<!DOCTYPE html>
<html>
<head>
    <title>Usuario-cloudSostenible</title>
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
            <a href="http://192.168.23.132:3000/d/eef0c6h1h296od/sostenible?orgId=1&from=now-6h&to=now&timezone=browser" target="_blank">Monitoreo</a>
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
                <select name="rol">
                    <option value="admin">Admin</option>
                    <option value="usuario">Usuario</option>
                </select>
                <button type="submit" name="add">Agregar</button>
            </form>
        </div>

        <h2>Listado de Usuarios</h2>
        <?php if (empty($usuarios)): ?>
            <p>No hay usuarios para mostrar.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Fecha Registro</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['iduser']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['fecha_registro']); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="iduser" value="<?php echo $usuario['iduser']; ?>">
                            <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                            <select name="rol">
                                <option value="admin" <?php echo $usuario['rol'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                <option value="usuario" <?php echo $usuario['rol'] === 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                            </select>
                            <button type="submit" name="edit">Actualizar</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="iduser" value="<?php echo $usuario['iduser']; ?>">
                            <button type="submit" name="delete" onclick="return confirm('¿Seguro que desea eliminar?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    <?php else: ?>
        <p>No tienes permisos para gestionar usuarios.</p>
    <?php endif; ?><br/>
    <br/>
    <a href="index.php">Regresar a inicio</a>
    <a href="logout.php">Cerrar sesión</a>
    <footer>
        <p>&copy; 2025 cloudSostenible | <a href="politicas.html">Políticas</a></p>
    </footer>
    <script src="js/scripts.js"></script>
</body>
</html>