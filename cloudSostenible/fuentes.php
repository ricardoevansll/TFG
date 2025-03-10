<?php
require_once 'auth.php';
require_once 'db_connect.php';

function getFuentes($pdo) {
    $stmt = $pdo->query("SELECT * FROM fuente");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$errors = [];
$success = '';
$is_admin = $_SESSION['rol'] === 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add'])) {
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
            $renovable = isset($_POST['renovable']) ? 1 : 0;

            if (empty($nombre)) $errors[] = "El nombre es requerido";

            if (empty($errors)) {
                $stmt = $pdo->prepare("INSERT INTO fuente (nombre, renovable) VALUES (?, ?)");
                $stmt->execute([$nombre, $renovable]);
                $success = "Fuente agregada exitosamente";
            }
        }

        if (isset($_POST['edit'])) {
            $id = filter_input(INPUT_POST, 'id_energia', FILTER_VALIDATE_INT);
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
            $renovable = isset($_POST['renovable']) ? 1 : 0;

            if (!$id) $errors[] = "ID inválido";
            if (empty($nombre)) $errors[] = "El nombre es requerido";

            if (empty($errors)) {
                $stmt = $pdo->prepare("UPDATE fuente SET nombre = ?, renovable = ? WHERE id_energia = ?");
                $stmt->execute([$nombre, $renovable, $id]);
                $success = "Fuente actualizada exitosamente";
            }
        }

        if (isset($_POST['delete']) && $is_admin) { // Solo admin puede eliminar
            $id = filter_input(INPUT_POST, 'id_energia', FILTER_VALIDATE_INT);
            if (!$id) {
                $errors[] = "ID inválido";
            } else {
                $stmt = $pdo->prepare("DELETE FROM fuente WHERE id_energia = ?");
                $stmt->execute([$id]);
                $success = "Fuente eliminada exitosamente";
            }
        }
    } catch (PDOException $e) {
        $errors[] = "Error en la base de datos: " . $e->getMessage();
    }
}

$fuentes = getFuentes($pdo);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fuentes-cloudSostenible</title>
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

    <h1>Gestión de Fuentes-Energía</h1>
    
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
            <h2>Agregar Fuente de energía</h2>
            <input type="text" name="nombre" placeholder="Nombre" required>
            <div>
                <label><input type="checkbox" name="renovable"> Renovable</label>
            </div><br/>
            <button type="submit" name="add">Agregar</button>
        </form>
    </div>

    <h2>Listado de fuentes de energía</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Renovable</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($fuentes as $fuente): ?>
        <tr>
            <td><?php echo htmlspecialchars($fuente['id_energia']); ?></td>
            <td><?php echo htmlspecialchars($fuente['nombre']); ?></td>
            <td><?php echo $fuente['renovable'] ? 'Sí' : 'No'; ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id_energia" value="<?php echo $fuente['id_energia']; ?>">
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($fuente['nombre']); ?>" required>
                    <label><input type="checkbox" name="renovable" <?php echo $fuente['renovable'] ? 'checked' : ''; ?>> Renovable</label>
                    <button type="submit" name="edit">Actualizar</button>
                </form>
                <?php if ($is_admin): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id_energia" value="<?php echo $fuente['id_energia']; ?>">
                        <button type="submit" name="delete" onclick="return confirm('¿Seguro que desea eliminar?')">Eliminar</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table><br/>
    <br/>
    <a href="index.php">Regresar a inicio</a>
    <a href="logout.php">Cerrar sesión</a>
    <footer>
        <p>&copy; 2025 cloudSostenible | <a href="politicas.html">Políticas</a></p>
    </footer>
    <script src="js/scripts.js"></script>
</body>
</html>