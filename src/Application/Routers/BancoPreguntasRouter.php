<?php

namespace App\Application\Routers;

use \App\Application\Controllers\BancoPreguntasController;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

/**
 * Class BancoPreguntasRouter.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Routers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class BancoPreguntasRouter
{
    /**
     * Constructor for the class.
     *
     * @param App $app The application instance.
     */
    public function __construct(App $app)
    {
        // Endpoint for getting all banco-preguntas.
        $app->get('/banco-preguntas', function (Request $request, Response $response) {
            $controller = new BancoPreguntasController();
            return $controller->index($response);
        });

        // Endpoint for getting a specific banco-preguntas.
        $app->get('/banco-preguntas/{id}', function (Request $request, Response $response, array $args) {
            $controller = new BancoPreguntasController();
            return $controller->find($request, $response, $args);
        });

        // Endpoint for creating a new banco-preguntas.
        $app->post('/banco-preguntas', function (Request $request, Response $response) {
            $controller = new BancoPreguntasController();
            return $controller->store($request, $response);
        });

        // Endpoint for updating an existing banco-preguntas.
        $app->put('/banco-preguntas/{id}', function (Request $request, Response $response, array $args) {
            $controller = new BancoPreguntasController();
            return $controller->update($request, $response, $args);
        });

        // Endpoint for deleting a specific banco-preguntas.
        $app->delete('/banco-preguntas/{id}', function (Request $request, Response $response, array $args) {
            $controller = new BancoPreguntasController();
            return $controller->delete($request, $response, $args);
        });
    }
}
