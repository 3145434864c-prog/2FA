<?php

$productos  = ControladorProductos::mostrarProductos();
$categorias = ControladorProductos::listarCategorias();
?>
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- Header -->
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h3 mb-0 text-gray-800">
      <i class="fas fa-box me-2 text-primary"></i> Productos
    </h1>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalProducto">
      <i class="fas fa-plus me-1"></i> Nuevo producto
    </button>
  </div>

  <p class="mb-4">Gestiona tus productos. Puedes subir una imagen (JPG, PNG o WEBP, máx 3 MB).</p>

  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex align-items-center">
      <h6 class="m-0 font-weight-bold text-primary">
        <i class="fas fa-list me-2"></i> Listado de productos
      </h6>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered align-middle tablas" id="tablaProductos" width="100%" cellspacing="0">
          <thead class="thead-light">
            <tr>
              <th style="width:60px;">#</th>
              <th style="width:100px;">Referencia</th>
              <th style="width:90px;">Imagen</th>
              <th>Nombre</th>
              <th>Categoría</th>
              <th class="text-end" style="width:120px;">Precio</th>
              <th class="text-end" style="width:90px;">Stock</th>
              <th style="width:160px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($productos)): $i=1; foreach ($productos as $p): 
              $id   = (int)$p['id_producto'];
              $img  = htmlspecialchars($p['imagen'] ?: '', ENT_QUOTES, 'UTF-8');
              $nom  = htmlspecialchars($p['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
              $cat  = htmlspecialchars($p['nombre_categoria'] ?? '', ENT_QUOTES, 'UTF-8');
              $precio = number_format((float)$p['precio'], 2, '.', ',');
              $stock  = (int)$p['stock'];
              $imgSrc = $img ?: 'https://via.placeholder.com/80x80?text=IMG';
            ?>
            <tr data-id="<?= $id ?>"
                data-nombre="<?= $nom ?>"
                data-descripcion="<?= htmlspecialchars($p['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                data-categoria="<?= (int)$p['id_categoria'] ?>"
                data-precio="<?= (float)$p['precio'] ?>"
                data-stock="<?= $stock ?>"
                data-imagen="<?= $img ?>">
              <td class="text-center"><?= $i++ ?></td>
<td class="text-center"><?= htmlspecialchars($p['referencia'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
<td class="text-center">
  <img src="<?= $imgSrc ?>" alt="img" class="rounded" style="width:64px;height:64px;object-fit:cover;">
</td>

              <td><?= $nom ?></td>
              <td><?= $cat ?></td>
              <td class="text-end">$ <?= $precio ?></td>
              <td class="text-end"><?= $stock ?></td>
              <td class="text-center text-nowrap">
                <button class="btn btn-outline-primary btn-sm me-1" data-action="edit" data-bs-toggle="tooltip" title="Editar">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm" data-action="delete" data-bs-toggle="tooltip" title="Eliminar">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="7" class="text-center text-muted">No hay productos registrados.</td></tr>
            <?php endif; ?>
          </tbody>
          <tfoot class="thead-light">
  <tr>
    <th>#</th>
    <th>Referencia</th>
    <th>Imagen</th>
    <th>Nombre</th>
    <th>Categoría</th>
    <th class="text-end">Precio</th>
    <th class="text-end">Stock</th>
    <th>Acciones</th>
  </tr>
</tfoot>

        </table>
      </div>
    </div>
  </div>
</div>

<!-- ========== FORM oculto eliminar ========== -->
<form id="formEliminarProducto" method="post" class="d-none">
  <input type="hidden" name="id_producto_eliminar" id="inputEliminarProducto">
</form>

<!-- ===================== MODAL: CREAR ===================== -->
<div class="modal fade" id="modalProducto" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" id="formProducto" method="post" enctype="multipart/form-data" autocomplete="off">
      <div class="modal-header">
        <h5 class="modal-title" id="modalProductoLabel">
          <i class="fas fa-plus me-1 text-primary"></i> Nuevo producto
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label"><i class="fas fa-tag me-1 text-secondary"></i> Nombre</label>
            <input type="text" class="form-control" name="nombre" required>
          </div>
          <div class="col-md-6">
            <label class="form-label"><i class="fas fa-sitemap me-1 text-secondary"></i> Categoría</label>
            <select class="form-select" name="id_categoria" required>
              <option value="">-- Selecciona --</option>
              <?php foreach ($categorias as $c): ?>
                <option value="<?= (int)$c['id_categoria'] ?>">
                  <?= htmlspecialchars($c['nombre_categoria'], ENT_QUOTES, 'UTF-8') ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-8">
            <label class="form-label"><i class="fas fa-align-left me-1 text-secondary"></i> Descripción</label>
            <textarea class="form-control" name="descripcion" rows="3"></textarea>
          </div>
          <div class="col-md-2">
            <label class="form-label"><i class="fas fa-dollar-sign me-1 text-secondary"></i> Precio</label>
            <input type="number" step="0.01" min="0" class="form-control" name="precio" value="0.00" required>
          </div>
          <div class="col-md-2">
            <label class="form-label"><i class="fas fa-boxes me-1 text-secondary"></i> Stock</label>
            <input type="number" min="0" class="form-control" name="stock" value="0" required>
          </div>

          <div class="col-12">
            <label class="form-label"><i class="fas fa-image me-1 text-secondary"></i> Imagen (JPG/PNG/WEBP, máx 3 MB)</label>
            <input type="file" class="form-control" name="imagen" accept=".jpg,.jpeg,.png,.webp">
          </div>
        </div>

        <?php
          $crear = new ControladorProductos();
          $crear->crearProducto();
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- ===================== MODAL: EDITAR ===================== -->
<div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-labelledby="modalEditarProductoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" id="formEditarProducto" method="post" enctype="multipart/form-data" autocomplete="off">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarProductoLabel">
          <i class="fas fa-edit me-1 text-primary"></i> Editar producto
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_producto" id="editIdProducto">

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" id="editNombre" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Categoría</label>
            <select class="form-select" name="id_categoria" id="editCategoria" required>
              <option value="">-- Selecciona --</option>
              <?php foreach ($categorias as $c): ?>
                <option value="<?= (int)$c['id_categoria'] ?>">
                  <?= htmlspecialchars($c['nombre_categoria'], ENT_QUOTES, 'UTF-8') ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-8">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" id="editDescripcion" rows="3"></textarea>
          </div>
          <div class="col-md-2">
            <label class="form-label">Precio</label>
            <input type="number" step="0.01" min="0" class="form-control" name="precio" id="editPrecio" required>
          </div>
          <div class="col-md-2">
            <label class="form-label">Stock</label>
            <input type="number" min="0" class="form-control" name="stock" id="editStock" required>
          </div>

          <div class="col-12">
            <label class="form-label">Imagen (opcional, reemplaza la actual)</label>
            <input type="file" class="form-control" name="imagen" accept=".jpg,.jpeg,.png,.webp">
            <div class="form-text">Si subes una nueva, se eliminará la anterior.</div>
          </div>
        </div>

        <?php
          $editar = new ControladorProductos();
          $editar->editarProducto();
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Actualizar</button>
      </div>
    </form>
  </div>
</div>

<?php
  $eliminar = new ControladorProductos();
  $eliminar->eliminarProducto();
?>

<!-- ===================== SCRIPTS ===================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Tooltips
  ([...document.querySelectorAll('[data-bs-toggle="tooltip"]')]).forEach(el => new bootstrap.Tooltip(el));

  const tabla = document.getElementById('tablaProductos');

  // Abrir modal EDICIÓN con datos de la fila
  tabla?.addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-action="edit"]');
    if (!btn) return;
    const tr = btn.closest('tr');
    if (!tr) return;

    document.getElementById('editIdProducto').value   = tr.dataset.id;
    document.getElementById('editNombre').value       = tr.dataset.nombre || '';
    document.getElementById('editDescripcion').value  = tr.dataset.descripcion || '';
    document.getElementById('editPrecio').value       = tr.dataset.precio || '0.00';
    document.getElementById('editStock').value        = tr.dataset.stock || '0';
    // categoría
    const sel = document.getElementById('editCategoria');
    if (sel) sel.value = tr.dataset.categoria || '';

    const modalEl = document.getElementById('modalEditarProducto');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
  });

  // Eliminar con SweetAlert
  const formEliminar = document.getElementById('formEliminarProducto');
  const inputEliminar = document.getElementById('inputEliminarProducto');

  tabla?.addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-action="delete"]');
    if (!btn) return;
    const tr = btn.closest('tr');
    const nombre = tr?.dataset?.nombre || 'este producto';
    const id = tr?.dataset?.id;

    if (window.Swal) {
      Swal.fire({
        title: '¿Eliminar producto?',
        text: `Se eliminará "${nombre}". Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then(r=>{
        if(!r.isConfirmed) return;
        inputEliminar.value = id;
        formEliminar.submit();
      });
    } else {
      if (confirm(`¿Eliminar "${nombre}"?`)) {
        inputEliminar.value = id;
        formEliminar.submit();
      }
    }
  });
});
</script>
