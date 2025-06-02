<div class="container py-5">
    <div class="row mb-5 justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body" style="background: linear-gradient(135deg, #f8fafc 40%, #e8f5e8 100%);">
                    <div class="mb-4 text-center">
                        <h5 class="fw-bold text-secondary mb-2">¡Sistema de Gestión de Clientes!</h5>
                        <h3 class="fw-bold mb-0" style="color: #2e7d32;">REGISTRO DE CLIENTES</h3>
                    </div>
                    <form id="FormClientes" class="p-4 bg-white rounded-4 shadow-sm border">
                        <input type="hidden" id="cliente_id" name="cliente_id">
                        <div class="row g-4 mb-3">
                            <div class="col-md-6">
                                <label for="cliente_nombre" class="form-label fw-semibold">Nombre</label>
                                <input type="text" class="form-control form-control-lg rounded-3" id="cliente_nombre" name="cliente_nombre">
                            </div>
                            <div class="col-md-6">
                                <label for="cliente_apellido" class="form-label fw-semibold">Apellido</label>
                                <input type="text" class="form-control form-control-lg rounded-3" id="cliente_apellido" name="cliente_apellido">
                            </div>
                        </div>
                        <div class="row g-4 mb-3">
                            <div class="col-md-6">
                                <label for="cliente_nit" class="form-label fw-semibold">NIT</label>
                                <input type="text" class="form-control form-control-lg rounded-3" id="cliente_nit" name="cliente_nit">
                            </div>
                            <div class="col-md-6">
                                <label for="cliente_email" class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control form-control-lg rounded-3" id="cliente_email" name="cliente_email">
                            </div>
                        </div>
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="cliente_telefono" class="form-label fw-semibold">Teléfono</label>
                                <input type="text" class="form-control form-control-lg rounded-3" id="cliente_telefono" name="cliente_telefono">
                            </div>
                            <div class="col-md-6">
                                <label for="cliente_direccion" class="form-label fw-semibold">Dirección</label>
                                <textarea class="form-control form-control-lg rounded-3" id="cliente_direccion" name="cliente_direccion" rows="1"></textarea>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <button class="btn btn-lg px-4 shadow-sm rounded-pill" type="submit" id="BtnGuardar" style="background-color: #2e7d32; color: white;">
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
            <div class="card shadow-lg border-0 rounded-3" style="border-left: 5px solid #2e7d32 !important;">
                <div class="card-body">
                    <h3 class="text-center mb-4">LISTADO DE CLIENTES</h3>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered align-middle rounded-3 overflow-hidden" id="TableClientes">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="<?= asset('build/js/clientes/index.js') ?>"></script>