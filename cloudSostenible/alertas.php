<?php
require_once 'auth.php';
require_once 'db_connect.php';

function getAlertas($pdo) {
    $stmt = $pdo->query("SELECT a.*, u.nombre AS usuario_nombre, r.nombre AS recurso_nombre 
                         FROM alertas a 
                         JOIN usuario u ON a.id_usuario = u.iduser 
                         JOIN recurso r ON a.id_recurso = r.id_recurso");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUsuarios($pdo) {
    $stmt = $pdo->query("SELECT iduser, nombre FROM usuario");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRecursos($pdo) {
    $stmt = $pdo->query("SELECT id_recurso, nombre FROM recurso");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$errors = [];
$success = '';
$is_admin = $_SESSION['rol'] === 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add'])) {
            $id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
            $id_recurso = filter_input(INPUT_POST, 'id_recurso', FILTER_VALIDATE_INT);
            $umbral_pue = filter_input(INPUT_POST, 'umbral_pue', FILTER_VALIDATE_FLOAT);
            $umbral_carbono = filter_input(INPUT_POST, 'umbral_carbono', FILTER_VALIDATE_FLOAT);

            if (!$id_usuario) $errors[] = "Usuario inválido";
            if (!$id_recurso) $errors[] = "Recurso inválido";
            if ($umbral_pue === false || $umbral_pue < 0) $errors[] = "Umbral PUE inválido";
            if ($umbral_carbono === false || $umbral_carbono < 0) $errors[] = "Umbral de carbono inválido";

            if (empty($errors)) {
                $stmt = $pdo->prepare("INSERT INTO alertas (id_usuario, id_recurso, umbral_pue, umbral_carbono) VALUES (?, ?, ?, ?)");
                $stmt->execute([$id_usuario, $id_recurso, $umbral_pue, $umbral_carbono]);
                $success = "Alerta agregada exitosamente";
            }
        }

        if (isset($_POST['edit'])) {
            $id = filter_input(INPUT_POST, 'id_alerta', FILTER_VALIDATE_INT);
            $id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
            $id_recurso = filter_input(INPUT_POST, 'id_recurso', FILTER_VALIDATE_INT);
            $umbral_pue = filter_input(INPUT_POST, 'umbral_pue', FILTER_VALIDATE_FLOAT);
            $umbral_carbono = filter_input(INPUT_POST, 'umbral_carbono', FILTER_VALIDATE_FLOAT);

            if (!$id) $errors[] = "ID inválido";
            if (!$id_usuario) $errors[] = "Usuario inválido";
            if (!$id_recurso) $errors[] = "Recurso inválido";
            if ($umbral_pue === false || $umbral_pue < 0) $errors[] = "Umbral PUE inválido";
            if ($umbral_carbono === false || $umbral_carbono < 0) $errors[] = "Umbral de carbono inválido";

            if (empty($errors)) {
                $stmt = $pdo->prepare("UPDATE alertas SET id_usuario = ?, id_recurso = ?, umbral_pue = ?, umbral_carbono = ? WHERE id_alerta = ?");
                $stmt->execute([$id_usuario, $id_recurso, $umbral_pue, $umbral_carbono, $id]);
                $success = "Alerta actualizada exitosamente";
            }
        }

        if (isset($_POST['delete']) && $is_admin) { // Solo admin puede eliminar
            $id = filter_input(INPUT_POST, 'id_alerta', FILTER_VALIDATE_INT);
            if (!$id) {
                $errors[] = "ID inválido";
            } else {
                $stmt = $pdo->prepare("DELETE FROM alertas WHERE id_alerta = ?");
                $stmt->execute([$id]);
                $success = "Alerta eliminada exitosamente";
            }
        }
    } catch (PDOException $e) {
        $errors[] = "Error en la base de datos: " . $e->getMessage();
    }
}

$alertas = getAlertas($pdo);
$usuarios = getUsuarios($pdo);
$recursos = getRecursos($pdo);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Alertas-cloudSostenible</title>
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

    <h1>Gestión de Alertas</h1>

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
            <h2>Agregar Alerta</h2>
            <select name="id_usuario" required>
                <option value="">Seleccione usuario</option>
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?php echo $usuario['iduser']; ?>"><?php echo htmlspecialchars($usuario['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="id_recurso" required>
                <option value="">Seleccione recurso</option>
                <?php foreach ($recursos as $recurso): ?>
                    <option value="<?php echo $recurso['id_recurso']; ?>"><?php echo htmlspecialchars($recurso['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" step="0.01" name="umbral_pue" placeholder="Umbral PUE" required>
            <input type="number" step="0.01" name="umbral_carbono" placeholder="Umbral Carbono" required><br/>
            <br/>
            <button type="submit" name="add">Agregar</button>
        </form>
    </div>

    <h2>Listado de Alertas</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Recurso</th>
            <th>Umbral PUE</th>
            <th>Umbral Carbono</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($alertas as $alerta): ?>
        <tr>
            <td><?php echo htmlspecialchars($alerta['id_alerta']); ?></td>
            <td><?php echo htmlspecialchars($alerta['usuario_nombre']); ?></td>
            <td><?php echo htmlspecialchars($alerta['recurso_nombre']); ?></td>
            <td><?php echo htmlspecialchars($alerta['umbral_pue']); ?></td>
            <td><?php echo htmlspecialchars($alerta['umbral_carbono']); ?></td>
            <td><?php echo htmlspecialchars($alerta['fecha']); ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id_alerta" value="<?php echo $alerta['id_alerta']; ?>">
                    <select name="id_usuario" required>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?php echo $usuario['iduser']; ?>" <?php echo $alerta['id_usuario'] == $usuario['iduser'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($usuario['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="id_recurso" required>
                        <?php foreach ($recursos as $recurso): ?>
                            <option value="<?php echo $recurso['id_recurso']; ?>" <?php echo $alerta['id_recurso'] == $recurso['id_recurso'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($recurso['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" step="0.01" name="umbral_pue" value="<?php echo htmlspecialchars($alerta['umbral_pue']); ?>" required>
                    <input type="number" step="0.01" name="umbral_carbono" value="<?php echo htmlspecialchars($alerta['umbral_carbono']); ?>" required>
                    <button type="submit" name="edit">Actualizar</button>
                </form>
                <?php if ($is_admin): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id_alerta" value="<?php echo $alerta['id_alerta']; ?>">
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