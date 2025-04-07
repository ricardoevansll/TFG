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
    } catch (PDOException $e) {
        $errors[] = "Error en la base de datos: " . $e->getMessage();
    }
}

$alertas = getAlertas($pdo);
$usuarios = getUsuarios($pdo);
$recursos = getRecursos($pdo);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Alertas-cloudSostenible</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/alerts.css">
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
            </select><br/>
            <input type="number" step="0.01" name="umbral_pue" placeholder="Umbral PUE" required>
            <input type="number" step="0.01" name="umbral_carbono" placeholder="Umbral Carbono" required><br/>
            <br/>
            <button type="submit" name="add">Agregar</button>
        </form>
    </div>

    <div class="search-container">
        <input type="text" id="search" class="search-bar" placeholder="Buscar por usuario o recurso...">
    </div>
    <div class="card-container" id="alerts-list">
        <?php foreach ($alertas as $alerta): ?>
            <div class="card" data-id="<?php echo $alerta['id_alerta']; ?>">
                <div class="info">
                    <h3><?php echo htmlspecialchars($alerta['usuario_nombre']); ?></h3>
                    <p><i class="fas fa-server"></i> Recurso: <?php echo htmlspecialchars($alerta['recurso_nombre']); ?></p>
                    <p><i class="fas fa-tachometer-alt"></i> Umbral PUE: <?php echo htmlspecialchars($alerta['umbral_pue']); ?></p>
                    <p><i class="fas fa-leaf"></i> Umbral Carbono: <?php echo htmlspecialchars($alerta['umbral_carbono']); ?></p>
                    <p><i class="fas fa-clock"></i> Fecha: <?php echo htmlspecialchars($alerta['fecha']); ?></p>
                </div>
                <form class="edit-form" method="POST" action="update_alerts.php">
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
                    <button type="submit" class="save-btn">Guardar</button>
                </form>
                <div class="actions">
                    <button class="edit-btn"><i class="fas fa-edit"></i> Editar</button>
                    <?php if ($is_admin): ?>
                        <button class="delete-btn" data-id="<?php echo $alerta['id_alerta']; ?>"><i class="fas fa-trash"></i> Eliminar</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div><br/>
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
    <script src="js/alerts.js"></script>
</body>
</html>