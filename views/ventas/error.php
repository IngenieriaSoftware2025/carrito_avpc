<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Factura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="alert alert-danger text-center">
                    <h4><i class="bi bi-exclamation-triangle"></i> Error al generar la factura</h4>
                    <p>No se pudo generar la factura solicitada.</p>
                    <hr>
                    <p class="mb-0"><strong>Detalle:</strong> <?= htmlspecialchars($mensaje) ?></p>
                </div>
                
                <div class="text-center">
                    <button class="btn btn-primary" onclick="window.history.back()">
                        <i class="bi bi-arrow-left"></i> Volver
                    </button>
                    <button class="btn btn-secondary" onclick="window.close()">
                        <i class="bi bi-x-circle"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>