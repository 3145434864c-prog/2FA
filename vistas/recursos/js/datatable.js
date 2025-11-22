
/**
 * Inicializa DataTables en todas las tablas .tablas
 * - Idioma español
 * - Responsive
 * - Tooltips Bootstrap (4/5)
 * - Numeración automática si el primer <th> es '#'
 * - Config por tabla con data-attributes:
 *   data-nonorderable="0,2"      -> columnas no ordenables
 *   data-nonsearchable="0,2"     -> columnas no buscables
 *   data-order-col="1"           -> columna por defecto para orden
 *   data-order-dir="asc|desc"    -> dirección por defecto
 */
(function($){
  $(function(){

    // Asegura que DataTables está cargado
    if (!$.fn.DataTable) {
      console.error('DataTables no está cargado. Revisa el orden de los <script>.');
      return;
    }

    // Activar tooltips globales (Bootstrap 4/5)
    var activarTooltips = function(){
      $('[data-bs-toggle="tooltip"], [data-toggle="tooltip"]').tooltip();
    };
    activarTooltips();

    $('table.tablas').each(function(){

      var $table = $(this);
      if ($.fn.DataTable.isDataTable(this)) return; // evita reinicializar

      // Lee configuraciones desde data-attributes
      var nonOrderable   = ($table.data('nonorderable')   || '').toString().trim();
      var nonSearchable  = ($table.data('nonsearchable')  || '').toString().trim();
      var orderCol       = parseInt($table.data('order-col'), 10);
      var orderDir       = ($table.data('order-dir') || 'asc').toString().toLowerCase();

      // Construye columnDefs dinámicos
      var columnDefs = [];

      if (nonOrderable) {
        var idx = nonOrderable.split(',').map(function(v){ return parseInt(v.trim(),10); }).filter(Number.isInteger);
        if (idx.length){ columnDefs.push({ targets: idx, orderable: false }); }
      }
      if (nonSearchable) {
        var idx2 = nonSearchable.split(',').map(function(v){ return parseInt(v.trim(),10); }).filter(Number.isInteger);
        if (idx2.length){ columnDefs.push({ targets: idx2, searchable: false }); }
      }

      // Detecta si la primera columna es '#' para numeración automática
      var tieneNumeracion = false;
      var thPrimero = $table.find('thead th').first().text().trim();
      if (thPrimero === '#' || thPrimero.toLowerCase() === 'n°' || thPrimero.toLowerCase() === 'no' ) {
        tieneNumeracion = true;
        // si no la incluyeron en nonOrderable/nonSearchable, fuerza:
        columnDefs.push({ targets: 0, orderable: false, searchable: false });
      }

      // Orden por defecto
      var defaultOrder = [];
      if (Number.isInteger(orderCol)) {
        defaultOrder = [[ orderCol, (orderDir === 'desc' ? 'desc' : 'asc') ]];
      } else {
        // fallback: si hay al menos 2 columnas y la primera es numeración, ordenar por la segunda
        var numCols = $table.find('thead th').length;
        if (numCols > 1) defaultOrder = [[ tieneNumeracion ? 1 : 0, 'asc' ]];
      }

      // Inicializa DataTable
      var dt = $table.DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        language: {
          url: "https://cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json"
        },
        columnDefs: columnDefs,
        order: defaultOrder,
        drawCallback: function () {
          activarTooltips(); // reactivar tooltips tras redraw
        }
      });

      // Numeración automática en la primera col si aplica
      if (tieneNumeracion) {
        dt.on('order.dt search.dt draw.dt', function () {
          let i = 1;
          dt.cells(null, 0, { search: 'applied', order: 'applied' }).every(function () {
            this.data(i++);
          });
        }).draw();
      }

    });

  });
})(jQuery);

