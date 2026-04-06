<?php

// Validar rol
if (!isset($_SESSION['rol'])) {
    // No hay rol, sacarlo del sistema
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
} else {
    // Rol definido
    switch ($_SESSION['rol']) {
        case 'administrador':
            // Acceso permitido, no hacemos nada
            break;
        case 'editor':
        case 'usuario':
            // Redirigir a productos
            echo '<script>
                window.location.href = "productos";
            </script>';
            exit;
        default:
            // Rol desconocido, sacarlo del sistema
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    Swal.fire({
                        icon: "error",
                        title: "Acceso denegado",
                        text: "Rol no válido",
                        confirmButtonColor: "#d33"
                    }).then(() => {
                        window.location.href = "salir";
                    });
                </script>';
            exit;
    }
}


// Llamamos al controlador
require_once __DIR__ . '/../../../Controladores/ControladorDashboard.php';
$dashboard = new ControladorDashboard();

// KPIs
$inventarioTotal = $dashboard->getInventarioTotal();
$ultimosAgregados = $dashboard->getUltimosProductosAgregados();
$ultimosEliminados = $dashboard->getUltimosProductosEliminados();
$usuarios = $dashboard->getUsuariosActivosInactivos();

// Stock Crítico
$stockCritico = $dashboard->getProductosStockCritico(4);
$nombresStockCritico = array_column($stockCritico, 'nombre');  
$valoresStockCritico = array_column($stockCritico, 'stock');   
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Profesional Modernizado</title>

<!-- Fonts & Icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

<!-- Bootstrap 4 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">

<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">

<!-- Custom CSS -->
<style>
body { font-family: 'Nunito', sans-serif; background: #f8f9fc; }
#wrapper { display: flex; min-height: 100vh; }
#sidebar { min-width: 220px; background: #4e73df; color: #fff; }
#sidebar .nav-link, #sidebar .sidebar-heading { color: #fff; }
#sidebar .nav-link:hover { background: rgba(255,255,255,0.1); border-radius: 4px; transition: 0.3s; }
#content-wrapper { flex: 1; padding: 20px; }
.card { margin-bottom: 20px; border-radius: 1rem; background: rgba(255,255,255,0.95); box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: transform 0.3s ease, box-shadow 0.3s ease; }
.card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); }
.card .card-body { padding: 1.25rem 1.5rem; }
.card .text-xs { font-weight: 500; letter-spacing: 0.5px; font-size: 0.75rem; }
.card .h5 { font-weight: 700; }
.kpi-card { height: 140px; display: flex; flex-direction: column; justify-content: center; }
.kpi-card canvas { height: 35px !important; width: 100% !important; margin-top: 5px; }
.dataTables_wrapper .dataTables_paginate .paginate_button { padding: 0.25rem 0.75rem; margin-left: 2px; }
</style>
</head>
<body>

<div id="wrapper">
    <div id="content-wrapper">
        <div class="container-fluid">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-gray-800">Dashboard</h1>
                <a href="vistas/administrativas/generar_reporte/generar_reporte.php" target="_blank" class="btn btn-primary">
    <i class="fas fa-download"></i> Generar Reporte
</a>

            </div>

            <!-- Tarjetas KPI -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card border-left-primary shadow kpi-card">
                        <div class="card-body">
                            <div class="text-xs text-primary text-uppercase mb-1">Inventario Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $inventarioTotal ?></div>
                            <canvas id="sparkInventario"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-success shadow kpi-card">
                        <div class="card-body">
                            <div class="text-xs text-success text-uppercase mb-1">Últimos Productos Agregados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($ultimosAgregados) ?></div>
                            <canvas id="sparkAgregados"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-warning shadow kpi-card">
                        <div class="card-body">
                            <div class="text-xs text-warning text-uppercase mb-1">Últimos Productos Eliminados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($ultimosEliminados) ?></div>
                            <canvas id="sparkEliminados"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-danger shadow kpi-card">
                        <div class="card-body">
                            <div class="text-xs text-danger text-uppercase mb-1">Alertas de Stock Bajo</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">5</div>
                            <canvas id="sparkAlertas"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mt-3">
                    <div class="card border-left-info shadow kpi-card">
                        <div class="card-body">
                            <div class="text-xs text-info text-uppercase mb-1">Usuarios Activos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $usuarios['activos'] ?></div>
                            <canvas id="sparkUsuariosActivos"></canvas>
                        </div>
                    </div>
                </div>



                <div class="col-md-3 mt-3">
                    <div class="card border-left-secondary shadow kpi-card">
                        <div class="card-body">
                            <div class="text-xs text-secondary text-uppercase mb-1">Usuarios Inactivos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $usuarios['inactivos'] ?></div>
                            <canvas id="sparkUsuariosInactivos"></canvas>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Tablas -->
           <!-- Tablas -->
<div class="row">

    <!-- Últimos Productos Agregados -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header font-weight-bold text-primary">Últimos Productos Agregados</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaAgregados" class="table table-bordered table-sm table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($ultimosAgregados as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['nombre']) ?></td>
                                <td><?= htmlspecialchars($p['categoria']) ?></td>
                                <td><?= date('d/m/Y', strtotime($p['fecha'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimos Productos Eliminados -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header font-weight-bold text-primary">Últimos Productos Eliminados</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaEliminados" class="table table-bordered table-sm table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($ultimosEliminados as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['nombre']) ?></td>
                                <td><?= htmlspecialchars($p['categoria']) ?></td>
                                <td><?= date('d/m/Y', strtotime($p['fecha'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>


            <!-- Gráficos -->
            <div class="row">
               <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header font-weight-bold text-primary">Productos con Stock Crítico</div>
                        <div class="card-body">
                            <canvas id="stockCriticoChart" class="chart-area"></canvas>
                        </div>
                    </div>
               </div>

               <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header font-weight-bold text-primary">Usuarios Activos/Inactivos</div>
                        <div class="card-body">
                            <canvas id="usuariosDonutChart"></canvas>
                        </div>
                    </div>
               </div>
            </div>

        </div>
    </div>
</div>

<!-- JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.5/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {
    $('#tablaAgregados').DataTable({
        pageLength: 5,
        lengthChange: true,
        searching: true,
        paging: true,
        info: true,
        ordering: true,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
    });

    $('#tablaEliminados').DataTable({
        pageLength: 5,
        lengthChange: true,
        searching: true,
        paging: true,
        info: true,
        ordering: true,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
    });
});


// Sparklines
function sparkline(ctx, data, color){
    new Chart(ctx,{
        type:'line',
        data:{labels:Array(data.length).fill(''), datasets:[{data:data, borderColor:color, fill:false, tension:0.3, pointRadius:0}]},
        options:{responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{x:{display:false}, y:{display:false}}}
    });
}

sparkline(document.getElementById('sparkInventario').getContext('2d'), [<?= $inventarioTotal ?>], '#4e73df');
sparkline(document.getElementById('sparkAgregados').getContext('2d'), [<?= count($ultimosAgregados) ?>], '#1cc88a');
sparkline(document.getElementById('sparkEliminados').getContext('2d'), [<?= count($ultimosEliminados) ?>], '#f6c23e');
sparkline(document.getElementById('sparkAlertas').getContext('2d'), [5], '#e74a3b');
sparkline(document.getElementById('sparkUsuariosActivos').getContext('2d'), [<?= $usuarios['activos'] ?>], '#36b9cc');
sparkline(document.getElementById('sparkUsuariosInactivos').getContext('2d'), [<?= $usuarios['inactivos'] ?>], '#858796');

// Stock Crítico
const ctxStock = document.getElementById('stockCriticoChart').getContext('2d');
new Chart(ctxStock, {
    type: 'bar',
    data: {
        labels: <?= json_encode($nombresStockCritico) ?>,
        datasets: [{
            label: 'Unidades en Stock',
            data: <?= json_encode($valoresStockCritico) ?>,
            backgroundColor: 'rgba(231,74,59,0.6)',
            borderColor: 'rgba(231,74,59,1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { mode: 'index', intersect: false }
        },
        scales: {
            y: { 
                beginAtZero: true, 
                title: { display: true, text: 'Unidades' }, 
                grid: { color: 'rgba(200,200,200,0.2)' } 
            },
            x: { 
                title: { display: true, text: 'Productos' }, 
                grid: { color: 'rgba(200,200,200,0.2)' },
                ticks: {
                    autoSkip: false,
                    font: { size: 12 },
                    callback: function(value) {
                        return value.length > 15 ? value.substr(0,15)+'…' : value;
                    }
                }
            }
        }
    }
});

// Donut chart Usuarios
const ctxUsuarios = document.getElementById('usuariosDonutChart').getContext('2d');
new Chart(ctxUsuarios,{
    type:'doughnut',
    data:{ 
        labels:['Activos','Inactivos'], 
        datasets:[{ 
            data:[<?= $usuarios['activos'] ?>,<?= $usuarios['inactivos'] ?>], 
            backgroundColor:['#36b9cc','#858796'], 
            hoverOffset:10 
        }] 
    },
    options:{ 
        responsive:true, 
        plugins:{ 
            legend:{ position:'bottom' }, 
            tooltip:{ 
                callbacks:{ 
                    label:function(context){ return context.label+': '+context.raw+' usuarios'; } 
                } 
            } 
        } 
    }
});
</script>

</body>
</html>
