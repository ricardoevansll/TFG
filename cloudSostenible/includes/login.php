<?php
session_start();
$conn = new mysqli("localhost", "revans", "", "sostenible");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $sql = "SELECT * FROM usuario WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $_SESSION["user"] = $email;
        header("Location: dashboard.html");
    } else {
        echo "<script>alert('Usuario o contraseña incorrectos');</script>";
    }
}
$conn->close();
?>
