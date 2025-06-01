<div class="container py-5">
    <div class="row mb-5 justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body" style="background: linear-gradient(135deg, #f8fafc 40%, #c6e1f7 100%);">
                    <div class="mb-4 text-center">
                        <h5 class="fw-bold text-secondary mb-2">¡Sistema de Gestión de Productos!</h5>
                        <h3 class="fw-bold mb-0" style="color: #1e5f8a;">INVENTARIO DE PRODUCTOS</h3>
                    </div>
                    <form id="FormProductos" class="p-4 bg-white rounded-4 shadow-sm border">
                        <input type="hidden" id="producto_id" name="producto_id">
                        <div class="row g-4 mb-3">
                            <div class="col-md-6">
                                <label for="producto_nombre" class="form-label fw-semibold">Nombre del Producto</label>
                                <input type="text" class="form-control form-control-lg rounded-3" id="producto_nombre" name="producto_nombre" >
                            </div>
                            <div class="col-md-6">
                                <label for="producto_precio" class="form-label fw-semibold">Precio</label>
                                <input type="number" step="0.01" min="0" class="form-control form-control-lg rounded-3" id="producto_precio" name="producto_precio">
                            </div>
                        </div>
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="producto_categoria_id" class="form-label fw-semibold">Categoría</label>
                                <select name="producto_categoria_id" class="form-select form-select-lg rounded-3" id="producto_categoria_id">
                                    <option value="">Seleccione categoría</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="producto_stock" class="form-label fw-semibold">Stock</label>
                                <input type="number" min="0" class="form-control form-control-lg rounded-3" id="producto_stock" name="producto_stock">
                            </div>
                        </div>
                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <button class="btn btn-lg px-4 shadow-sm rounded-pill" type="submit" id="BtnGuardar" style="background-color: #1e5f8a; color: white;">
                                <i class="bi bi-check-lg me-2"></i>Guardar
                            </button>
                            <button class="btn btn-warning btn-lg px-4 shadow-sm rounded-pill d-none" type="button" id="BtnModificar">
                                <i class="bi bi-pen me-2"></i>Modificar
                            </button>
                            <button class="btn btn-secondary btn-lg px-4 shadow-sm rounded-pill" type="reset" id="BtnLimpiar">
                               <i class="bi bi-magic me-2"></i>Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-5">
        <div class="col-11">
            <div class="card shadow-lg border-0 rounded-3" style="border-left: 5px solid #1e5f8a !important;">
                <div class="card-body">
                    <h3 class="text-center mb-4">LISTADO DE PRODUCTOS</h3>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered align-middle rounded-3 overflow-hidden" id="TableProductos">
                    
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="<?= asset('build/js/productos/index.js') ?>"></script></div>

