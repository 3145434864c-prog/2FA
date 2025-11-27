<?php
// 2fa.php – Vista para ingresar OTP
// Se asume que la sesión contiene: $_SESSION['2fa_user_id']
// Si no existe, redirigir al login para evitar acceso directo.

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
/* =========================
   Fondo general
   ========================= */
body {
  margin: 0;
  padding: 0;
  font-family: "Arial", sans-serif;
  background: linear-gradient(0deg, #070707 0%, #253447 100%);
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

/* =========================
   Contenedor principal 2FA
   ========================= */
.otp-container {
  max-width: 450px;
  width: 100%;
  background: linear-gradient(0deg, #FFFFFF 0%, #F4F7FB 100%);
  border-radius: 40px;
  padding: 30px 40px;
  border: 5px solid #FFFFFF;
  box-shadow: rgba(16, 64, 87, 0.88) 0px 30px 30px -20px;
  margin: 20px;
  text-align: center;
  animation: fadeInUp 0.6s ease;
}

@keyframes fadeInUp {
  0% { opacity: 0; transform: translateY(30px); }
  100% { opacity: 1; transform: translateY(0); }
}

/* =========================
   Título
   ========================= */
.otp-title {
  font-weight: 900;
  font-size: 28px;
  color: rgb(16, 137, 211); 
}

/* =========================
   Contenedor de inputs OTP
   ========================= */
.otp-inputs {
  display: flex;
  justify-content: space-between;
  margin: 30px 0;
}

.otp-input {
  width: 60px;
  height: 68px;
  font-size: 28px;
  text-align: center;
  border: none;
  border-radius: 20px;
  background: white;
  box-shadow: #1788bdff 0px 10px 10px -5px;
  transition: all 0.2s ease;
  border-inline: 2px solid transparent;
}

.otp-input:focus {
  outline: none;
  border-inline: 2px solid #12B1D1;
  transform: scale(1.06);
}

/* =========================
   Botón verificar
   ========================= */
.btn-otp {
  display: block;
  width: 100%;
  font-weight: bold;
  background: linear-gradient(45deg, rgb(16, 137, 211) 0%, rgb(18, 177, 209) 100%);
  color: white;
  padding-block: 15px;
  margin-top: 10px;
  border-radius: 20px;
  border: none;
  cursor: pointer;
  box-shadow: rgba(133, 189, 215, 0.88) 0px 20px 10px -15px;
  transition: all 0.2s ease-in-out;
}

.btn-otp:hover {
  transform: scale(1.03);
  box-shadow: rgba(133, 189, 215, 0.88) 0px 23px 10px -20px;
}

.btn-otp:active {
  transform: scale(0.95);
  box-shadow: rgba(133, 189, 215, 0.88) 0px 15px 10px -10px;
}

/* =========================
   Reenviar código
   ========================= */
.resend {
  margin-top: 15px;
  color: #0099ff;
  font-size: 14px;
  cursor: pointer;
  transition: color .3s ease;
}

.resend:hover {
  color: rgb(16, 137, 211);
}

/* =========================
   Timer
   ========================= */
.timer {
  margin-top: 15px;
  font-size: 15px;
  color: #444;
  font-weight: bold;
}

/* =========================
   Responsivo
   ========================= */
@media (max-width: 480px) {
  .otp-container {
    width: 90%;
    padding: 25px 25px;
  }

  .otp-input {
    width: 50px;
    height: 58px;
    font-size: 24px;
  }

  .otp-title {
    font-size: 24px;
  }
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
