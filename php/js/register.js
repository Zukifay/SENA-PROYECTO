const formRegister = document.querySelector(".form-register");
const inputUser = document.querySelector(".form-register input[name='userName']");
const inputEmail = document.querySelector(".form-register input[name='userEmail']");
const inputPass = document.querySelector(".form-register input[name='userPassword']");

const alertaError = document.querySelector(".alerta-error");
const alertaExito = document.querySelector(".alerta-exito");

const userNameRegex = /^(?![\s\d]*$)[a-zA-Z0-9\s\-]{4,16}$/;
const emailRegex = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9.]+$/;
const passwordRegex = /^.{4,12}$/;

const captchaInput = document.querySelector("#captcha-input");
const codigoCaptcha = document.querySelector("#codigoCaptcha");
let captchaGenerado = "";


const estadoValidacionCampos = {
    userName: false,
    userEmail: false,
    userPassword: false,
};
function generarCaptcha() {
    const caracteres = "ABCDEFGHJKLMNPQRSTUVWXYZ123456789";
    captchaGenerado = "";
    for (let i = 0; i < 5; i++) {
        captchaGenerado += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
    }
    codigoCaptcha.textContent = "Código: " + captchaGenerado;
}       

function enviarFormulario() {
    // Limpiar espacios
    inputUser.value = inputUser.value.trim();
    inputEmail.value = inputEmail.value.trim();
    inputPass.value = inputPass.value.trim();
    captchaInput.value = captchaInput.value.trim();

    // Validar campos y captcha
    if (
        estadoValidacionCampos.userName &&
        estadoValidacionCampos.userEmail &&
        estadoValidacionCampos.userPassword
    ) {
        if (captchaInput.value !== captchaGenerado) {
            alertaError.textContent = "❌ Código captcha incorrecto.";
            alertaError.classList.add("alertaError");
            setTimeout(() => {
                alertaError.classList.remove("alertaError");
                captchaInput.value = ""; // limpia el campo
                captchaInput.focus(); // enfoca para reintentar
                generarCaptcha(); // genera nuevo código
            }, 2000);
            return;
        }

        // Si todo está validado correctamente, enviar por fetch
        const formData = new FormData(formRegister);

        fetch("php/verificar_correo.php", {
            method: "POST",
            body: formData,
        })
        .then(res => res.text())
        .then(respuesta => {
            if (respuesta.includes("Usuario registrado con éxito")) {
                alertaExito.classList.add("alertaExito");
                alertaError.classList.remove("alertaError");
                formRegister.reset();
                captchaInput.value = "";
                Object.keys(estadoValidacionCampos).forEach(key => estadoValidacionCampos[key] = false);
                generarCaptcha(); // por si el usuario registra otro

                setTimeout(() => {
                    alertaExito.classList.remove("alertaExito");
                    window.location.href = "login.php";
                }, 2500);
            } else {
                alertaExito.classList.remove("alertaExito");
                alertaError.textContent = respuesta;
                alertaError.classList.add("alertaError");
                setTimeout(() => {
                    alertaError.classList.remove("alertaError");
                }, 3000);
            }
        })
        .catch(error => {
            console.error("Error en el registro:", error);
            alertaExito.classList.remove("alertaExito");
            alertaError.textContent = "❌ Error inesperado en el servidor.";
            alertaError.classList.add("alertaError");
            setTimeout(() => {
                alertaError.classList.remove("alertaError");
            }, 3000);
        });
    } else {
        alertaExito.classList.remove("alertaExito");
        alertaError.textContent = "⚠️ Verifica que todos los campos estén correctos.";
        alertaError.classList.add("alertaError");
        setTimeout(() => {
            alertaError.classList.remove("alertaError");
        }, 3000);
    }
}
    
    document.addEventListener("DOMContentLoaded", () => {
    formRegister.addEventListener("submit", e => {
        e.preventDefault(); // ← necesario para que no recargue
        inputUser.value = inputUser.value.trim(); // elimina espacios antes y después
        enviarFormulario();
    });

    generarCaptcha(); // llama al cargar
        codigoCaptcha.addEventListener("click", generarCaptcha);
        codigoCaptcha.style.cursor = "pointer";

    inputUser.addEventListener("input", () => {
        // Normaliza espacios
        inputUser.value = inputUser.value
            .replace(/\s{2,}/g, " ") // reemplaza espacios dobles por uno
            .trimStart(); // elimina espacios al inicio (no al final aún por UX)
    
        validarCampo(
            userNameRegex,
            inputUser,
            "El nombre debe tener entre 4 y 40 caracteres, y contener letras. No puede ser solo espacios o solo números."
        );
    });
      

    inputEmail.addEventListener("input", () => {
        inputEmail.value = inputEmail.value.trimStart(); // elimina espacios al inicio
        validarCampo(
            emailRegex,
            inputEmail,
            "Correo inválido."
        );
    });
    
    inputPass.addEventListener("input", () => {
        inputPass.value = inputPass.value.trimStart(); // elimina espacios al inicio
        validarCampo(
            passwordRegex,
            inputPass,
            "La contraseña debe tener entre 4 y 12 caracteres."
        );
        
        
    });
});
    function validarCampo(regex, campo, mensaje) {
        const esValido = regex.test(campo.value);
        if (esValido) {
            eliminarAlerta(campo.parentElement.parentElement);
            estadoValidacionCampos[campo.name] = true;
            campo.parentElement.classList.remove("error");
        } else {
            estadoValidacionCampos[campo.name] = false;
            mostrarAlerta(campo.parentElement.parentElement, mensaje);
            campo.parentElement.classList.add("error");
        }
    }

    function mostrarAlerta(referencia, mensaje) {
        eliminarAlerta(referencia);
        const alertaDiv = document.createElement("div");
        alertaDiv.classList.add("alerta");
        alertaDiv.textContent = mensaje;
        referencia.appendChild(alertaDiv);
    }

    function eliminarAlerta(referencia) {
        const alerta = referencia.querySelector(".alerta");
        if (alerta) {
            alerta.remove();
        }
    }

    inputEmail.addEventListener("blur", async () => {
        const email = inputEmail.value.trim();
        if (!emailRegex.test(email)) return; // Solo consulta si el formato es válido
    
        try {
            const res = await fetch("php/register_db.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "email=" + encodeURIComponent(email)
            });
    
            const data = await res.json();
    
            if (data.existe) {
                estadoValidacionCampos["userEmail"] = false;
                mostrarAlerta(inputEmail.parentElement.parentElement, "Este correo ya está registrado.");
                inputEmail.parentElement.classList.add("error");
            }
        } catch (error) {
            console.error("Error al verificar el correo:", error);
        }
        
    });
    