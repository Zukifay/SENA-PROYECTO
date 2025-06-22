<?php
include "conexion_db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    $query = "SELECT * FROM usuarios WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo json_encode(["existe" => true]);
    } else {
        echo json_encode(["existe" => false]);
    }
}
?>
