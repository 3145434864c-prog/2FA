    


    <!-- Page Wrapper -->
    <div id="wrapper">

        <?php
            include_once "vistas/modulos/sidebar.php";
        ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <?php
                    include_once "vistas/modulos/topbar.php";

                    //Cargamos el contenido de acuerdo a la ruta
                    if(isset($_GET["route"])){

                        //creamos una lista de rutas permitidas

                        if(
                            $_GET["route"] == "dashboard" || 
                            $_GET["route"] == "usuarios" || 
                            $_GET["route"] == "categorias" || 
                            $_GET["route"] == "productos" || 
                            $_GET["route"] == "clientes" || 
                            $_GET["route"] == "ventas" || 
                            $_GET["route"] == "crear-venta" || 
                            $_GET["route"] == "reportes" ||
                            $_GET["route"] == "salir"

                        ){
                            include_once "vistas/administrativas/".$_GET["route"]."/".$_GET["route"].".php";
                        }else{
                            include_once "vistas/404/404.php";
                        }

                    }else{
                        include_once "vistas/administrativas/productos/productos.php";
                    }
                    
                ?>

            </div>
            <!-- End of Main Content -->

            <?php
                include_once "vistas/modulos/footer.php";
            ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

                <?php
                    include_once "vistas/modulos/botones-flotantes.php";
                    include_once "vistas/modulos/modals.php";
                ?>



    <!-- SCRIPTS EXTERNOS -->




