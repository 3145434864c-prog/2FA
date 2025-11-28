<?php

// Validar rol
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: "error",
            title: "Acceso denegado",
            text: "No tienes permisos para acceder a esta página",
            confirmButtonColor: "#d33"
        }).then(() => {
            window.location.href = "salir";
        });
    </script>';
    exit;
}

require_once "Controladores/ControladorUsuarios.php";

$controlador = new ControladorUsuarios();

// Manejo de formularios
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'crear':
                $res = $controlador->crearUsuario();
                $mensaje = $res['mensaje'];
                break;
            case 'editar':
                $res = $controlador->editarUsuario();
                $mensaje = $res['mensaje'];
                break;
            case 'eliminar':
                $res = $controlador->eliminarUsuario();
                $mensaje = $res['mensaje'];
                break;
        }
    }
}

// Obtener lista de usuarios
$usuarios = $controlador->listarUsuarios();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
    /* Estilos para el estado de usuario */
    .estado-activo {
        color: #155724;
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 20px;
        padding: 5px 12px;
        font-weight: 600;
    }

    .estado-inactivo {
        color: #721c24;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 20px;
        padding: 5px 12px;
        font-weight: 600;
    }

    /* Opcional: colorear fila completa según estado */
    tr.activo {
        background-color: #f8fff8;
    }

    tr.inactivo {
        background-color: #fff5f5;
        opacity: 0.8;
    }
</style>

</head>
<body class="bg-light">

<div class="container py-4">
    <h1 class="mb-4">Usuarios</h1>

    <!-- Mostrar el mensaje de éxito o error -->
    <?php if ($mensaje): ?>
        <?php 
            // Determinar la clase de alerta según si la respuesta fue exitosa o no
            $claseAlerta = isset($res['success']) && $res['success'] ? 'alert-success' : 'alert-danger';
        ?>
        <div class="alert <?= $claseAlerta ?>"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <!-- Botón Crear Usuario -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalCrear">Crear Usuario</button>

    <!-- Tabla de usuarios -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Foto</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Perfil</th>
                <th>Estado</th>
                <th>Creación</th>
                <th>Acciones</th>
               

            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= $u['id_usuario'] ?></td>
                    <td>
                        <?php if ($u['foto_usuario']): ?>
                            <img src="<?= $u['foto_usuario'] ?>" alt="Foto" width="50">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($u['nombre_usuario']) ?></td>
                    <td><?= htmlspecialchars($u['email_usuario']) ?></td>
                    <td><?= htmlspecialchars($u['perfil_usuario']) ?></td>
                    <td>
    <?php if ($u['estado_usuario']): ?>
        <span class="estado-activo">Activo</span>
    <?php else: ?>
        <span class="estado-inactivo">Inactivo</span>
    <?php endif; ?>
</td>

                    <td><?= $u['fyh_creacion_usuario'] ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                data-bs-target="#modalEditar<?= $u['id_usuario'] ?>">Editar</button>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">
                            <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar usuario?')">Eliminar</button>
                        </form>
                    </td>
                </tr>

                <!-- Modal Editar Usuario -->
                <div class="modal fade" id="modalEditar<?= $u['id_usuario'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="accion" value="editar">
                                <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">
                                <div class="modal-header">
                                    <h5 class="modal-title">Editar Usuario</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-2">
                                        <label>Nombre</label>
                                        <input type="text" name="nombre" class="form-control" required
                                               value="<?= htmlspecialchars($u['nombre_usuario']) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control" required
                                               value="<?= htmlspecialchars($u['email_usuario']) ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label>Contraseña (dejar vacío para no cambiar)</label>
                                        <input type="password" name="password" class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label>Estado</label>
                                        <select name="estado" class="form-select">
                                            <option value="1" <?= $u['estado_usuario'] ? 'selected' : '' ?>>Activo</option>
                                            <option value="0" <?= !$u['estado_usuario'] ? 'selected' : '' ?>>Inactivo</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label>Perfil</label>
                                            <select name="perfil" class="form-select">
                                                <option value="administrador" <?= $u['perfil_usuario'] === 'administrador' ? 'selected' : '' ?>>Administrador</option>
                                                <option value="editor" <?= $u['perfil_usuario'] === 'editor' ? 'selected' : '' ?>>Editor</option>
                                                <option value="usuario" <?= $u['perfil_usuario'] === 'usuario' ? 'selected' : '' ?>>Usuario</option>
                                             </select>
                                    </div>


                                    <div class="mb-2">
                                        <label>Foto (opcional)</label>
                                        <input type="file" name="foto" class="form-control">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Crear Usuario -->
<div class="modal fade" id="modalCrear" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="crear">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Contraseña </label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-2">
    <label>Estado</label>
    <select name="estado" class="form-select">
        <option value="1">Activo</option>
        <option value="0">Inactivo</option> 
    </select>
</div>
<div class="mb-2">
    <label>Perfil</label>
    <select name="perfil" class="form-select" required>
        <option value="administrador">Administrador</option>
        <option value="editor">Editor</option>
        <option value="usuario" selected>Usuario</option>
    </select>
</div>


                    <div class="mb-2">
                        <label>Foto (opcional)</label>
                        <input type="file" name="foto" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
