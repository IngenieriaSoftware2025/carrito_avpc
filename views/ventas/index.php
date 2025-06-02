<div class="container py-5">
    <div class="row mb-5 justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body" style="background: linear-gradient(135deg, #f8fafc 40%, #e1f5fe 100%);">
                    <div class="mb-4 text-center">
                        <h5 class="fw-bold text-secondary mb-2">¡Sistema de Gestión de Ventas!</h5>
                        <h3 class="fw-bold mb-0" style="color: #1565c0;">GENERAR FACTURA</h3>
                    </div>
                    <form id="FormVentas" class="p-4 bg-white rounded-4 shadow-sm border">
                        <input type="hidden" id="venta_id" name="venta_id">
                        
                        <!-- Selección de Cliente -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-12">
                                <label for="venta_cliente_id" class="form-label fw-semibold">Cliente</label>
                                <select name="venta_cliente_id" class="form-select form-select-lg rounded-3" id="venta_cliente_id">
                                    <option value="">Seleccione un cliente</option>
                                </select>
                            </div>
                        </div>

                        <!-- Selección de Productos -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-8">
                                <label for="producto_select" class="form-label fw-semibold">Producto</label>
                                <select class="form-select form-select-lg rounded-3" id="producto_select">
                                    <option value="">Seleccione un producto</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="cantidad_input" class="form-label fw-semibold">Cantidad</label>
                                <input type="number" class="form-control form-control-lg rounded-3" id="cantidad_input" min="1" value="1">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-success btn-lg rounded-3" id="BtnAgregarProducto">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Tabla de Productos Seleccionados -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="fw-bold text-secondary mb-3">Productos Seleccionados</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover rounded-3 overflow-hidden" id="TablaProductosSeleccionados">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>Producto</th>
                                                <th>Precio Unit.</th>
                                                <th>Cantidad</th>
                                                <th>Subtotal</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id="filaVacia">
                                                <td colspan="5" class="text-center text-muted">No hay productos seleccionados</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="row mb-4">
                            <div class="col-12 text-end">
                                <h4 class="fw-bold" style="color: #1565c0;">
                                    Total: Q <span id="totalVenta">0.00</span>
                                </h4>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <button class="btn btn-lg px-4 shadow-sm rounded-pill" type="submit" id="BtnGuardar" style="background-color: #1565c0; color: white;">
                                <i class="bi bi-check-lg me-2"></i>Guardar Venta
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

    <!-- Listado de Ventas -->
    <div class="row justify-content-center mt-5">
        <div class="col-11">
            <div class="card shadow-lg border-0 rounded-3" style="border-left: 5px solid #1565c0 !important;">
                <div class="card-body">
                    <h3 class="text-center mb-4">HISTORIAL DE VENTAS</h3>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered align-middle rounded-3 overflow-hidden" id="TableVentas">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalle de venta -->
<div class="modal fade" id="ModalDetalleVenta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoDetalle">
                <!-- Contenido se carga dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="<?= asset('build/js/ventas/index.js') ?>"></script>