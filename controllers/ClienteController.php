<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Clientes;
use MVC\Router;

class ClienteController extends ActiveRecord
{
    public function renderizarPagina(Router $router)
    {
        $router->render('clientes/index', []);
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        // Validar nombre
        $_POST['cliente_nombre'] = htmlspecialchars($_POST['cliente_nombre']);
        $cantidad_nombre = strlen($_POST['cliente_nombre']);

        if ($cantidad_nombre < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del cliente debe tener al menos 2 caracteres'
            ]);
            return;
        }

        // Validar apellido
        $_POST['cliente_apellido'] = htmlspecialchars($_POST['cliente_apellido']);
        $cantidad_apellido = strlen($_POST['cliente_apellido']);

        if ($cantidad_apellido < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El apellido del cliente debe tener al menos 2 caracteres'
            ]);
            return;
        }

        // Validar NIT
        $_POST['cliente_nit'] = htmlspecialchars($_POST['cliente_nit']);
        if (empty($_POST['cliente_nit'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El NIT es obligatorio'
            ]);
            return;
        }

        // Verificar que el NIT no exista
        if (Clientes::existeNit($_POST['cliente_nit'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Este NIT ya está registrado'
            ]);
            return;
        }

        // Validar email (opcional)
        if (!empty($_POST['cliente_email'])) {
            $_POST['cliente_email'] = filter_var($_POST['cliente_email'], FILTER_VALIDATE_EMAIL);
            if (!$_POST['cliente_email']) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El formato del email no es válido'
                ]);
                return;
            }
        }

        // Sanitizar campos opcionales
        $_POST['cliente_telefono'] = htmlspecialchars($_POST['cliente_telefono'] ?? '');
        $_POST['cliente_direccion'] = htmlspecialchars($_POST['cliente_direccion'] ?? '');

        try {
            $data = new Clientes([
                'cliente_nombre' => $_POST['cliente_nombre'],
                'cliente_apellido' => $_POST['cliente_apellido'],
                'cliente_nit' => $_POST['cliente_nit'],
                'cliente_email' => $_POST['cliente_email'] ?? '',
                'cliente_telefono' => $_POST['cliente_telefono'],
                'cliente_direccion' => $_POST['cliente_direccion'],
                'cliente_situacion' => 1
            ]);

            $crear = $data->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El cliente ha sido agregado correctamente'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function buscarAPI()
    {
        try {
            $sql = "SELECT * FROM clientes WHERE cliente_situacion = 1 ORDER BY cliente_nombre, cliente_apellido";
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

        // Validar nombre
        $_POST['cliente_nombre'] = htmlspecialchars($_POST['cliente_nombre']);
        $cantidad_nombre = strlen($_POST['cliente_nombre']);

        if ($cantidad_nombre < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del cliente debe tener al menos 2 caracteres'
            ]);
            return;
        }

        // Validar apellido
        $_POST['cliente_apellido'] = htmlspecialchars($_POST['cliente_apellido']);
        $cantidad_apellido = strlen($_POST['cliente_apellido']);

        if ($cantidad_apellido < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El apellido del cliente debe tener al menos 2 caracteres'
            ]);
            return;
        }

        // Validar NIT
        $_POST['cliente_nit'] = htmlspecialchars($_POST['cliente_nit']);
        if (empty($_POST['cliente_nit'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El NIT es obligatorio'
            ]);
            return;
        }

        // Verificar que el NIT no exista (excluyendo el cliente actual)
        if (Clientes::existeNit($_POST['cliente_nit'], $id)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Este NIT ya está registrado'
            ]);
            return;
        }

        // Validar email (opcional)
        if (!empty($_POST['cliente_email'])) {
            $_POST['cliente_email'] = filter_var($_POST['cliente_email'], FILTER_VALIDATE_EMAIL);
            if (!$_POST['cliente_email']) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El formato del email no es válido'
                ]);
                return;
            }
        }

        // Sanitizar campos opcionales
        $_POST['cliente_telefono'] = htmlspecialchars($_POST['cliente_telefono'] ?? '');
        $_POST['cliente_direccion'] = htmlspecialchars($_POST['cliente_direccion'] ?? '');

        try {
            $data = Clientes::find($id);
            if (!$data) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Cliente no encontrado'
                ]);
                return;
            }

            $data->sincronizar([
                'cliente_nombre' => $_POST['cliente_nombre'],
                'cliente_apellido' => $_POST['cliente_apellido'],
                'cliente_nit' => $_POST['cliente_nit'],
                'cliente_email' => $_POST['cliente_email'] ?? '',
                'cliente_telefono' => $_POST['cliente_telefono'],
                'cliente_direccion' => $_POST['cliente_direccion'],
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
                'mensaje' => 'Error al guardar',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function eliminarAPI()
    {
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'ID de cliente inválido'
                ]);
                return;
            }

            // Verificar si el cliente tiene ventas
            $sql_verificar = "SELECT COUNT(*) as total FROM ventas WHERE venta_cliente_id = " . $id . " AND venta_situacion = 1";
            $verificacion = self::fetchFirst($sql_verificar);

            if ($verificacion['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar el cliente porque tiene ventas registradas'
                ]);
                return;
            }

            // Eliminar cliente (cambiar situación)
            $sql = "UPDATE clientes SET cliente_situacion = 0 WHERE cliente_id = " . $id;
            $ejecutar = self::SQL($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El cliente ha sido eliminado correctamente'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar',
                'detalle' => $e->getMessage(),
            ]);
        }
    }
}