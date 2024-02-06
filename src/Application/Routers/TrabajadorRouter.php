<?php

namespace App\Application\Routers;

use App\Application\Controllers\TrabajadorController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

/**
 * Class TrabajadorRouter.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Routers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class TrabajadorRouter
{
    /**
     * Constructor for the class.
     *
     * @param App $app The application instance.
     */
    public function __construct(App $app)
    {
        // Endpoint for getting all trabajadores.
        $app->get('/trabajadores', function (Request $request, Response $response) {
            $controller = new TrabajadorController();
            return $controller->index($response);
        });

        // Endpoint for getting a specific trabajador.
        $app->get('/trabajadores/{id}', function (Request $request, Response $response, array $args) {
            $controller = new TrabajadorController();
            return $controller->find($request, $response, $args);
        });

        // Endpoint for getting all clases for a trabajador.
        $app->get('/trabajadores/{id}/periodo-activo/clases', function (Request $request, Response $response, array $args) {
            $controller = new TrabajadorController();
            return $controller->clases($request, $response, $args);
        });

        // Endpoint for getting all clases by tipo for a trabajador.
        $app->get('/trabajadores/{id}/periodo-activo/clases/{tipo}', function (Request $request, Response $response, array $args) {
            $controller = new TrabajadorController();
            return $controller->clasesByTipo($request, $response, $args);
        });

        // Endpoint for getting all trabajadores matriculados.
        $app->get('/trabajadores/matriculados/{id}', function (Request $request, Response $response, array $args) {
            $controller = new TrabajadorController();
            return $controller->matriculados($request, $response, $args);
        });

        // Endpoint for creating a new trabajador.
        $app->post('/trabajadores', function (Request $request, Response $response) {
            $controller = new TrabajadorController();
            return $controller->store($request, $response);
        });

        // Endpoint for updating an existing trabajador.
        $app->put('/trabajadores/{id}', function (Request $request, Response $response, array $args) {
            $controller = new TrabajadorController();
            return $controller->update($request, $response, $args);
        });

        // Endpoint for deleting a trabajador.
        $app->delete('/trabajadores/{id}', function (Request $request, Response $response, array $args) {
            $controller = new TrabajadorController();
            return $controller->delete($request, $response, $args);
        });

        // Endpoint for deleting a multiple trabajadores.
        $app->post('/trabajadores/eliminar', function (Request $request, Response $response) {
            $controller = new TrabajadorController();
            return $controller->deleteMultiple($request, $response);
        });
    }
}
