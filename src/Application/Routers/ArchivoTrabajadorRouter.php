<?php

namespace App\Application\Routers;

use App\Application\Controllers\ArchivoTrabajadorController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

/**
 * Class ArchivoTrabajadorRouter.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Routers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class ArchivoTrabajadorRouter
{
    /**
     * Constructor for the class.
     *
     * @param App $app The application instance.
     */
    public function __construct(App $app)
    {
        // Endpoint for getting all declaraciones juradas.
        $app->get('/archivos-trabajadores', function (Request $request, Response $response) {
            $controller = new ArchivoTrabajadorController();
            return $controller->index($response);
        });

        // Endpoint for getting a specific declaracion jurada.
        $app->get('/archivos-trabajadores/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ArchivoTrabajadorController();
            return $controller->find($request, $response, $args);
        });

        // Endpoint for getting all declaraciones juradas for a specific trabajador.
        $app->get('/archivos-trabajadores/trabajador/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ArchivoTrabajadorController();
            return $controller->byTrabajador($request, $response, $args);
        });

        // Endpoint for creating a new declaracion jurada.
        $app->post('/archivos-trabajadores', function (Request $request, Response $response) {
            $controller = new ArchivoTrabajadorController();
            return $controller->store($request, $response);
        });

        // Endpoint for creating a new declaracion jurada.
        $app->post('/archivos-trabajadores/accept', function (Request $request, Response $response) {
            $controller = new ArchivoTrabajadorController();
            return $controller->accept($request, $response);
        });

        // Endpoint for creating a new declaracion jurada.
        $app->post('/archivos-trabajadores/download', function (Request $request, Response $response) {
            $controller = new ArchivoTrabajadorController();
            return $controller->download($request, $response);
        });

        // Endpoint for updating an existing declaracion jurada.
        $app->put('/archivos-trabajadores/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ArchivoTrabajadorController();
            return $controller->update($request, $response, $args);
        });

        // Endpoint for deleting a declaracion jurada.
        $app->delete('/archivos-trabajadores/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ArchivoTrabajadorController();
            return $controller->delete($request, $response, $args);
        });
    }
}
