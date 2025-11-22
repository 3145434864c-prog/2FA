<?php
require_once "Modelos/ModeloUsuarios.php";

class ControladorUsuarios {

// =======================================
// Crear usuario con perfil
// =======================================
public function crearUsuario(): array {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $foto = $_FILES['foto'] ?? null;
    $password = $_POST['password'] ?? '';  // Tomamos la contraseña del formulario
    $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 0;

    // **Nuevo:** validar perfil
    $perfilesValidos = ['administrador', 'editor', 'usuario'];
    $perfil = $_POST['perfil'] ?? 'usuario';
    if (!in_array($perfil, $perfilesValidos)) {
        $perfil = 'usuario'; // seguridad: si no es válido, por defecto usuario
    }

    // Validar campos obligatorios
    if ($nombre === '' || $email === '' || $password === '') {
        return ['danger' => true, 'mensaje' => 'Nombre, correo y contraseña son obligatorios'];
    }

    // Validación de la contraseña
    if (!$this->validarContraseña($password)) {
        return ['danger' => true, 'mensaje' => 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial'];
    }

    // Procesar foto si se subió
    $rutaFoto = '';
    if ($foto && $foto['tmp_name']) {
        $nombreArchivo = uniqid() . '_' . basename($foto['name']);
        $rutaFoto = 'vistas/recursos/img/usuarios/' . $nombreArchivo;
        move_uploaded_file($foto['tmp_name'], $rutaFoto);
    }

    // Hash de la contraseña utilizando Argon2id
    $passwordHash = password_hash($password, PASSWORD_ARGON2ID, [
    'memory_cost' => 1 << 17, // 128 MB
    'time_cost'   => 4,
    'threads'     => 2
]);


    // Crear usuario en la base de datos
    $resultado = ModeloUsuarios::crear([
    'nombre_usuario' => $nombre,
    'email_usuario' => $email,
    'password_usuario' => $passwordHash,
    'foto_usuario' => $rutaFoto,
    'estado_usuario' => $estado,
    'perfil_usuario' => $perfil
]);


    if ($resultado) {
        return ['success' => true, 'mensaje' => 'Usuario creado correctamente'];
    } else {
        return ['danger' => true, 'mensaje' => 'Error al crear usuario'];
    }
}



// Función para validar la contraseña
private function validarContraseña($contraseña): bool {
    // Validación de contraseña con una expresión regular
    $patron = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    return preg_match($patron, $contraseña);
}


    // =======================================
    // Editar usuario
    // =======================================
   public function editarUsuario(): array {
    $id = $_POST['id_usuario'] ?? null;
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $foto = $_FILES['foto'] ?? null;
    $password = trim($_POST['password'] ?? '');
    $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 1;
    $perfil = $_POST['perfil'] ?? 'usuario';


    if (!$id || $nombre === '' || $email === '') {
        return ['success' => false, 'mensaje' => 'Todos los campos son obligatorios'];
    }

    // Obtener datos actuales del usuario
    $usuarioActual = ModeloUsuarios::findById($id);
    if (!$usuarioActual) {
        return ['success' => false, 'mensaje' => 'Usuario no encontrado'];
    }

    // Procesar foto si se sube una nueva
    $rutaFoto = $usuarioActual['foto_usuario'] ?? '';
    if ($foto && $foto['tmp_name']) {
        $nombreArchivo = uniqid() . '_' . basename($foto['name']);
        $rutaFoto = 'vistas/recursos/img/usuarios/' . $nombreArchivo;
        move_uploaded_file($foto['tmp_name'], $rutaFoto);
    }

    // Preparar arreglo con los datos a actualizar
   $datosActualizar = [
    'nombre_usuario' => $nombre,
    'email_usuario' => $email,
    'foto_usuario' => $rutaFoto,
    'estado_usuario' => $estado,
    'perfil_usuario' => $perfil
];

if ($password !== '') {

    // Validar contraseña antes de actualizar
    if (!$this->validarContraseña($password)) {
        return [
            'success' => false,
            'mensaje' => 'La contraseña debe tener mínimo 8 caracteres, una letra mayúscula, una minúscula, un número y un carácter especial.'
        ];
    }

    // Si es válida, generar el hash
    $datosActualizar['password_usuario'] = password_hash($password, PASSWORD_ARGON2ID, [
        'memory_cost' => 1 << 17,
        'time_cost'   => 4,
        'threads'     => 2
    ]);
}



    // Enviar datos al modelo
    $resultado = ModeloUsuarios::actualizar($id, $datosActualizar);

    if ($resultado) {
        return ['success' => true, 'mensaje' => 'Usuario editado correctamente'];
    } else {
        return ['success' => false, 'mensaje' => 'Error al editar usuario'];
    }
}


    // =======================================
    // Eliminar usuario
    // =======================================
    public function eliminarUsuario(): array {
        $id = $_POST['id_usuario'] ?? null;
        if (!$id) {
            return ['success' => false, 'mensaje' => 'ID de usuario no válido'];
        }

        $resultado = ModeloUsuarios::eliminar($id);
        if ($resultado) {
            return ['success' => true, 'mensaje' => 'Usuario eliminado correctamente'];
        } else {
            return ['success' => false, 'mensaje' => 'Error al eliminar usuario'];
        }
    }

    // =======================================
    // Listar todos los usuarios
    // =======================================
    public function listarUsuarios(): array {
        return ModeloUsuarios::listar();
    }

    // =======================================
    // Obtener un usuario por ID
    // =======================================
    public function obtenerUsuario(int $id): ?array {
        return ModeloUsuarios::findById($id);
    }
// =======================================
// Login de usuario
// =======================================
public function ingresoUsuario(): void {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

    // Evita salida prematura antes de los headers
    if (headers_sent()) {
        ob_end_clean();
    }

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Campos vacíos',
                text: 'Debes ingresar correo y contraseña.',
                confirmButtonColor: '#3085d6'
            });
        </script>";
        return;
    }

    $usuario = ModeloUsuarios::findByEmail($email);

    // Si no se encuentra el usuario
    if (!$usuario) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Usuario no encontrado',
                text: 'Verifica tus credenciales e intenta nuevamente.',
                confirmButtonColor: '#d33'
            });
        </script>";
        return;
    }

    // ===================================================
    // Verificación híbrida de contraseñas
    // (funciona con bcrypt y Argon2id)
    // ===================================================
    $hash = $usuario['password_usuario'] ?? '';

    if (
        !password_verify($password, $hash)
    ) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Acceso denegado',
                text: 'Usuario o contraseña incorrectos.',
                confirmButtonColor: '#d33'
            });
        </script>";
        return;
    }

    // ===================================================
    // Verificar estado del usuario (activo / inactivo)
    // ===================================================
    $estadoUsuario = isset($usuario['estado_usuario']) ? (int)$usuario['estado_usuario'] : 0;

    if ($estadoUsuario !== 1) {
        echo "<script>
    Swal.fire({
        icon: 'question',
        title: 'Cuenta inactiva',
        text: 'Tu cuenta está inactiva. Por favor contacta al administrador.',
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#3085d6',
        background: '#fff',
        color: '#333',
        backdrop: `rgba(0,0,0,0.4)`
    }).then(() => {
        window.location = 'ingreso';
    });
</script>";
        exit;
    }

    // ===================================================
    // Login exitoso → iniciar sesión
    // ===================================================
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    $_SESSION['admin'] = $usuario['perfil_usuario'] === 'administrador' ? 'ok' : '';
    $_SESSION['usuario_id'] = $usuario['id_usuario'];
    $_SESSION['usuario_nombre'] = $usuario['nombre_usuario'];
    $_SESSION['usuario_foto'] = !empty($usuario['foto_usuario'])
        ? $usuario['foto_usuario']
        : 'vistas/recursos/img/default_user.png';

    // Redirigir a la página de inicio
    header('Location: inicio');
    exit;
}

public function solicitarRecuperacion() {
    if (!isset($_POST["email_recuperacion"])) {
        return;
    }

    $email = trim($_POST["email_recuperacion"]);
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

    // Validar existencia del usuario
    $usuario = ModeloUsuarios::mdlBuscarUsuarioPorEmail($email);

    if (!$usuario) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Correo no encontrado',
                text: 'No existe una cuenta registrada con ese correo.',
                confirmButtonColor: '#d33'
            });
        </script>";
        return;
    }

    // Generar selector y token
    $selector = bin2hex(random_bytes(8));
    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);
    $expira = date("Y-m-d H:i:s", time() + 10 * 60); // 10 minutos desde ahora


    // Guardar en DB
    $guardado = ModeloUsuarios::mdlRegistrarSolicitudRecuperacion([
        "user_id" => $usuario["id_usuario"],
        "selector" => $selector,
        "token_hash" => $tokenHash,
        "expires_at" => $expira,
        "request_ip" => $ip,
        "user_agent" => $userAgent
    ]);

    if ($guardado) {
    require_once "Controladores/CorreoRecuperacion.php";
    $enviado = CorreoRecuperacion::enviar($email, $selector, $token);

    if ($enviado) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Solicitud enviada',
                text: 'Hemos enviado un enlace a tu correo para restablecer tu contraseña.',
                confirmButtonColor: '#3085d6'
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error al enviar el correo',
                text: 'No se pudo enviar el correo de recuperación. Intenta más tarde.',
                confirmButtonColor: '#d33'
            });
        </script>";
    }

    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error al generar la solicitud',
                text: 'Intenta de nuevo en unos minutos.',
                confirmButtonColor: '#d33'
            });
        </script>";
    }
}

// =======================================
// Actualizar contraseña desde el enlace
// =======================================
public function actualizarPassword()
{
    require_once "Modelos/Conexion.php";

    if (
        empty($_POST['selector']) ||
        empty($_POST['token']) ||
        empty($_POST['password']) ||
        empty($_POST['confirmar'])
    ) {
        echo "<script>
            Swal.fire('Error', 'Todos los campos son obligatorios', 'error');
        </script>";
        return;
    }

    $selector = $_POST['selector'];
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirmar = $_POST['confirmar'];

    // Validar coincidencia
    if ($password !== $confirmar) {
        echo "<script>
            Swal.fire('Error', 'Las contraseñas no coinciden', 'error');
        </script>";
        return;
    }

    // VALIDACIÓN DE CONTRASEÑA FUERTE
    if (!$this->validarContraseña($password)) {
        echo "<script>
            Swal.fire({
                icon: 'warning',
                title: 'Contraseña inválida',
                text: 'Debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.'
            });
        </script>";
        return;
    }

    // Buscar solicitud válida
    $stmt = Conexion::pdo()->prepare("
        SELECT * FROM password_resets
        WHERE selector = :selector
          AND used = 0
          AND expires_at > NOW()
        LIMIT 1
    ");
    $stmt->bindParam(":selector", $selector, PDO::PARAM_STR);
    $stmt->execute();
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reset) {
        echo "<script>
            Swal.fire('Error', 'El enlace no es válido o ha expirado', 'error');
        </script>";
        return;
    }

    // Validar token
    if (!hash_equals($reset['token_hash'], hash('sha256', $token))) {
        echo "<script>
            Swal.fire('Error', 'El token no coincide o ha sido alterado', 'error');
        </script>";
        return;
    }

    // Hashear nueva contraseña con Argon2ID
    $nuevoHash = password_hash($password, PASSWORD_ARGON2ID, [
        'memory_cost' => 1 << 17,
        'time_cost'   => 4,
        'threads'     => 2
    ]);

    // Iniciar transacción
    $db = Conexion::pdo();
    $db->beginTransaction();

    try {
        // Actualizar contraseña en usuarios
        $updateUser = $db->prepare("
            UPDATE usuarios
            SET password_usuario = :pass
            WHERE id_usuario = :id
        ");
        $updateUser->bindParam(":pass", $nuevoHash, PDO::PARAM_STR);
        $updateUser->bindParam(":id", $reset['user_id'], PDO::PARAM_INT);
        $updateUser->execute();

        // Marcar el token como usado
        $markUsed = $db->prepare("
            UPDATE password_resets
            SET used = 1
            WHERE id = :id
        ");
        $markUsed->bindParam(":id", $reset['id'], PDO::PARAM_INT);
        $markUsed->execute();

        $db->commit();

        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Contraseña actualizada',
                text: 'Ya puedes iniciar sesión con tu nueva contraseña.',
                confirmButtonText: 'Ir al login'
            }).then(() => {
                window.location = 'ingreso';
            });
        </script>";
    } catch (Exception $e) {
        $db->rollBack();
        echo "<script>
            Swal.fire('Error', 'No se pudo actualizar la contraseña', 'error');
        </script>";
    }
}



}
?>
