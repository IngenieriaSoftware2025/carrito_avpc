<div class="container mt-4">

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-1">¡Bienvenido al Sistema de Ventas!</h5>
                    <h4 class="mb-0">CARRITO DE COMPRAS</h4>
                </div>
                <div class="card-body">
                    <form id="FormVentas">
                        <input type="hidden" id="venta_id" name="venta_id">
         
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <label for="venta_cliente_id" class="form-label">Seleccionar Cliente</label>
                                <select class="form-select" id="venta_cliente_id" name="venta_cliente_id" required>
                                    <option value="">-- Seleccione un cliente --</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn btn-info w-100" id="BtnCargarProductos">
                                    <i class="bi bi-cart-plus me-1"></i>Cargar Productos
                                </button>
                            </div>
                        </div>

                       
                        <div id="seccionProductos" style="display: none;">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Productos Disponibles</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="5%">Seleccionar</th>
                                                    <th width="35%">Producto</th>
                                                    <th width="15%">Precio</th>
                                                    <th width="10%">Stock</th>
                                                    <th width="15%">Cantidad</th>
                                                    <th width="20%">Descripción</th>
                                                    <th width="10%">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody id="productosDisponibles">
                                               
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div id="seccionCarrito" style="display: none;">
                            <div class="card mb-4">
                                <div class="card-header bg-success  text-dark">
                                    <h5 class="mb-0">Carrito de Compras</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-info">
                                                <tr>
                                                    <th>Producto</th>
                                                    <th>Precio Unitario</th>
                                                    <th>Cantidad</th>
                                                    <th>Subtotal</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody id="carritoItems">
                                                
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-info">
                                                    <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                                                    <td><strong><span id="totalVenta">Q. 0.00</span></strong></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button class="btn btn-success me-2" type="submit" id="BtnGuardarVenta" style="display: none;">
                                <i class="bi bi-save me-1"></i>Guardar Venta
                            </button>
                            <button class="btn btn-warning me-2" type="button" id="BtnModificarVenta" style="display: none;">
                                <i class="bi bi-pencil-square me-1"></i>Modificar Venta
                            </button>
                            <button class="btn btn-secondary" type="button" id="BtnLimpiarVenta">
                                <i class="bi bi-arrow-clockwise me-1"></i>Limpiar Todo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="text-center mb-0">Ventas Realizadas</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="TableVentas">
                          
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalDetalleVenta" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoDetalleVenta">
               
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<script src="<?= asset('build/js/ventas/index.js') ?>"></script>