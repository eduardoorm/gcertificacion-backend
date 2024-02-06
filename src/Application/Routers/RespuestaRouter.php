<?php

namespace App\Application\Routers;

use App\Application\Controllers\RespuestaController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

/**
 * Class RespuestaRouter.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Routers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class RespuestaRouter
{
    /**
     * Constructor for the class.
     *
     * @param App $app The application instance.
     */
    public function __construct(App $app)
    {
        // Endpoint for getting all files.
        $app->get('/respuestas', function (Request $request, Response $response) {
            $controller = new RespuestaController();
            return $controller->index($response);
        });

        // Endpoint for getting a specific file.
        $app->get('/respuestas/{id}', function (Request $request, Response $response, array $args) {
            $controller = new RespuestaController();
            return $controller->find($request, $response, $args);
        });

        // Endpoint for creating a new file.
        $app->post('/respuestas', function (Request $request, Response $response) {
            $controller = new RespuestaController();
            return $controller->store($request, $response);
        });

        // Endpoint for updating an existing file.
        $app->put('/respuestas/{id}', function (Request $request, Response $response, array $args) {
            $controller = new RespuestaController();
            return $controller->update($request, $response, $args);
        });

        // Endpoint for deleting a file.
        $app->delete('/respuestas/{id}', function (Request $request, Response $response, array $args) {
            $controller = new RespuestaController();
            return $controller->delete($request, $response, $args);
        });
    }
}
