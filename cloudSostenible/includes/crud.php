<?php
$conn = new mysqli("localhost", "root", "", "sostenible");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
$sql = "SELECT * FROM recurso";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    echo "<p>" . $row["nombre"] . " - " . $row["tipo"] . "</p>";
}
$conn->close();
?>