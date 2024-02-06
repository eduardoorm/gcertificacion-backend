<?php

namespace App\Application\Routers;

use App\Application\Controllers\ExamenAzarController;
use App\Application\Factory\HttpStatusCodes;
use App\Application\Factory\MessageResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

/**
 * Class ExamenAzarRouter.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Routers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class ExamenAzarRouter
{
    /**
     * Constructor for the class.
     *
     * @param App $app The application instance.
     */
    public function __construct(App $app)
    {
        // Endpoint for getting all examenes.
        $app->get('/examenes-azar', function (Request $request, Response $response) {
            $controller = new ExamenAzarController();
            return $controller->index($response);
        });

        // Endpoint for getting a specific examen.
        $app->get('/examenes-azar/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ExamenAzarController();
            return $controller->find($request, $response, $args);
        });

        //Endpoint for getting a last examen for a clase-trabajador.
        $app->get('/examenes-azar/clase-trabajador/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ExamenAzarController();
            return $controller->byClaseTrabajador($request, $response, $args);
        });

        // Endpoint for creating a new examen.
        $app->post('/examenes-azar', function (Request $request, Response $response) {
            $controller = new ExamenAzarController();
            return $controller->store($request, $response);
        });

        // Endpoint for getting pdf file
        $app->get('/files/{id}/pdf', function (Request $request, Response $response, array $args) {
            $controller = new ExamenAzarController();
            return $controller->pdf($request, $response, $args);
        });


        // Endpoint for updating an existing examen.
        $app->post('/examenes-azar/solve', function (Request $request, Response $response) {
            $controller = new ExamenAzarController();
            return $controller->solve($request, $response);
        });

        // Endpoint for updating an existing examen.
        $app->put('/examenes-azar/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ExamenAzarController();
            return $controller->update($request, $response, $args);
        });

        // Endpoint for deleting a examen.
        $app->delete('/examenes-azar/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ExamenAzarController();
            return $controller->delete($request, $response, $args);
        });
    }
}
