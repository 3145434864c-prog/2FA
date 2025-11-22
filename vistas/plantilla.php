<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>inventario</title>
    <link rel="shortcut icon" href="public/identidad-corporativa/inventario.svg" type="image/x-icon">

    <!-- ESTILOS EXTERNOS -->
    <!-- Custom fonts for this template-->
    <link href="vistas/recursos/librerias/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <!-- bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- ESTILOS PROPIOS -->
    <!-- Custom styles for this template-->
    <link href="vistas/recursos/css/sb-admin-2.min.css" rel="stylesheet">


  
    <!-- Bootstrap core JavaScript-->
    <script src="vistas/recursos/librerias/jquery/jquery.min.js"></script>
    <script src="vistas/recursos/librerias/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <!-- Core plugin JavaScript-->
    <script src="vistas/recursos/librerias/jquery-easing/jquery.easing.min.js"></script>

    <!-- Page level plugins -->
    <script src="vistas/recursos/librerias/datatables/jquery.dataTables.min.js"></script>
    <script src="vistas/recursos/librerias/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level plugins -->
    <script src="vistas/recursos/librerias/chart.js/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>  


</head>

<body id="page-top">

    <?php

        if(isset($_SESSION["admin"]) && $_SESSION["admin"] == "ok"){

            include_once  "vistas/administrativas/admin.php";
        
        }else{

            include_once "vistas/publicas/public.php";
        
        }
        
    ?>


    <!-- Scripts propios -->

    <!-- Custom scripts for all pages-->
    <script src="vistas/recursos/js/sb-admin-2.min.js"></script>

    <!-- <script src="vistas/recursos/js/activar-plugins.js"></script> -->
    <script src="vistas/recursos/js/datatable.js"></script>

</body>

</html>