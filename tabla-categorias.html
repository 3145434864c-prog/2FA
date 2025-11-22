<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- Encabezado de página -->
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0 text-gray-800">
      <i class="fas fa-tags me-2 text-primary"></i> Categorías
    </h1>

    <!-- Botón: Agregar categoría (abre modal) -->
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCategoria">
      <i class="fas fa-plus me-1"></i> Nueva categoría
    </button>
  </div>

  <!-- Descripción -->
  <p class="mb-4">
    Gestiona las categorías de productos desde esta sección. Utiliza el botón <strong>“Nueva categoría”</strong> para añadir una nueva.
  </p>

  <!-- Tabla -->
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex align-items-center">
      <h6 class="m-0 font-weight-bold text-primary">
        <i class="fas fa-list me-2"></i> Listado de categorías
      </h6>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered align-middle tablas" id="tablaCategorias" width="100%" cellspacing="0">
          <thead class="thead-light">
            <tr>
              <th style="width: 60px;">#</th>
              <th><i class="fas fa-tag me-1 text-secondary"></i> Nombre de la categoría</th>
              <th style="width: 140px;"><i class="fas fa-cogs me-1 text-secondary"></i> Acciones</th>
            </tr>
          </thead>
          <tbody>
            <!-- Registros de ejemplo -->
            <tr>
              <td></td>
              <td>Pintura interior</td>
              <td class="text-nowrap text-center">
                <button class="btn btn-outline-primary btn-sm me-1" data-bs-toggle="tooltip" title="Editar categoría">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Eliminar categoría">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>Pintura exterior</td>
              <td class="text-nowrap text-center">
                <button class="btn btn-outline-primary btn-sm me-1" data-bs-toggle="tooltip" title="Editar categoría">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Eliminar categoría">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
            <tr>
              <td></td>
              <td>Impermeabilizantes</td>
              <td class="text-nowrap text-center">
                <button class="btn btn-outline-primary btn-sm me-1" data-bs-toggle="tooltip" title="Editar categoría">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Eliminar categoría">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
          <tfoot class="thead-light">
            <tr>
              <th>#</th>
              <th>Nombre de la categoría</th>
              <th>Acciones</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->

<!-- Modal: Agregar/Editar categoría -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalCategoriaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formCategoria" class="modal-content" autocomplete="off">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCategoriaLabel">
          <i class="fas fa-plus me-1 text-primary"></i> Nueva categoría
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="inputCategoria" class="form-label">
            <i class="fas fa-tag me-1 text-secondary"></i> Nombre de la categoría
          </label>
          <input type="text" class="form-control" id="inputCategoria" name="categoria" placeholder="Ejemplo: Selladores" required>
          <div class="invalid-feedback">Por favor, escribe un nombre válido.</div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Cancelar
        </button>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save me-1"></i> Guardar
        </button>
      </div>
      
    </form>
  </div>
</div>

<!-- Inicialización DataTables + Tooltips + Alta rápida desde el modal -->
<script>
//   document.addEventListener('DOMContentLoaded', function () {
//     // Inicializa tooltips de Bootstrap
//     $('[data-bs-toggle="tooltip"], [data-toggle="tooltip"]').tooltip();

//     if (!$.fn.DataTable.isDataTable('#tablaCategorias')) {
//       const tabla = $('#tablaCategorias').DataTable({
//         responsive: true,
//         pageLength: 10,
//         lengthMenu: [10, 25, 50, 100],
//         language: {
//           url: "//cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json"
//         },
//         columnDefs: [
//           { targets: 0, orderable: false, searchable: false },
//           { targets: 2, orderable: false, searchable: false }
//         ],
//         order: [[1, 'asc']],
//         drawCallback: function () {
//           $('[data-bs-toggle="tooltip"], [data-toggle="tooltip"]').tooltip();
//         }
//       });

//       // Numeración automática
//       tabla.on('order.dt search.dt draw.dt', function () {
//         let i = 1;
//         tabla.cells(null, 0, { search: 'applied', order: 'applied' }).every(function () {
//           this.data(i++);
//         });
//       }).draw();

//       // Alta rápida desde el modal
//       const form = document.getElementById('formCategoria');
//       form.addEventListener('submit', function (e) {
//         e.preventDefault();
//         const input = document.getElementById('inputCategoria');
//         const nombre = input.value.trim();

//         if (!nombre) {
//           input.classList.add('is-invalid');
//           return;
//         }
//         input.classList.remove('is-invalid');

//         const accionesHTML = `
//           <button class="btn btn-outline-primary btn-sm me-1" data-bs-toggle="tooltip" title="Editar categoría">
//             <i class="fas fa-edit"></i>
//           </button>
//           <button class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Eliminar categoría">
//             <i class="fas fa-trash"></i>
//           </button>
//         `;

//         tabla.row.add(['', nombre, accionesHTML]).draw(false);

//         input.value = '';
//         const modalEl = document.getElementById('modalCategoria');
//         const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
//         modal.hide();

//         $('[data-bs-toggle="tooltip"], [data-toggle="tooltip"]').tooltip();
//       });
//     }
//   });
</script>
