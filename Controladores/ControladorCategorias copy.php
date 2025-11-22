<?php

// require_once "Modelos/ModeloCategorias.php";

// class ControladorCategorias{

//  static public function crearCategoria(){
//     if(isset($_POST['categoria'])){

//         $categoria = $_POST['categoria'];

//             if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü0-9\s\-]+$/u', $categoria)) {

//                 echo '
//                     <script>
//                         Swal.fire({
//                             title: "Cuidado",
//                             text: "No se permiten caracteres especiales.",
//                             icon: "error",
//                             confirmButtonText: "Entendido"
//                         }).then((result) => {
//                             if (result.isConfirmed) {
//                                 window.location = "categorias";
//                             }
//                         });
//                     </script>
//                 ';

//             } 

//             $categoria = ModeloCategorias::registroCategoria($categoria);

//             if($respuesta == "ok"){
//                 echo '
//                     <script>
//                         Swal.fire({
//                             title: "Registro exitoso,
//                             text: "La categoría ha sido guardada exitosamente",
//                             icon: "success",
//                             confirmButtonText: "Entendido"
//                         }).then((result) => {
//                             if (result.isConfirmed) {
//                                 window.location = "categorias";
//                             }
//                         });
//                     </script>
//                 ';
//             }


//     }
//  }
// }


require_once "Modelos/ModeloCategorias.php";

class ControladorCategorias
{
    public static function crearCategoria()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['categoria'])) {
            return;
        }

        // 1) Tomar y sanear
        $categoria = trim($_POST['categoria']);

        // 2) Validar: letras (con acentos), números, espacios y guiones
        if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñÜü0-9\s\-]+$/u', $categoria)) {
            echo '
                <script>
                    Swal.fire({
                        title: "Cuidado",
                        text: "No se permiten caracteres especiales.",
                        icon: "error",
                        confirmButtonText: "Entendido"
                    }).then(() => {
                        window.location = "categorias";
                    });
                </script>
            ';
            return;
        }

        // 3) Guardar usando el modelo
        // El modelo debe devolver el ID insertado (int) o null en caso de error
        $nuevoId = ModeloCategorias::registrarCategoria($categoria);

        if ($nuevoId) {
            echo '
                <script>
                    Swal.fire({
                        title: "Registro exitoso",
                        text: "La categoría ha sido guardada exitosamente.",
                        icon: "success",
                        confirmButtonText: "Entendido"
                    }).then(() => {
                        window.location = "categorias";
                    });
                </script>
            ';
            return;
        } else {
            echo '
                <script>
                    Swal.fire({
                        title: "Error",
                        text: "No fue posible guardar la categoría. Intenta nuevamente.",
                        icon: "error",
                        confirmButtonText: "Entendido"
                    }).then(() => {
                        window.location = "categorias";
                    });
                </script>
            ';
            return;
        }
    }


    // ✅ Nuevo método para listar todas las categorías
    public static function mostrarCategorias()
    {
        $categorias = ModeloCategorias::mostrarCategorias();
        return $categorias;
    }
}
