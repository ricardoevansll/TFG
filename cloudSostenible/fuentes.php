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
    } catch (PDOException $e) {
        $errors[] = "Error en la base de datos: " . $e->getMessage();
    }
}

$fuentes = getFuentes($pdo);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Fuentes-cloudSostenible</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/sources.css">
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

    <h1>Gestión de Fuentes de Energía</h1>

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
            <h2>Agregar Fuente de Energía</h2>
            <input type="text" name="nombre" placeholder="Nombre" required><br/>
            <label><input type="checkbox" name="renovable"> Renovable</label><br/>
            <br/>
            <button type="submit" name="add">Agregar</button>
        </form>
    </div>

    <div class="search-container">
        <input type="text" id="search" class="search-bar" placeholder="Buscar por nombre...">
    </div>
    <div class="card-container" id="sources-list">
        <?php foreach ($fuentes as $fuente): ?>
            <div class="card <?php echo $fuente['renovable'] ? 'renovable' : 'no-renovable'; ?>" data-id="<?php echo $fuente['id_energia']; ?>">
                <div class="info">
                    <h3><?php echo htmlspecialchars($fuente['nombre']); ?></h3>
                    <p><i class="fas fa-leaf"></i> Renovable: <?php echo $fuente['renovable'] ? 'Sí' : 'No'; ?></p>
                </div>
                <form class="edit-form" method="POST" action="update_sources.php">
                    <input type="hidden" name="id_energia" value="<?php echo $fuente['id_energia']; ?>">
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($fuente['nombre']); ?>" required>
                    <label><input type="checkbox" name="renovable" <?php echo $fuente['renovable'] ? 'checked' : ''; ?>> Renovable</label>
                    <button type="submit" class="save-btn">Guardar</button>
                </form>
                <div class="actions">
                    <button class="edit-btn"><i class="fas fa-edit"></i> Editar</button>
                    <?php if ($is_admin): ?>
                        <button class="delete-btn" data-id="<?php echo $fuente['id_energia']; ?>"><i class="fas fa-trash"></i> Eliminar</button>
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
    <script src="js/sources.js"></script>
</body>
</html>