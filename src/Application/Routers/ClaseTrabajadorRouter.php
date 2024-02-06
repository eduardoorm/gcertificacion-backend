<?php

namespace App\Application\Routers;

use App\Application\Controllers\ClaseTrabajadorController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

/**
 * Class ClaseTrabajadorRouter.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Routers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class ClaseTrabajadorRouter
{
    /**
     * Constructor for the class.
     *
     * @param App $app The application instance.
     */
    public function __construct(App $app)
    {
        // Endpoint for getting all clases-trabajadores.
        $app->get('/clases-trabajadores', function (Request $request, Response $response) {
            $controller = new ClaseTrabajadorController();
            return $controller->index($response);
        });

        // Endpoint for getting a specific clase-trabajador.
        $app->get('/clases-trabajadores/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ClaseTrabajadorController();
            return $controller->find($request, $response, $args);
        });

        // Endpoint for creating a new clase-trabajador.
        $app->post('/clases-trabajadores', function (Request $request, Response $response) {
            $controller = new ClaseTrabajadorController();
            return $controller->store($request, $response);
        });

        // Endpoint for creating a multiple clases-trabajadores.
        $app->post('/clases-trabajadores/matricular', function (Request $request, Response $response) {
            $controller = new ClaseTrabajadorController();
            return $controller->matricular($request, $response);
        });

        // Endpoint for deleting a multiple clases-trabajadores.
        $app->post('/clases-trabajadores/desmatricular', function (Request $request, Response $response) {
            $controller = new ClaseTrabajadorController();
            return $controller->desmatricular($request, $response);
        });

        // Endpoint for updating an existing clase-trabajador.
        $app->put('/clases-trabajadores/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ClaseTrabajadorController();
            return $controller->update($request, $response, $args);
        });

        // Endpoint for deleting a clase-trabajador.
        $app->delete('/clases-trabajadores/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ClaseTrabajadorController();
            return $controller->delete($request, $response, $args);
        });
    }
}
