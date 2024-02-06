<?php

namespace App\Application\Routers;

use \App\Application\Controllers\PeriodoController;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

/**
 * Class PeriodoRouter.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Routers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class PeriodoRouter
{
    /**
     * Constructor for the class.
     *
     * @param App $app The application instance.
     */
    public function __construct(App $app)
    {
        // Endpoint for getting all files.
        $app->get('/periodos', function (Request $request, Response $response) {
            $controller = new PeriodoController();
            return $controller->index($response);
        });

        // Endpoint for getting a specific file.
        $app->get('/periodos/empresa-cliente/{id}', function (Request $request, Response $response, array $args) {
            $controller = new PeriodoController();
            return $controller->periodosByEmpresa($request, $response, $args);
        });

        // Endpoint for getting a specific file.
        $app->get('/periodos/{id}', function (Request $request, Response $response, array $args) {
            $controller = new PeriodoController();
            return $controller->find($request, $response, $args);
        });

        // Endpoint for getting all clases for a specific periodo.
        $app->get('/periodos/{id}/clases', function (Request $request, Response $response, array $args) {
            $controller = new PeriodoController();
            return $controller->clases($request, $response, $args);
        });

        // Endpoint for creating a new file.
        $app->post('/periodos', function (Request $request, Response $response) {
            $controller = new PeriodoController();
            return $controller->store($request, $response);
        });

        // Endpoint for updating an existing file.
        $app->put('/periodos/{id}', function (Request $request, Response $response, array $args) {
            $controller = new PeriodoController();
            return $controller->update($request, $response, $args);
        });

        // Endpoint for deleting a file.
        $app->delete('/periodos/{id}', function (Request $request, Response $response, array $args) {
            $controller = new PeriodoController();
            return $controller->delete($request, $response, $args);
        });
    }
}
