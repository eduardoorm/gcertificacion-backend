<?php

namespace App\Application\Routers;

use App\Application\Controllers\ClasesArchivoController;
use App\Application\Settings\SettingsInterface;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

/**
 * Class ClasesArchivoRouter.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Routers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class ClasesArchivoRouter
{
    /**
     * Constructor for the class.
     *
     * @param App $app The application instance.
     */
    public function __construct(App $app)
    {
        
        // Endpoint for getting all files.
        $app->get('/clases-archivos', function (Request $request, Response $response) {
            $controller = new ClasesArchivoController();
            return $controller->index($response);
        });

        // Endpoint for getting a specific file.
        $app->get('/clases-archivos/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ClasesArchivoController();
            return $controller->find($request, $response, $args);
        });

        // Endpoint for getting files for a specific class.
        $app->get('/clases-archivos/clases/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ClasesArchivoController();
            return $controller->findArchivosByClase($request, $response, $args);
        });

        // Endpoint for creating a new file.
        $app->post('/clases-archivos', function (Request $request, Response $response) {
            $controller = new ClasesArchivoController();
            return $controller->store($request, $response);
        });

        // Endpoint for updating an existing file.
        $app->put('/clases-archivos/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ClasesArchivoController();
            return $controller->update($request, $response, $args);
        });

        // Endpoint for deleting a file.
        $app->delete('/clases-archivos/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ClasesArchivoController();
            return $controller->delete($request, $response, $args);
        });
    }
}
