<div class="container mt-4">

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-1">¡Bienvenido a la Aplicación para el registro, modificación y eliminación de productos!</h5>
                    <h4 class="mb-0">MANIPULACIÓN DE PRODUCTOS</h4>
                </div>
                <div class="card-body">
                    <form id="FormProductos">
                        <input type="hidden" id="producto_id" name="producto_id">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="producto_nombre" class="form-label">Nombre del Producto</label>
                                <input type="text" class="form-control" id="producto_nombre" name="producto_nombre" placeholder="Ingrese el nombre del producto" required>
                            </div>
                            <div class="col-md-6">
                                <label for="producto_precio" class="form-label">Precio</label>
                                <input type="number" step="0.01" class="form-control" id="producto_precio" name="producto_precio" placeholder="0.00" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="producto_cantidad" class="form-label">Cantidad</label>
                                <input type="number" class="form-control" id="producto_cantidad" name="producto_cantidad" placeholder="Cantidad disponible" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="producto_descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="producto_descripcion" name="producto_descripcion" rows="3" placeholder="Ingrese la descripción del producto" required></textarea>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button class="btn btn-success me-2" type="submit" id="BtnGuardar">
                                Guardar
                            </button>
                            <button class="btn btn-warning me-2 d-none" type="button" id="BtnModificar">
                                Modificar
                            </button>
                            <button class="btn btn-secondary" type="reset" id="BtnLimpiar">
                                Limpiar
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
                    <h4 class="text-center mb-0">Productos registrados en la base de datos</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="TableProductos">
                            
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="<?= asset('build/js/productos/index.js') ?>"></script>