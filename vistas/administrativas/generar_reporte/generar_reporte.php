<?php
// vistas/administrativas/generar_reporte/generar_reporte.php
declare(strict_types=1);

// Ajusta rutas relativas según tu estructura. Desde este archivo subimos 3 niveles a la raíz.
require_once __DIR__ . '/../../../Controladores/ControladorDashboard.php';
// (vendor/autoload.php lo necesitamos cuando integremos Dompdf; lo incluimos ahora por si ya está)
if (file_exists(__DIR__ . '/../../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../../vendor/autoload.php';
}

$dashboard = new ControladorDashboard();

// Obtener datos
$inventarioTotal     = $dashboard->getInventarioTotal();
$ultimosAgregados    = $dashboard->getUltimosProductosAgregados();
$ultimosEliminados   = $dashboard->getUltimosProductosEliminados();
$usuarios            = $dashboard->getUsuariosActivosInactivos();
$stockCritico        = $dashboard->getProductosStockCritico(4);

// Preparar arrays para Chart (labels y datos)
$nombresStockCritico = array_map(function($r){ return (string)$r['nombre']; }, $stockCritico);
$valoresStockCritico = array_map(function($r){ return (int)$r['stock']; }, $stockCritico);

// QuickChart: construir configuración ChartJS y URL
function quickchart_url(array $chartConfig, int $width = 800, int $height = 400, int $devicePixelRatio = 1): string {
    $base = 'https://quickchart.io/chart';
    $params = [
        'c' => json_encode($chartConfig),
        'w' => (string)$width,
        'h' => (string)$height,
        'devicePixelRatio' => (string)$devicePixelRatio
    ];
    return $base . '?' . http_build_query($params);
}

// Stock crítico (bar)
$stockConfig = [
    'type' => 'bar',
    'data' => [
        'labels' => $nombresStockCritico,
        'datasets' => [
            [
                'label' => 'Unidades en Stock',
                'data' => $valoresStockCritico,
                'backgroundColor' => array_map(function(){ return 'rgba(231,74,59,0.8)'; }, $valoresStockCritico),
                'borderColor' => array_map(function(){ return 'rgba(231,74,59,1)'; }, $valoresStockCritico),
                'borderWidth' => 1
            ]
        ]
    ],
    'options' => [
        'plugins' => [
            'legend' => ['display' => false],
            'title' => ['display' => true, 'text' => 'Stock Crítico']
        ],
        'scales' => [
            'y' => ['beginAtZero' => true, 'title' => ['display' => true, 'text' => 'Unidades']],
            'x' => ['ticks' => ['maxRotation' => 45, 'minRotation' => 45]]
        ]
    ]
];

$stockChartUrl = quickchart_url($stockConfig, 900, 450);

// Usuarios donut
$usuariosConfig = [
    'type' => 'doughnut',
    'data' => [
        'labels' => ['Activos', 'Inactivos'],
        'datasets' => [
            [
                'data' => [(int)$usuarios['activos'], (int)$usuarios['inactivos']],
                'backgroundColor' => ['#36b9cc','#858796']
            ]
        ]
    ],
    'options' => [
        'plugins' => [
            'title' => ['display' => true, 'text' => 'Usuarios']
        ]
    ]
];

$usuariosChartUrl = quickchart_url($usuariosConfig, 600, 350);

// --- HTML de preview simple ---
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Preview - Generar Reporte</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f8f9fc;font-family:Arial,Helvetica,sans-serif;padding:20px}
.kpi {display:flex;gap:20px;margin-bottom:20px}
.kpi .card{flex:1;padding:12px}
.preview-img{max-width:100%;height:auto;border:1px solid #ddd;background:#fff;padding:6px}
.table-fixed th, .table-fixed td { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>
</head>
<body>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Preview: Generar Reporte</h2>
        <div>
            <!-- enlace que usaremos luego para generar PDF final -->
            <a href="generar_pdf_final.php" class="btn btn-primary" target="_blank"><i class="fas fa-file-pdf"></i> Generar PDF (próximo)</a>
        </div>
    </div>

    <div class="kpi">
        <div class="card">
            <div class="text-muted small">Inventario Total</div>
            <div class="h4"><?= htmlspecialchars((string)$inventarioTotal) ?></div>
        </div>
        <div class="card">
            <div class="text-muted small">Usuarios Activos</div>
            <div class="h4"><?= htmlspecialchars((string)$usuarios['activos']) ?></div>
        </div>
        <div class="card">
            <div class="text-muted small">Usuarios Inactivos</div>
            <div class="h4"><?= htmlspecialchars((string)$usuarios['inactivos']) ?></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <h5>Stock Crítico (preview)</h5>
            <img src="<?= htmlspecialchars($stockChartUrl) ?>" class="preview-img" alt="Stock Crítico">
        </div>
        <div class="col-md-4">
            <h5>Usuarios (preview)</h5>
            <img src="<?= htmlspecialchars($usuariosChartUrl) ?>" class="preview-img" alt="Usuarios">
        </div>
    </div>

    <hr>

    <h5>Últimos Productos Agregados</h5>
    <div class="table-responsive">
        <table class="table table-sm table-bordered table-fixed">
            <thead><tr><th>Producto</th><th>Categoría</th><th>Fecha</th></tr></thead>
            <tbody>
            <?php foreach($ultimosAgregados as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                    <td><?= htmlspecialchars($p['categoria']) ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($p['fecha']))) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h5>Últimos Productos Eliminados</h5>
    <div class="table-responsive">
        <table class="table table-sm table-bordered table-fixed">
            <thead><tr><th>Producto</th><th>Categoría</th><th>Fecha</th></tr></thead>
            <tbody>
            <?php foreach($ultimosEliminados as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                    <td><?= htmlspecialchars($p['categoria']) ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($p['fecha']))) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

   
</body>
</html>
