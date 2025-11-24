<?php
// 2fa.php – Vista para ingresar OTP
// Se asume que la sesión contiene: $_SESSION['2fa_user_id']
// Si no existe, redirigir al login para evitar acceso directo.

session_start();
if (!isset($_SESSION['2fa_user_id'])) {
    header("Location: login");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificación en dos pasos</title>

    <!-- SWEETALERT 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background: #f4f6f9;
            font-family: Arial, sans-serif;
        }
        .otp-container {
            max-width: 420px;
            margin: 90px auto;
            padding: 35px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.1);
            text-align: center;
        }
        .otp-title {
            font-size: 22px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .otp-inputs {
            display: flex;
            justify-content: space-between;
            margin: 25px 0;
        }
        .otp-input {
            width: 48px;
            height: 55px;
            font-size: 26px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .btn-otp {
            background: #34495e;
            border: none;
            width: 100%;
            padding: 14px;
            color: white;
            font-size: 17px;
            cursor: pointer;
            border-radius: 8px;
            margin-top: 20px;
        }
        .btn-otp:hover {
            background: #2c3e50;
        }
        .resend {
            margin-top: 15px;
            color: #2980b9;
            cursor: pointer;
            font-size: 14px;
        }
        .timer {
            margin-top: 10px;
            font-size: 15px;
            color: #555;
        }
    </style>

</head>
<body>

<div class="otp-container">

    <div class="otp-title">Verificación en dos pasos</div>
    <div>Ingresa el código que enviamos a tu correo.</div>

    <!-- FORMULARIO -->
    <form id="formOtp" method="POST" action="index.php?route=2fa&action=verificar">

        <div class="otp-inputs">
            <input maxlength="1" class="otp-input" name="otp[]" />
            <input maxlength="1" class="otp-input" name="otp[]" />
            <input maxlength="1" class="otp-input" name="otp[]" />
            <input maxlength="1" class="otp-input" name="otp[]" />
            <input maxlength="1" class="otp-input" name="otp[]" />
            <input maxlength="1" class="otp-input" name="otp[]" />
        </div>

        <button class="btn-otp" type="submit">Verificar código</button>

        <div class="timer">
            Código expira en: <span id="countdown">05:00</span>
        </div>

        <div class="resend" id="resendBtn">Reenviar código</div>
        <div class="resend" id="resendWait" style="display:none;">
            Espera <span id="wait">60</span>s para reenviar
        </div>

    </form>

</div>

<script>
// ===============================
// Autoadvance para los inputs
// ===============================
const inputs = document.querySelectorAll(".otp-input");
inputs.forEach((input, idx) => {
    input.addEventListener("keyup", e => {
        if (e.key >= 0 && e.key <= 9) {
            if (idx < inputs.length - 1) inputs[idx+1].focus();
        } else if (e.key === "Backspace") {
            if (idx > 0) inputs[idx-1].focus();
        }
    });
});
inputs[0].focus();

// ===============================
// Timer regresivo 5 minutos
// ===============================
let remaining = 300;
setInterval(() => {
    let m = Math.floor(remaining / 60);
    let s = remaining % 60;

    document.getElementById("countdown").innerText =
        `${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;

    if (remaining > 0) remaining--;
}, 1000);

// ===============================
// Reenvío seguro
// ===============================
document.getElementById("resendBtn").onclick = function() {
    Swal.fire({
        title: "Reenviar código",
        text: "¿Deseas reenviar el código OTP?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, reenviar",
    }).then(res => {
        if (res.isConfirmed) {
            window.location = "index.php?pagina=reenviar-otp";
        }
    });
};

// Cooldown de 60 segundos
let wait = 60;
document.getElementById("resendBtn").style.display = "none";
document.getElementById("resendWait").style.display = "block";

let timer = setInterval(() => {
    if (wait <= 0) {
        clearInterval(timer);
        document.getElementById("resendBtn").style.display = "block";
        document.getElementById("resendWait").style.display = "none";
    } else {
        document.getElementById("wait").innerText = wait;
        wait--;
    }
}, 1000);
</script>

</body>
</html>
