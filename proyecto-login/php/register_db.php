<?php
include "conexion_db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userName = $_POST["userName"];
    $userEmail = $_POST["userEmail"];
    $userPassword = password_hash($_POST["userPassword"], PASSWORD_DEFAULT);

    // Paso 1: Verificar si el correo ya existe
    $checkQuery = "SELECT * FROM usuarios WHERE email = '$userEmail'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "⚠️ El correo ya está registrado. Intenta con otro.";
    } else {
        // Insertar el nuevo usuario
        $query = "INSERT INTO usuarios(nombre_usuario, email, clave)
                  VALUES ('$userName', '$userEmail', '$userPassword')";

        $run = mysqli_query($conn, $query);

        if ($run) {
            echo "✅ Usuario registrado con éxito.";
        } else {
            echo "❌ Error al registrar: " . mysqli_error($conn);
        }
    }
}
?>

