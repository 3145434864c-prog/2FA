        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard">
                <div class="sidebar-brand-icon ">
                    <!-- <i class="fas fa-laugh-wink"></i> -->
                    <img src="vistas/recursos/productos_recursos/images/nuva_tecnologia_logo.jpg" alt="" width="50px" class="rounded-pill">
                </div>
                <div class="sidebar-brand-text mx-3">Nuva S.A.S</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <!-- <li class="nav-item">
                <a class="nav-link" href="dashboard">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li> -->

            <!-- Divider -->
            <hr class="sidebar-divider">

            <?php if ($_SESSION["rol"] === 'administrador'):?>
                <!-- Usuarios -->
                <li class="nav-item">
                    <a class="nav-link" href="usuarios">
                        <i class="fas fa-solid fa-users"></i>
                        <span>Usuarios</span></a>
                </li>
            <?php endif ;?>

            <!-- Divider -->
            <hr class="sidebar-divider">



            <!-- Heading -->
            <div class="sidebar-heading">
                Inventario
            </div>

        <?php if ($_SESSION["rol"] === 'administrador'):?>
            <li class="nav-item">
                <a class="nav-link" href="dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        <?php endif ;?>

            <!-- Categorías -->
            <li class="nav-item">
                <a class="nav-link" href="categorias">
                    <i class="fas fas fa-tags"></i>
                    <span>Categorías</span></a>
            </li>

            <!-- Productos -->
            <li class="nav-item">
                <a class="nav-link" href="productos">
                    <i class="fas fas fa-box"></i>
                    <span>Productos</span></a>
            </li>

            <!-- Asistente IA -->
            <li class="nav-item">
                <a class="nav-link" href="chatbot">
                    <i class="fas fa-robot"></i>
                    <span>Asistente IA</span>
                </a>
            </li>

            <!-- Salir -->
            <li class="nav-item">
                <a class="nav-link" href="salir">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Salir</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>