<?php
session_start();
require_once("..php/conexion_db.php");

$mensajeLogin = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["userEmail"], $_POST["userPassword"])) {
    $email = trim($_POST["userEmail"]);
    $password = trim($_POST["userPassword"]);

    $query = "SELECT id, contrasena FROM usuarios WHERE correo = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) === 1) {
        mysqli_stmt_bind_result($stmt, $userId, $hashedPassword);
        mysqli_stmt_fetch($stmt);

        if (password_verify($password, $hashedPassword)) {
            $_SESSION["user_id"] = $userId;
            header("Location: dashboard.php");
            exit;
        } else {
            $mensajeLogin = "❌ Contraseña incorrecta.";
        }
    } else {
        $mensajeLogin = "❌ Correo no encontrado.";
    }

    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../estilos_login.css">
    <title>Rafastore H.A - Inicio Sesion</title>
</head>
<body>
    <div class="container-form register">
        <div class="information">
            <div class="info-childs">
                <h2>Bienvenido a Rafastore H.A</h2>
                <p>Para unirte a nuestra comunidad por favor Iniciar Sesión</p>
                <input type="button" value="Iniciar Sesión" id="sign-in">
            </div>
        </div>
        <div class="form-information">
            <div class="form-information-childs">
                <h2>Crear una Cuenta Con</h2>
                <div class="icons">
                    <i class='bx bxl-google'></i>
                </div>
                <p>Sino usar tu correo electronico para registrarte</p>
                <form method="POST" action="../register_db.php" class="form form-register" id="formRegister" novalidate>
                    <div>
                        <label>
                            <i class='bx bx-user' ></i>
                            <input type="text" placeholder="Nombre Usuario" name="userName" >
                        </label>
                    </div>
                    <div>
                        <label >
                            <i class='bx bx-envelope' ></i>
                            <input type="email" placeholder="Correo Electronico" name="userEmail" >
                        </label>
                    </div>
                   <div>
                        <label>
                            <i class='bx bx-lock-alt' ></i>
                            <input type="password" placeholder="Contraseña" name="userPassword">
                        </label>
                   </div>
                   <div>
                <label>
                    <i class='bx bx-check-shield'></i>
                    <input type="text" id="captcha-input" name="captchaInput" placeholder="Ingresa el código" required>
                </label>
                <div id="codigoCaptcha" style="margin-top: 5px; font-weight: bold; font-size: 14px; color: #333;"></div>
            </div>
                    <input type="submit" value="Registrarse">
                    <div class="alerta-error">Todos los campos son obligatorios</div>
                    <div class="alerta-exito">Te registraste Correctamente</div>
                </form>
            </div>
        </div>
    </div>


    <div class="container-form login hide">
        <div class="information">
            <div class="info-childs">
                <h2>Quieres saber mas?</h2>
                <p>Para unirte a nuestra comunidad por favor registrate</p>
                <input type="button" value="Registrarse" id="sign-up">
            </div>
        </div>
        <div class="form-information">
            <div class="form-information-childs">
                <h2>Iniciar Sesión Con</h2>
                <div class="icons">
                    <i class='bx bxl-google'></i>
                </div>
                <p>Si ya estas registrado iniciar sesión</p>
                <form class="form form-login" method="POST" action="login.php" novalidate>
                    <div>
                        <label >
                            <i class='bx bx-envelope' ></i>
                            <input type="email" placeholder="Correo Electronico" name="userEmail">
                        </label>
                    </div>
                    <div>
                        <label>
                            <i class='bx bx-lock-alt' ></i>
                            <input type="password" placeholder="Contraseña" name="userPassword">
                        </label>
                    </div>
                    <input type="submit" value="Iniciar Sesión">
                    <div class="alerta-error">Todos los campos son obligatorios</div>
                    <div class="alerta-exito">Iniciaste Sesion Correctamente</div>
                    <?php if (!empty($mensajeLogin)): ?>
                    <div class="alerta-error" style="display:block;"><?= $mensajeLogin ?></div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <script src="js/script.js"></script> 
    <script src="js/register.js"></script>
    <script src="js/iffe_login.js"></script>
    <script src="js/login_modulo.js"></script>


</body>
</html>