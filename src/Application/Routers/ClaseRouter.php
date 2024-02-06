<?php

namespace App\Application\Routers;

use App\Application\Controllers\ClaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

/**
 * Class ClaseRouter.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Routers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class ClaseRouter
{
    /**
     * Constructor for the class.
     *
     * @param App $app The application instance.
     */
    public function __construct(App $app)
    {

        $app->get('/clases/tipos', function (Request $request, Response $response) {
            $controller = new ClaseController();
            return $controller->types($request, $response);
        });
        
        // Endpoint for getting all classes.
        $app->get('/clases', function (Request $request, Response $response) {
            $controller = new ClaseController();
            return $controller->index($response);
        });

        // Endpoint for getting a specific class.
        $app->get('/clases/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ClaseController();
            return $controller->find($request, $response, $args);
        });

        // Endpoint for getting all files for a specific class.
        $app->get('/clases/{id}/archivos', function (Request $request, Response $response, array $args) {
            $controller = new ClaseController();
            return $controller->archivos($request, $response, $args);
        });

        // Endpoint for getting all banco-preguntas for a specific class.
        $app->get('/clases/{id}/banco-preguntas', function (Request $request, Response $response, array $args) {
            $controller = new ClaseController();
            return $controller->bancoPreguntas($request, $response, $args);
        });

        // Endpoint for creating a new class.
        $app->post('/clases', function (Request $request, Response $response) {
            $controller = new ClaseController();
            return $controller->store($request, $response);
        });

        // Endpoint for updating an existing class.
        $app->put('/clases/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ClaseController();
            return $controller->update($request, $response, $args);
        });

        // Endpoint for deleting a specific class.
        $app->delete('/clases/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ClaseController();
            return $controller->delete($request, $response, $args);
        });
    }
}
