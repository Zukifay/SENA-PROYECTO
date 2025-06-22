<?php
header('Content-Type: application/json');

require_once("conexion_db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"])) {
    $email = mysqli_real_escape_string($conexion, $_POST["email"]);

    $consulta = "SELECT * FROM usuarios WHERE correo = '$email'";
    $resultado = mysqli_query($conexion, $consulta);

    if (!$resultado) {
        echo json_encode(["error" => "Error en la consulta: " . mysqli_error($conexion)]);
        exit;
    }

    if (mysqli_num_rows($resultado) > 0) {
        echo json_encode(["existe" => true]);
    } else {
        echo json_encode(["existe" => false]);
    }
} else {
    echo json_encode(["error" => "Petición inválida."]);
}
?>


