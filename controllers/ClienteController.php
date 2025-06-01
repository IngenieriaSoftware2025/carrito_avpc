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

        // Validar nombre del cliente
        $_POST['cliente_nombre'] = htmlspecialchars($_POST['cliente_nombre']);
        $cantidad_nombre = strlen($_POST['cliente_nombre']);

        if ($cantidad_nombre < 3) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del cliente debe tener al menos 3 caracteres'
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

        // Sanitizar campos 
        $_POST['cliente_direccion'] = htmlspecialchars($_POST['cliente_direccion'] ?? '');
        $_POST['cliente_telefono'] = htmlspecialchars($_POST['cliente_telefono'] ?? '');

        try {
            $data = new Clientes([
                'cliente_nombre' => $_POST['cliente_nombre'],
                'cliente_nit' => $_POST['cliente_nit'],
                'cliente_direccion' => $_POST['cliente_direccion'],
                'cliente_telefono' => $_POST['cliente_telefono'],
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
            $sql = "SELECT * FROM clientes 
                    WHERE cliente_situacion = 1 
                    ORDER BY cliente_nombre";
            
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

        // Validar nombre del cliente
        $_POST['cliente_nombre'] = htmlspecialchars($_POST['cliente_nombre']);
        $cantidad_nombre = strlen($_POST['cliente_nombre']);

        if ($cantidad_nombre < 3) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del cliente debe tener al menos 3 caracteres'
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

       

        // Sanitizar campos 
        $_POST['cliente_direccion'] = htmlspecialchars($_POST['cliente_direccion'] ?? '');
        $_POST['cliente_telefono'] = htmlspecialchars($_POST['cliente_telefono'] ?? '');

        try {
            $data = Clientes::find($id);
            $data->sincronizar([
                'cliente_nombre' => $_POST['cliente_nombre'],
                'cliente_nit' => $_POST['cliente_nit'],
                'cliente_direccion' => $_POST['cliente_direccion'],
                'cliente_telefono' => $_POST['cliente_telefono'],
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

            $ejecutar = Clientes::eliminarCliente($id);

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