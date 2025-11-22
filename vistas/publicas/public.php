<!-- styles public -->
<link href="vistas/publicas/css/styles.css" rel="stylesheet" />

<?php
// Cargamos el contenido de acuerdo a la ruta
if (isset($_GET["route"])) {

    // Lista de rutas permitidas
    $rutasPermitidas = ["inicio", "registro", "equipo", "productos", "ingreso", "recuperar", "recuperar_confirmar"];

    if (in_array($_GET["route"], $rutasPermitidas)) {

        if ($_GET["route"] == "recuperar_confirmar") {
            include_once "vistas/publicas/recuperar/recuperar_confirmar.php";
        } else {
            include_once "vistas/publicas/" . $_GET["route"] . "/" . $_GET["route"] . ".php";
        }

    } else {
        include_once "vistas/404/404.php";
    }

} else {
    include_once "vistas/publicas/inicio/inicio.php";
}

// Modulos comunes
include_once "vistas/modulos/botones-flotantes.php";
include_once "vistas/modulos/modals.php";
?>
