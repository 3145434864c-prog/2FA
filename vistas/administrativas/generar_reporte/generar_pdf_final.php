<?php
// vistas/administrativas/generar_reporte/generar_pdf_final_v1.1.php
declare(strict_types=1);

// Ajusta ruta si tu estructura cambia
require_once __DIR__ . '/../../../Controladores/ControladorDashboard.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

set_time_limit(60);

// Instancia controlador
$dashboard = new ControladorDashboard();

// Datos
$inventarioTotal   = $dashboard->getInventarioTotal();
$ultimosAgregados  = $dashboard->getUltimosProductosAgregados();
$ultimosEliminados = $dashboard->getUltimosProductosEliminados();
$usuarios          = $dashboard->getUsuariosActivosInactivos();
$stockCritico      = $dashboard->getProductosStockCritico(4);

$nombresStockCritico = array_map(fn($r) => (string)$r['nombre'], $stockCritico);
$valoresStockCritico = array_map(fn($r) => (int)$r['stock'], $stockCritico);

// QuickChart URL builder
function quickchart_url(array $chartConfig, int $width = 800, int $height = 400, int $devicePixelRatio = 1): string {
    $base = 'https://quickchart.io/chart';
    $params = [
        'c' => json_encode($chartConfig),
        'w' => $width,
        'h' => $height,
        'devicePixelRatio' => $devicePixelRatio
    ];
    return $base . '?' . http_build_query($params);
}

// Fetch image and return base64 data URI (tries file_get_contents then cURL)
function fetch_image_base64(string $url, int $timeout = 10): ?string {
    // Try file_get_contents if allowed
    $ctx = stream_context_create(['http' => ['timeout' => $timeout], 'https' => ['timeout' => $timeout]]);
    try {
        $data = @file_get_contents($url, false, $ctx);
        if ($data !== false) {
            return 'data:image/png;base64,' . base64_encode($data);
        }
    } catch (Throwable $e) {
        // continue to curl
    }

    // Try cURL
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $data = curl_exec($ch);
        $err  = curl_errno($ch);
        curl_close($ch);
        if ($err === 0 && $data !== false) {
            return 'data:image/png;base64,' . base64_encode($data);
        }
    }

    return null;
}

// Construir gráficos QuickChart
$stockConfig = [
    'type' => 'bar',
    'data' => [
        'labels' => $nombresStockCritico,
        'datasets' => [[
            'label' => 'Unidades en Stock',
            'data' => $valoresStockCritico,
            'backgroundColor' => array_map(fn() => 'rgba(231,74,59,0.8)', $valoresStockCritico),
            'borderColor' => array_map(fn() => 'rgba(231,74,59,1)', $valoresStockCritico),
            'borderWidth' => 1
        ]]
    ],
    'options' => [
        'plugins' => [
            'legend' => ['display' => false],
            'title' => ['display' => true, 'text' => 'Stock Crítico']
        ],
        'scales' => [
            'y' => ['beginAtZero' => true],
            'x' => ['ticks' => ['maxRotation' => 30, 'minRotation' => 0]]
        ]
    ]
];

$usuariosConfig = [
    'type' => 'doughnut',
    'data' => [
        'labels' => ['Activos', 'Inactivos'],
        'datasets' => [[
            'data' => [(int)$usuarios['activos'], (int)$usuarios['inactivos']],
            'backgroundColor' => ['#36b9cc','#858796']
        ]]
    ],
    'options' => ['plugins' => ['title' => ['display' => true, 'text' => 'Usuarios']]]
];

$stockUrl    = quickchart_url($stockConfig, 900, 450);
$usuariosUrl = quickchart_url($usuariosConfig, 600, 350);

$stockBase64    = fetch_image_base64($stockUrl);
$usuariosBase64 = fetch_image_base64($usuariosUrl);

// Logo local (ruta proporcionada por ti)
$logoRel = 'vistas/recursos/productos_recursos/images/nuva_tecnologia_logo.jpg';
$logoAbs = realpath(__DIR__ . '/../../../' . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $logoRel));
$logoData = null;
if ($logoAbs && is_file($logoAbs) && is_readable($logoAbs)) {
    $logoData = 'data:image/' . pathinfo($logoAbs, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($logoAbs));
}

// Preparar fecha/hora
$fechaGeneracion = (new DateTime())->format('d/m/Y H:i:s');

// HTML del PDF
ob_start();
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<style>
/* Estilos simples compatibles con Dompdf */
body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #222; margin: 20px; }
.header { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
.logo { width: 90px; height: auto; }
.title { flex:1; text-align:left; }
.company-name { font-size:16px; font-weight:700; margin-bottom:2px; }
.report-meta { text-align:right; font-size:11px; color:#666; }
.kpi-box { display:flex; gap:10px; margin:10px 0 16px 0; }
.kpi { background:#f7f7f7; padding:8px; border-radius:4px; border:1px solid #e0e0e0; flex:1; text-align:center; }
.section-title { font-size:13px; font-weight:700; margin-top:18px; margin-bottom:6px; }
.table { width:100%; border-collapse:collapse; margin-bottom:10px; }
.table th, .table td { border:1px solid #ccc; padding:6px; font-size:11px; }
.chart { width:100%; max-height:320px; display:block; margin:6px 0; }
.footer { position: fixed; bottom: 10px; left: 20px; right: 20px; font-size:10px; color:#666; text-align:center; }
.page-number { float:right; font-size:10px; color:#666; }
</style>
</head>
<body>

<div class="header">
    <?php if ($logoData): ?>
        <img src="<?= $logoData ?>" class="logo" alt="Logo">
    <?php else: ?>
        <div style="width:90px;height:60px;background:#eee;display:flex;align-items:center;justify-content:center;color:#999;font-size:12px;border:1px solid #ddd;">Nuva</div>
    <?php endif; ?>

    <div class="title">
        <div class="company-name">Nuva Tecnología</div>
        <div style="font-size:12px;color:#555;">Reporte general del inventario</div>
    </div>

    <div class="report-meta">
        Fecha: <?= $fechaGeneracion ?><br>
        Generado por: Sistema
    </div>
</div>

<div class="kpi-box">
    <div class="kpi"><div style="font-size:11px;color:#555;">Inventario Total</div><div style="font-size:16px;font-weight:700;"><?= htmlspecialchars((string)$inventarioTotal) ?></div></div>
    <div class="kpi"><div style="font-size:11px;color:#555;">Usuarios Activos</div><div style="font-size:16px;font-weight:700;"><?= htmlspecialchars((string)$usuarios['activos']) ?></div></div>
    <div class="kpi"><div style="font-size:11px;color:#555;">Usuarios Inactivos</div><div style="font-size:16px;font-weight:700;"><?= htmlspecialchars((string)$usuarios['inactivos']) ?></div></div>
</div>

<div class="section-title">Stock Crítico</div>
<?php if ($stockBase64): ?>
    <img src="<?= $stockBase64 ?>" class="chart" alt="Stock crítico">
<?php else: ?>
    <div style="padding:12px;border:1px solid #eee;background:#fafafa;color:#999;">Gráfico de stock no disponible</div>
<?php endif; ?>

<div class="section-title">Usuarios</div>
<?php if ($usuariosBase64): ?>
    <img src="<?= $usuariosBase64 ?>" class="chart" alt="Usuarios">
<?php else: ?>
    <div style="padding:12px;border:1px solid #eee;background:#fafafa;color:#999;">Gráfico de usuarios no disponible</div>
<?php endif; ?>

<div class="section-title">Últimos Productos Agregados</div>
<table class="table">
    <thead><tr><th>Producto</th><th>Categoría</th><th>Fecha</th></tr></thead>
    <tbody>
    <?php foreach ($ultimosAgregados as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['nombre']) ?></td>
            <td><?= htmlspecialchars($r['categoria']) ?></td>
            <td><?= htmlspecialchars(date('d/m/Y', strtotime($r['fecha']))) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="section-title">Últimos Productos Eliminados</div>
<table class="table">
    <thead><tr><th>Producto</th><th>Categoría</th><th>Fecha</th></tr></thead>
    <tbody>
    <?php foreach ($ultimosEliminados as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['nombre']) ?></td>
            <td><?= htmlspecialchars($r['categoria']) ?></td>
            <td><?= htmlspecialchars(date('d/m/Y', strtotime($r['fecha']))) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="footer">
    Fecha y hora de generación: <?= $fechaGeneracion ?>
</div>

</body>
</html>
<?php
$html = ob_get_clean();

// Configurar Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Helvetica');
// opcional: acotar chroot a la raíz del proyecto para seguridad
$options->setChroot(realpath(__DIR__ . '/../../../'));

// Generar PDF
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Forzar descarga
$dompdf->stream('reporte_inventario_nuva_tecnologia.pdf', ['Attachment' => true]);
exit;
