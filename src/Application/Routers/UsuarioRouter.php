<?php

namespace App\Application\Routers;

use App\Application\Controllers\UsuarioController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

/**
 * Class UsuarioRouter.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Routers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class UsuarioRouter
{
    /**
     * Constructor for the class.
     *
     * @param App $app The application instance.
     */
    public function __construct(App $app)
    {
        // Endpoint for getting all files.
        $app->get('/usuarios', function (Request $request, Response $response) {
            $controller = new UsuarioController();
            return $controller->index($response);
        });
        
        // Endpoint for getting a specific file.
        $app->get('/usuarios/{id}', function (Request $request, Response $response, array $args) {
            $controller = new UsuarioController();
            return $controller->find($request, $response, $args);
        });

        // Endpoint for creating a new file.
        $app->post('/usuarios', function (Request $request, Response $response) {
            $controller = new UsuarioController();
            return $controller->store($request, $response);
        });

        // Endpoint for updating an existing file.
        $app->put('/usuarios/{id}', function (Request $request, Response $response, array $args) {
            $controller = new UsuarioController();
            return $controller->update($request, $response, $args);
        });

        // Endpoint for deleting a file.
        $app->delete('/usuarios/{id}', function (Request $request, Response $response, array $args) {
            $controller = new UsuarioController();
            return $controller->delete($request, $response, $args);
        });
    }
}
