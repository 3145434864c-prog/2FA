<?php
require_once "Modelos/ModeloCategorias.php";

class ControladorCategorias
{
    /* =========================
       Helpers
    ==========================*/
    private static function swal(string $title, string $text, string $icon, string $redirect = 'categorias'): void
    {
        // Escapar
        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $text  = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $redir = htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8');

        echo '<script>
            if (window.Swal) {
              Swal.fire({ title: "'.$title.'", text: "'.$text.'", icon: "'.$icon.'", confirmButtonText: "Entendido"})
                  .then(() => { window.location = "'.$redir.'"; });
            } else {
              alert("'.$title.'\\n'.$text.'");
              window.location = "'.$redir.'";
            }
        </script>';
    }

    private static function validarNombre(?string $nombre): ?string
    {
        if ($nombre === null) return null;
        $nombre = trim($nombre);

        // Letras (incluye acentos), números, espacios y guiones
        if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü0-9\s\-]+$/u', $nombre)) {
            return null;
        }
        // Longitud mínima razonable
        if (mb_strlen($nombre) < 2) {
            return null;
        }
        return $nombre;
    }

    private static function filtrarId($valor): ?int
    {
        $id = filter_var($valor, FILTER_VALIDATE_INT);
        return ($id && $id > 0) ? (int)$id : null;
    }

    /* =========================
       Crear
       Espera: POST[categoria]
       (tu vista envía este formulario en el modal "Nueva categoría")
    ==========================*/
    public function crearCategoria(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        // Si viene id_categoria, entonces NO es "crear" (evita choque con form de edición)
        if (isset($_POST['id_categoria'])) return;

        if (!isset($_POST['categoria'])) return;

        $nombre = self::validarNombre($_POST['categoria']);
        if ($nombre === null) {
            self::swal('Cuidado', 'No se permiten caracteres especiales o el nombre es muy corto.', 'error');
            return;
        }

        $nuevoId = ModeloCategorias::registrarCategoria($nombre);

        if ($nuevoId) {
            self::swal('Registro exitoso', 'La categoría ha sido guardada exitosamente.', 'success');
        } else {
            self::swal('Error', 'No fue posible guardar la categoría. Intenta nuevamente.', 'error');
        }
    }

    /* =========================
       Editar
       Espera: POST[id_categoria, categoria]
       (tu vista envía este formulario en el modal "Editar categoría")
    ==========================*/
    public function editarCategoria(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        // Este bloque solo aplica si viene id de edición
        if (!isset($_POST['id_categoria'])) return;
        if (!isset($_POST['categoria'])) {
            self::swal('Cuidado', 'Falta el nombre de la categoría.', 'error');
            return;
        }

        $id = self::filtrarId($_POST['id_categoria']);
        if ($id === null) {
            self::swal('Cuidado', 'El identificador de la categoría es inválido.', 'error');
            return;
        }

        $nombre = self::validarNombre($_POST['categoria']);
        if ($nombre === null) {
            self::swal('Cuidado', 'No se permiten caracteres especiales o el nombre es muy corto.', 'error');
            return;
        }

        $ok = ModeloCategorias::actualizarCategoria($id, $nombre);

        if ($ok) {
            self::swal('Actualización exitosa', 'La categoría ha sido actualizada correctamente.', 'success');
        } else {
            self::swal('Error', 'No fue posible actualizar la categoría. Intenta nuevamente.', 'error');
        }
    }

    /* =========================
       Eliminar
       Espera: POST[id_categoria_eliminar]
       (tu vista envía este formulario oculto al confirmar SweetAlert)
    ==========================*/
    public function eliminarCategoria(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        if (!isset($_POST['id_categoria_eliminar'])) return;

        $id = self::filtrarId($_POST['id_categoria_eliminar']);
        if ($id === null) {
            self::swal('Cuidado', 'El identificador de la categoría es inválido.', 'error');
            return;
        }

        $ok = ModeloCategorias::eliminarCategoria($id);

        if ($ok) {
            self::swal('Eliminación exitosa', 'La categoría ha sido eliminada correctamente.', 'success');
        } else {
            // Si tu modelo devuelve false por restricción de FK, aquí puedes personalizar el mensaje
            self::swal('No se pudo eliminar', 'La categoría no se pudo eliminar. Verifica si tiene productos asociados.', 'error');
        }
    }

    /* =========================
       Listar
       (usado por tu vista para pintar la tabla)
    ==========================*/
    public static function mostrarCategorias(): array
    {
        return ModeloCategorias::mostrarCategorias() ?? [];
    }
}
