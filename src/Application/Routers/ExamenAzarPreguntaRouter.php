<?php

namespace App\Application\Routers;

use App\Application\Controllers\ExamenAzarPreguntaController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

/**
 * Class ExamenAzarPreguntaRouter.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Routers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class ExamenAzarPreguntaRouter
{
    /**
     * Constructor for the class.
     *
     * @param App $app The application instance.
     */
    public function __construct(App $app)
    {
        // Endpoint for getting all files.
        $app->get('/examenes-azar-preguntas', function (Request $request, Response $response) {
            $controller = new ExamenAzarPreguntaController();
            return $controller->index($response);
        });
        
        // Endpoint for getting a specific file.
        $app->get('/examenes-azar-preguntas/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ExamenAzarPreguntaController();
            return $controller->find($request, $response, $args);
        });

        // Endpoint for creating a new file.
        $app->post('/examenes-azar-preguntas', function (Request $request, Response $response) {
            $controller = new ExamenAzarPreguntaController();
            return $controller->store($request, $response);
        });

        // Endpoint for updating an existing file.
        $app->put('/examenes-azar-preguntas/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ExamenAzarPreguntaController();
            return $controller->update($request, $response, $args);
        });

        // Endpoint for deleting a file.
        $app->delete('/examenes-azar-preguntas/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ExamenAzarPreguntaController();
            return $controller->delete($request, $response, $args);
        });
    }
}
