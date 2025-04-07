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
            $energia_renovable = isset($_POST['energia_renovable']) ? 1 : 0;

            if (empty($nombre)) $errors[] = "El nombre es requerido";
            if (!in_array($tipo, ['servidor', 'db', 'otro'])) $errors[] = "Tipo inválido";
            if (!in_array($estado, ['activo', 'inactivo'])) $errors[] = "Estado inválido";

            if (empty($errors)) {
                $stmt = $pdo->prepare("INSERT INTO recurso (nombre, tipo, estado, id_energia, energia_renovable) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $tipo, $estado, $id_energia, $energia_renovable]);
                $success = "Recurso agregado exitosamente";
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
<html lang="es">
<head>
    <title>Recurso-cloudSostenible</title>
    <link rel="stylesheet" href="css/resources.css">
    <link rel="stylesheet" href="css/styles.css">
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
    </div><br/>

    <div class="search-container">
        <input type="text" id="search" class="search-bar" placeholder="Buscar por nombre o tipo de recurso...">
    </div>
    <div class="card-container" id="resource-list">
        <?php foreach ($recursos as $recurso): ?>
            <div class="card <?php echo $recurso['estado'] === 'activo' ? 'estado-activo' : 'estado-inactivo'; ?>" data-id="<?php echo $recurso['id_recurso']; ?>">
                <div class="info">
                    <h3><?php echo htmlspecialchars($recurso['nombre']); ?></h3>
                    <p><i class="fas fa-server"></i> Tipo: <?php echo htmlspecialchars($recurso['tipo']); ?></p>
                    <p><i class="fas fa-toggle-<?php echo $recurso['estado'] === 'activo' ? 'on' : 'off'; ?>"></i> Estado: <?php echo htmlspecialchars($recurso['estado']); ?></p>
                    <p><i class="fas fa-plug"></i> Fuente: <?php echo $recurso['fuente_nombre'] ? htmlspecialchars($recurso['fuente_nombre']) : 'Sin fuente'; ?></p>
                    <p><i class="fas fa-leaf"></i> Renovable: <?php echo $recurso['energia_renovable'] ? 'Sí' : 'No'; ?></p>
                    <p><i class="fas fa-clock"></i> Fecha: <?php echo htmlspecialchars($recurso['fecha']); ?></p>
                </div>
                <form class="edit-form" method="POST" action="update_resource.php">
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
                    <button type="submit" class="save-btn">Guardar</button>
                </form>
                <div class="actions">
                    <button class="edit-btn"><i class="fas fa-edit"></i> Editar</button>
                    <?php if ($is_admin): ?>
                        <button class="delete-btn" data-id="<?php echo $recurso['id_recurso']; ?>"><i class="fas fa-trash"></i> Eliminar</button>
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
    <script src="js/resources.js"></script>
</body>
</html>