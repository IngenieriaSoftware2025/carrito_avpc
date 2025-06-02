<div class="container py-5">

    <div class="row mb-5 justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body" style="background: linear-gradient(135deg, #f8fafc 40%, #c6e1f7 100%);">
                    <div class="mb-4 text-center">
                        <h5 class="fw-bold text-secondary mb-2">¡Sistema de Facturación!</h5>
                        <h3 class="fw-bold mb-0" style="color: #1e5f8a;">CARRITO DE COMPRAS</h3>
                    </div>
                    
                  
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="cliente_id" class="form-label fw-semibold">Cliente</label>
                            <select id="cliente_id" class="form-select form-select-lg rounded-3">
                                <option value="">Seleccione un cliente</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="card bg-light w-100 p-3 rounded-3">
                                <h5 class="mb-1">Total: <span class="fw-bold text-success" id="total-general">Q0.00</span></h5>
                                <small class="text-muted">Productos: <span id="items-count">0</span></small>
                            </div>
                        </div>
                    </div>

             
                    <div class="card bg-white rounded-4 shadow-sm border mb-4">
                        <div class="card-header bg-primary text-white rounded-top">
                            <h5 class="mb-0"><i class="bi bi-bag me-2"></i>Productos Disponibles</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="TableProductosDisponibles">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio</th>
                                            <th>Stock</th>
                                            <th>Categoría</th>
                                            <th>Cantidad</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productos-disponibles">
                                     
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                   
                    <div class="card bg-white rounded-4 shadow-sm border mb-4">
                        <div class="card-header bg-success text-white rounded-top">
                            <h5 class="mb-0"><i class="bi bi-cart3 me-2"></i>Carrito de Compras</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="TableCarrito">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio Unit.</th>
                                            <th>Cantidad</th>
                                            <th>Subtotal</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="carrito-items">
                                        <tr id="carrito-vacio">
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="bi bi-cart-x display-6"></i>
                                                <p class="mt-2">El carrito está vacío</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

               
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <button class="btn btn-success btn-lg px-4 shadow-sm rounded-pill" id="BtnProcesarVenta" disabled>
                            <i class="bi bi-check-circle me-2"></i>Procesar Venta
                        </button>
                        <button class="btn btn-warning btn-lg px-4 shadow-sm rounded-pill" id="BtnLimpiarCarrito">
                            <i class="bi bi-trash me-2"></i>Limpiar Carrito
                        </button>
                        <button class="btn btn-info btn-lg px-4 shadow-sm rounded-pill" id="BtnVerFacturas">
                            <i class="bi bi-receipt me-2"></i>Ver Facturas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row justify-content-center mt-5" id="SeccionFacturas" style="display: none;">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-3" style="border-left: 5px solid #28a745 !important;">
                <div class="card-body">
                    <h3 class="text-center mb-4">FACTURAS GENERADAS</h3>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered align-middle rounded-3 overflow-hidden" id="TableFacturas">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="ModalDetalleFactura" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Detalle de Factura</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenido-factura">
             
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="BtnImprimirFactura">
                    <i class="bi bi-printer me-2"></i>Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="<?= asset('build/js/facturas/index.js') ?>"></script></div>
