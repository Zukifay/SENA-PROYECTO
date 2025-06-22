<?php
require_once("conexion_db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["userName"]);
    $correo = trim($_POST["userEmail"]);
    $password = trim($_POST["userPassword"]);

    if (empty($nombre) || empty($correo) || empty($password)) {
        echo "❌ Todos los campos son obligatorios.";
        exit;
    }

    // Verificar si el correo ya existe
    $query_check = "SELECT id FROM usuarios WHERE correo = ?";
    $stmt = mysqli_prepare($conexion, $query_check);
    mysqli_stmt_bind_param($stmt, "s", $correo);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        echo "❌ El correo ya está registrado.";
        exit;
    }
    mysqli_stmt_close($stmt);

    // Insertar nuevo usuario
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $query_insert = "INSERT INTO usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conexion, $query_insert);
    mysqli_stmt_bind_param($stmt, "sss", $nombre, $correo, $passwordHash);

    if (mysqli_stmt_execute($stmt)) {
        echo "Usuario registrado con éxito";
    } else {
        echo "❌ Error al registrar usuario.";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexion);
} else {
    echo "❌ Petición inválida.";
}
?>

