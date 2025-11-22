<?php
require_once "Modelos/ModeloProductos.php";
require_once "Modelos/ModeloCategorias.php"; // para combos

class ControladorProductos
{
    /* ===================== Helpers de UI ===================== */
    private static function swal(string $title, string $text, string $icon, string $redirect): string
    {
        $titleEsc = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $textEsc  = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $redirEsc = htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8');

        return '<script>
          Swal.fire({
            title: "'.$titleEsc.'",
            text: "'.$textEsc.'",
            icon: "'.$icon.'",
            confirmButtonText: "Entendido"
          }).then(()=>{ window.location = "'.$redirEsc.'"; });
        </script>';
    }

    /* ===================== Helpers de rutas ===================== */
    /** Raíz del proyecto (donde está index.php y la carpeta /public) */
    private static function projectRoot(): string
    {
        // __DIR__ = /Controladores  → subimos un nivel
        $root = realpath(__DIR__ . '/..');
        return $root !== false ? $root : dirname(__DIR__);
    }

    /** Ruta absoluta a /public */
    private static function publicDirAbs(): string
    {
        return self::projectRoot() . DIRECTORY_SEPARATOR . 'public';
    }

    /** Ruta absoluta a /public/uploads/products (crea si no existe) */
    private static function uploadsDirAbs(): string
    {
        return self::publicDirAbs() . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'products';
    }

    /** URL pública relativa (como tu webroot es la raíz, incluimos /public) */
    private static function uploadsUrlRel(): string
    {
        return 'public/uploads/products';
    }

    /** Convierte una ruta relativa tipo "/public/..." o "uploads/products/..." a ruta absoluta en disco */
    private static function pathAbsFromRel(string $rel): string
    {
        $rel = ltrim($rel, "/\\");
        $rel = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $rel);

        // Si ya viene con "public/..." la resolvemos desde la raíz del proyecto
        if (strpos($rel, 'public' . DIRECTORY_SEPARATOR) === 0) {
            return self::projectRoot() . DIRECTORY_SEPARATOR . $rel;
        }

        // Si viene como "uploads/products/..." la resolvemos dentro de /public
        return self::publicDirAbs() . DIRECTORY_SEPARATOR . $rel;
    }

    /**
     * Valida y guarda una imagen. Devuelve la RUTA pública relativa (p.ej. /public/uploads/products/abc123.webp) o null.
     * Lanza RuntimeException con mensaje de validación si corresponde.
     */
    private static function guardarImagen(array $file, ?string $imagenAnterior = null): ?string
    {
        if (!isset($file['tmp_name']) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            // No cargaron nueva imagen
            return null;
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Error al subir archivo (código '.($file['error'] ?? 'desconocido').').');
        }
        if (($file['size'] ?? 0) > 3 * 1024 * 1024) {
            throw new RuntimeException('La imagen supera 3 MB.');
        }

        // Validar MIME real
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']) ?: '';
        $permitidos = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($permitidos[$mime])) {
            throw new RuntimeException('Formato no permitido. Usa JPG, PNG o WEBP.');
        }

        // Validación extra
        if (!@getimagesize($file['tmp_name'])) {
            throw new RuntimeException('El archivo no es una imagen válida.');
        }

        // Asegurar carpeta
        $dirAbs = self::uploadsDirAbs();
        if (!is_dir($dirAbs)) {
            @mkdir($dirAbs, 0775, true);
        }

        // Nombre único
        $ext    = $permitidos[$mime];
        $nombre = bin2hex(random_bytes(8)) . '.' . $ext;
        $destAbs = $dirAbs . DIRECTORY_SEPARATOR . $nombre;

        if (!move_uploaded_file($file['tmp_name'], $destAbs)) {
            throw new RuntimeException('No se pudo guardar la imagen.');
        }

        // Borrar anterior si corresponde
        if ($imagenAnterior) {
            $anteriorAbs = self::pathAbsFromRel($imagenAnterior);
            if ($anteriorAbs && is_file($anteriorAbs)) {
                @unlink($anteriorAbs);
            }
        }

        // Devolver ruta pública relativa (incluye /public)
        return self::uploadsUrlRel() . '/' . $nombre;
    }

    /* ===================== Acciones ===================== */

    public static function mostrarProductos(): array
    {
        return ModeloProductos::listar();
    }

    public static function crearProducto(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || isset($_POST['id_producto'])) return;

        $id_categoria = (int)($_POST['id_categoria'] ?? 0);
        $nombre       = trim($_POST['nombre'] ?? '');
        $descripcion  = trim($_POST['descripcion'] ?? '') ?: null;
        $precio       = (float)($_POST['precio'] ?? 0);
        $stock        = (int)($_POST['stock'] ?? 0);

        if ($id_categoria <= 0 || $nombre === '' || !preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü0-9\s\-\.\,]+$/u', $nombre)) {
            echo self::swal('Cuidado', 'Datos inválidos. Verifica categoría y nombre.', 'error', 'productos');
            return;
        }

        $rutaRel = null;
        try {
            $rutaRel = self::guardarImagen($_FILES['imagen'] ?? []);
        } catch (Throwable $e) {
            echo self::swal('Imagen inválida', $e->getMessage(), 'error', 'productos');
            return;
        }

        $id = ModeloProductos::crear([
            'id_categoria' => $id_categoria,
            'nombre'       => $nombre,
            'descripcion'  => $descripcion,
            'precio'       => $precio,
            'stock'        => $stock,
            'imagen'       => $rutaRel
        ]);

        if ($id) {
            echo self::swal('Registro exitoso', 'El producto fue guardado correctamente.', 'success', 'productos');
        } else {
            // si falló, eliminar la imagen recién subida para evitar basura
            if ($rutaRel) {
                @unlink(self::pathAbsFromRel($rutaRel));
            }
            echo self::swal('Error', 'No se pudo guardar el producto.', 'error', 'productos');
        }
    }

    public static function editarProducto(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_producto'])) return;

        $id_producto  = (int)($_POST['id_producto'] ?? 0);
        $id_categoria = (int)($_POST['id_categoria'] ?? 0);
        $nombre       = trim($_POST['nombre'] ?? '');
        $descripcion  = trim($_POST['descripcion'] ?? '') ?: null;
        $precio       = (float)($_POST['precio'] ?? 0);
        $stock        = (int)($_POST['stock'] ?? 0);

        if ($id_producto <= 0 || $id_categoria <= 0 || $nombre === '') {
            echo self::swal('Cuidado', 'Datos inválidos para editar.', 'error', 'productos');
            return;
        }

        $actual = ModeloProductos::obtenerPorId($id_producto);
        if (!$actual) {
            echo self::swal('No encontrado', 'El producto no existe.', 'error', 'productos');
            return;
        }

        $nuevaRuta = null;
        try {
            $nuevaRuta = self::guardarImagen($_FILES['imagen'] ?? [], $actual['imagen'] ?? null);
        } catch (Throwable $e) {
            echo self::swal('Imagen inválida', $e->getMessage(), 'error', 'productos');
            return;
        }

        // Si no cargaron nueva imagen, no tocamos el campo 'imagen'
        $payload = [
            'id_categoria' => $id_categoria,
            'nombre'       => $nombre,
            'descripcion'  => $descripcion,
            'precio'       => $precio,
            'stock'        => $stock
        ];
        if ($nuevaRuta !== null) {
            $payload['imagen'] = $nuevaRuta; // nueva ruta si subieron
        }

        $ok = ModeloProductos::actualizar($id_producto, $payload);
        if ($ok) {
            echo self::swal('Actualizado', 'El producto fue actualizado correctamente.', 'success', 'productos');
        } else {
            // si falló y subimos imagen nueva, limpiar archivo recién subido
            if ($nuevaRuta) {
                @unlink(self::pathAbsFromRel($nuevaRuta));
            }
            echo self::swal('Error', 'No se pudo actualizar el producto.', 'error', 'productos');
        }
    }

    public static function eliminarProducto(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_producto_eliminar'])) return;

        $id = (int)($_POST['id_producto_eliminar'] ?? 0);
        if ($id <= 0) {
            echo self::swal('Cuidado', 'ID inválido para eliminar.', 'error', 'productos');
            return;
        }

        $actual = ModeloProductos::obtenerPorId($id);
        if (!$actual) {
            echo self::swal('No encontrado', 'El producto no existe.', 'error', 'productos');
            return;
        }

        try {
            $ok = ModeloProductos::eliminar($id);
            if ($ok) {
                // borra imagen si existe
                if (!empty($actual['imagen'])) {
                    $abs = self::pathAbsFromRel($actual['imagen']);
                    if ($abs && is_file($abs)) @unlink($abs);
                }
                echo self::swal('Eliminado', 'El producto fue eliminado correctamente.', 'success', 'productos');
            } else {
                echo self::swal('Error', 'No fue posible eliminar.', 'error', 'productos');
            }
        } catch (Throwable $e) {
            $msg = 'Ocurrió un error inesperado.';
            if ($e instanceof PDOException) {
                $code = (int)($e->errorInfo[1] ?? 0);
                if ($code === 1451) $msg = 'No se puede eliminar: el producto está referenciado.';
            }
            error_log("ControladorProductos::eliminar => ".$e->getMessage());
            echo self::swal('Error', $msg, 'error', 'productos');
        }
    }

    /* ===================== Soporte combos ===================== */
    public static function listarCategorias(): array
    {
        return ModeloCategorias::mostrarCategorias();
    }
}
