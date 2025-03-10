<?php
require_once 'auth.php';
require_once 'db_connect.php';

function getRecursos($pdo) {
    $stmt = $pdo->query("SELECT r.*, f.nombre AS fuente_nombre FROM recurso r LEFT JOIN fuente f ON r.id_energia = f.id_energia");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

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
            $tipo = $_POST['tipo'];
            $estado = $_POST['estado'];
            $id_energia = filter_input(INPUT_POST, 'id_energia', FILTER_VALIDATE_INT) ?: null;
            $energia_renovable = isset($_POST['energia_renovable']) ? 1 : 0; // 1 si está marcado, 0 si no

            if (empty($nombre)) $errors[] = "El nombre es requerido";
            if (!in_array($tipo, ['servidor', 'db', 'otro'])) $errors[] = "Tipo inválido";
            if (!in_array($estado, ['activo', 'inactivo'])) $errors[] = "Estado inválido";

            if (empty($errors)) {
                $stmt = $pdo->prepare("INSERT INTO recurso (nombre, tipo, estado, id_energia, energia_renovable) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $tipo, $estado, $id_energia, $energia_renovable]);
                $success = "Recurso agregado exitosamente";
            }
        }

        if (isset($_POST['edit'])) {
            $id = filter_input(INPUT_POST, 'id_recurso', FILTER_VALIDATE_INT);
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
            $tipo = $_POST['tipo'];
            $estado = $_POST['estado'];
            $id_energia = filter_input(INPUT_POST, 'id_energia', FILTER_VALIDATE_INT) ?: null;
            $energia_renovable = isset($_POST['energia_renovable']) ? 1 : 0; // 1 si está marcado, 0 si no

            if (!$id) $errors[] = "ID inválido";
            if (empty($nombre)) $errors[] = "El nombre es requerido";
            if (!in_array($tipo, ['servidor', 'db', 'otro'])) $errors[] = "Tipo inválido";
            if (!in_array($estado, ['activo', 'inactivo'])) $errors[] = "Estado inválido";

            if (empty($errors)) {
                $stmt = $pdo->prepare("UPDATE recurso SET nombre = ?, tipo = ?, estado = ?, id_energia = ?, energia_renovable = ? WHERE id_recurso = ?");
                $stmt->execute([$nombre, $tipo, $estado, $id_energia, $energia_renovable, $id]);
                $success = "Recurso actualizado exitosamente";
            }
        }

        if (isset($_POST['delete']) && $is_admin) { // Solo admin puede eliminar
            $id = filter_input(INPUT_POST, 'id_recurso', FILTER_VALIDATE_INT);
            if (!$id) {
                $errors[] = "ID inválido";
            } else {
                $stmt = $pdo->prepare("DELETE FROM recurso WHERE id_recurso = ?");
                $stmt->execute([$id]);
                $success = "Recurso eliminado exitosamente";
            }
        }
    } catch (PDOException $e) {
        $errors[] = "Error en la base de datos: " . $e->getMessage();
    }
}

$recursos = getRecursos($pdo);
$fuentes = getFuentes($pdo);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Recurso-cloudSostenible</title>
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

    <h1>Gestión de Recursos</h1>

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
            <h2>Agregar Recurso</h2>
            <input type="text" name="nombre" placeholder="Nombre" required>
            <select name="tipo" required>
                <option value="servidor">Servidor</option>
                <option value="db">Base de Datos</option>
                <option value="otro">Otro</option>
            </select>
            <select name="estado" required>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
            </select>
            <select name="id_energia">
                <option value="">Sin fuente</option>
                <?php foreach ($fuentes as $fuente): ?>
                    <option value="<?php echo $fuente['id_energia']; ?>"><?php echo htmlspecialchars($fuente['nombre']); ?></option>
                <?php endforeach; ?>
            </select><br/>
            <label><input type="checkbox" name="energia_renovable"> Energía Renovable</label><br/>
            <br/>
            <button type="submit" name="add">Agregar</button>
        </form>
    </div>

    <h2>Listado de Recursos</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Fuente</th>
            <th>Energía Renovable</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($recursos as $recurso): ?>
        <tr>
            <td><?php echo htmlspecialchars($recurso['id_recurso']); ?></td>
            <td><?php echo htmlspecialchars($recurso['nombre']); ?></td>
            <td><?php echo htmlspecialchars($recurso['tipo']); ?></td>
            <td><?php echo htmlspecialchars($recurso['estado']); ?></td>
            <td><?php echo $recurso['fuente_nombre'] ? htmlspecialchars($recurso['fuente_nombre']) : 'Sin fuente'; ?></td>
            <td><?php echo $recurso['energia_renovable'] ? 'Sí' : 'No'; ?></td>
            <td><?php echo htmlspecialchars($recurso['fecha']); ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id_recurso" value="<?php echo $recurso['id_recurso']; ?>">
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($recurso['nombre']); ?>" required>
                    <select name="tipo" required>
                        <option value="servidor" <?php echo $recurso['tipo'] === 'servidor' ? 'selected' : ''; ?>>Servidor</option>
                        <option value="db" <?php echo $recurso['tipo'] === 'db' ? 'selected' : ''; ?>>Base de Datos</option>
                        <option value="otro" <?php echo $recurso['tipo'] === 'otro' ? 'selected' : ''; ?>>Otro</option>
                    </select>
                    <select name="estado" required>
                        <option value="activo" <?php echo $recurso['estado'] === 'activo' ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo $recurso['estado'] === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                    <select name="id_energia">
                        <option value="">Sin fuente</option>
                        <?php foreach ($fuentes as $fuente): ?>
                            <option value="<?php echo $fuente['id_energia']; ?>" <?php echo $recurso['id_energia'] == $fuente['id_energia'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($fuente['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label><input type="checkbox" name="energia_renovable" <?php echo $recurso['energia_renovable'] ? 'checked' : ''; ?>> Energía Renovable</label>
                    <button type="submit" name="edit">Actualizar</button>
                </form>
                <?php if ($is_admin): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id_recurso" value="<?php echo $recurso['id_recurso']; ?>">
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