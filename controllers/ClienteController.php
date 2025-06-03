<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Clientes;
use MVC\Router;

class ClienteController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        $router->render('clientes/index', []);
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        $_POST['cliente_apellidos'] = htmlspecialchars($_POST['cliente_apellidos']);
        $cantidad_apellidos = strlen($_POST['cliente_apellidos']);

        if ($cantidad_apellidos < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Los apellidos deben tener al menos 2 caracteres'
            ]);
            return;
        }

        $_POST['cliente_nombres'] = htmlspecialchars($_POST['cliente_nombres']);
        $cantidad_nombres = strlen($_POST['cliente_nombres']);

        if ($cantidad_nombres < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Los nombres deben tener al menos 2 caracteres'
            ]);
            return;
        }

        $_POST['cliente_telefono'] = filter_var($_POST['cliente_telefono'], FILTER_VALIDATE_INT);
        if (strlen($_POST['cliente_telefono']) != 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El teléfono debe tener exactamente 8 dígitos'
            ]);
            return;
        }

        $_POST['cliente_nit'] = filter_var($_POST['cliente_nit'], FILTER_SANITIZE_NUMBER_INT);
        $_POST['cliente_correo'] = filter_var($_POST['cliente_correo'], FILTER_SANITIZE_EMAIL);

        if (!filter_var($_POST['cliente_correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electrónico ingresado es inválido'
            ]);
            return;
        }

        // Verificar que el correo no exista
        $correo_existe = Clientes::BuscarPorCorreo($_POST['cliente_correo']);
        if ($correo_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe un cliente con este correo electrónico'
            ]);
            return;
        }

        $_POST['cliente_estado'] = htmlspecialchars($_POST['cliente_estado']);
        $_POST['cliente_fecha'] = date('Y-m-d H:i', strtotime($_POST['cliente_fecha']));

        $estado = $_POST['cliente_estado'];
        if ($estado == "A" || $estado == "I") {
            try {
                $data = new Clientes([
                    'cliente_nombres' => $_POST['cliente_nombres'],
                    'cliente_apellidos' => $_POST['cliente_apellidos'],
                    'cliente_nit' => $_POST['cliente_nit'],
                    'cliente_telefono' => $_POST['cliente_telefono'],
                    'cliente_correo' => $_POST['cliente_correo'],
                    'cliente_estado' => $_POST['cliente_estado'],
                    'cliente_fecha' => $_POST['cliente_fecha'],
                    'cliente_situacion' => 1
                ]);

                $crear = $data->crear();

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'El cliente ha sido registrado correctamente'
                ]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al guardar el cliente',
                    'detalle' => $e->getMessage(),
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El estado solo puede ser "A" (Activo) o "I" (Inactivo)'
            ]);
            return;
        }
    }

    public static function buscarAPI()
    {
        try {
            $sql = "SELECT * FROM clientes WHERE cliente_situacion = 1 ORDER BY cliente_nombres";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Clientes obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los clientes',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();

        $id = $_POST['cliente_id'];
        $_POST['cliente_apellidos'] = htmlspecialchars($_POST['cliente_apellidos']);

        $cantidad_apellidos = strlen($_POST['cliente_apellidos']);
        if ($cantidad_apellidos < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Los apellidos deben tener al menos 2 caracteres'
            ]);
            return;
        }

        $_POST['cliente_nombres'] = htmlspecialchars($_POST['cliente_nombres']);
        $cantidad_nombres = strlen($_POST['cliente_nombres']);

        if ($cantidad_nombres < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Los nombres deben tener al menos 2 caracteres'
            ]);
            return;
        }

        $_POST['cliente_telefono'] = filter_var($_POST['cliente_telefono'], FILTER_VALIDATE_INT);
        if (strlen($_POST['cliente_telefono']) != 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El teléfono debe tener exactamente 8 dígitos'
            ]);
            return;
        }

        $_POST['cliente_nit'] = filter_var($_POST['cliente_nit'], FILTER_SANITIZE_NUMBER_INT);
        $_POST['cliente_correo'] = filter_var($_POST['cliente_correo'], FILTER_SANITIZE_EMAIL);
        $_POST['cliente_fecha'] = date('Y-m-d H:i', strtotime($_POST['cliente_fecha']));

        if (!filter_var($_POST['cliente_correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electrónico ingresado es inválido'
            ]);
            return;
        }

        // Verificar que el correo no exista en otro cliente
        $correo_existe = self::fetchFirst("SELECT cliente_id FROM clientes WHERE cliente_correo = " . self::$db->quote($_POST['cliente_correo']) . " AND cliente_id != $id AND cliente_situacion = 1");
        if ($correo_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe otro cliente con este correo electrónico'
            ]);
            return;
        }

        $_POST['cliente_estado'] = htmlspecialchars($_POST['cliente_estado']);

        $estado = $_POST['cliente_estado'];
        if ($estado == "A" || $estado == "I") {
            try {
                $data = Clientes::find($id);
                $data->sincronizar([
                    'cliente_nombres' => $_POST['cliente_nombres'],
                    'cliente_apellidos' => $_POST['cliente_apellidos'],
                    'cliente_nit' => $_POST['cliente_nit'],
                    'cliente_telefono' => $_POST['cliente_telefono'],
                    'cliente_correo' => $_POST['cliente_correo'],
                    'cliente_estado' => $_POST['cliente_estado'],
                    'cliente_fecha' => $_POST['cliente_fecha'],
                    'cliente_situacion' => 1
                ]);
                $data->actualizar();

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'La información del cliente ha sido modificada exitosamente'
                ]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al modificar el cliente',
                    'detalle' => $e->getMessage(),
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El estado solo puede ser "A" (Activo) o "I" (Inactivo)'
            ]);
            return;
        }
    }

    public static function EliminarAPI()
    {
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

           
            $ventas_cliente = self::fetchFirst("SELECT COUNT(*) as total FROM ventas WHERE venta_cliente_id = $id AND venta_situacion = 1");
            
            if ($ventas_cliente['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar el cliente porque tiene ventas registradas',
                    'detalle' => "El cliente tiene {$ventas_cliente['total']} ventas asociadas"
                ]);
                return;
            }

            $ejecutar = Clientes::EliminarCliente($id);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El cliente ha sido eliminado correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el cliente',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function clientesActivosAPI()
    {
        try {
            $data = Clientes::ObtenerClientesActivos();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Clientes activos obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los clientes activos',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}