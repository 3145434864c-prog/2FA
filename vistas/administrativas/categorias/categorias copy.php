<?php
// Asegúrate de tener cargado el controlador antes de usarlo:
// require_once "Controladores/ControladorCategorias.php";
?>
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
            <?php
              // Obtener categorías desde el controlador
              $categorias = ControladorCategorias::mostrarCategorias();

              if (!empty($categorias)) {
                $i = 1;
                foreach ($categorias as $cat) {
                  $id   = isset($cat['id_categoria']) ? (int)$cat['id_categoria'] : 0;
                  $name = htmlspecialchars($cat['nombre_categoria'] ?? '', ENT_QUOTES, 'UTF-8');

                  echo '<tr data-id="'.$id.'">
                          <td class="text-center">'.$i.'</td>
                          <td>'.$name.'</td>
                          <td class="text-nowrap text-center">
                            <button class="btn btn-outline-primary btn-sm me-1" data-bs-toggle="tooltip" title="Editar categoría" data-action="edit" data-id="'.$id.'">
                              <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Eliminar categoría" data-action="delete" data-id="'.$id.'">
                              <i class="fas fa-trash"></i>
                            </button>
                          </td>
                        </tr>';
                  $i++;
                }
              } else {
                echo '<tr>
                        <td colspan="3" class="text-center text-muted">
                          No hay categorías registradas.
                        </td>
                      </tr>';
              }
            ?>
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

<!-- ===================== MODAL: CREAR ===================== -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalCategoriaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formCategoria" class="modal-content" method="post" autocomplete="off">
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

        <?php
          // Procesa POST (crear) — método SEPARADO
          $crearCategoria = new ControladorCategorias();
          $crearCategoria->crearCategoria();
        ?>
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

<!-- ========== FORM OCULTO ELIMINAR (POST clásico) ========== -->
<form id="formEliminarCategoria" method="post" class="d-none">
  <input type="hidden" name="id_categoria_eliminar" id="inputEliminarId">
</form>

<!-- ===================== MODAL: EDITAR ===================== -->
<div class="modal fade" id="modalEditarCategoria" tabindex="-1" aria-labelledby="modalEditarCategoriaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formEditarCategoria" class="modal-content" method="post" autocomplete="off">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarCategoriaLabel">
          <i class="fas fa-edit me-1 text-primary"></i> Editar categoría
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <!-- ID oculto solo para edición -->
        <input type="hidden" name="id_categoria" id="editIdCategoria">

        <div class="mb-3">
          <label for="editInputCategoria" class="form-label">
            <i class="fas fa-tag me-1 text-secondary"></i> Nombre de la categoría
          </label>
          <input type="text" class="form-control" id="editInputCategoria" name="categoria" required>
          <div class="invalid-feedback">Por favor, escribe un nombre válido.</div>
        </div>

        <?php
          // Procesa POST (editar) — método SEPARADO
          $editarCategoria = new ControladorCategorias();
          $editarCategoria->editarCategoria();
        ?>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Cancelar
        </button>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save me-1"></i> Actualizar
        </button>
      </div>
    </form>
  </div>
</div>

<?php
  // Procesa POST (eliminar) — método SEPARADO
  $eliminarCategoria = new ControladorCategorias();
  $eliminarCategoria->eliminarCategoria();
?>

<!-- ===================== SCRIPTS ===================== -->
<script>
  // Tooltips globales
  document.addEventListener('DOMContentLoaded', function () {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (el) { new bootstrap.Tooltip(el); });
  });
</script>

<!-- Abrir modal de EDICIÓN con los datos de la fila -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const tabla = document.getElementById('tablaCategorias');

  tabla?.addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-action="edit"]');
    if (!btn) return;

    const id = btn.getAttribute('data-id');
    const tr = btn.closest('tr');
    const nombre = tr?.children[1]?.textContent?.trim() ?? '';

    // Setear campos del modal de edición
    document.getElementById('editIdCategoria').value = id;
    document.getElementById('editInputCategoria').value = nombre;

    // Mostrar modal de edición
    const modalEl = document.getElementById('modalEditarCategoria');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
  });
});
</script>

<!-- ELIMINAR con SweetAlert (o confirm nativo si no está SweetAlert) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const tabla = document.getElementById('tablaCategorias');
  const formEliminar = document.getElementById('formEliminarCategoria');
  const inputEliminarId = document.getElementById('inputEliminarId');

  tabla?.addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-action="delete"]');
    if (!btn) return;

    const id = btn.getAttribute('data-id');
    const tr = btn.closest('tr');
    const nombre = tr?.children[1]?.textContent?.trim() ?? '';

    if (window.Swal) {
      Swal.fire({
        title: '¿Eliminar categoría?',
        text: `Se eliminará "${nombre}". Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((r) => {
        if (!r.isConfirmed) return;
        inputEliminarId.value = id;
        formEliminar.submit();
      });
    } else {
      if (confirm(`¿Eliminar la categoría "${nombre}"?`)) {
        inputEliminarId.value = id;
        formEliminar.submit();
      }
    }
  });
});
</script>
