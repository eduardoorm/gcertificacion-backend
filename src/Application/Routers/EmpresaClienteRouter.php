<?php

namespace App\Application\Routers;

use App\Application\Controllers\EmpresaClienteController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

/**
 * Class EmpresaClienteRouter.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Routers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class EmpresaClienteRouter
{
    /**
     * Constructor for the class.
     *
     * @param App $app The application instance.
     */
    public function __construct(App $app)
    {
        // Endpoint for getting all empresa-clientes with active periodo.
        $app->get('/empresas-clientes/periodo-activo', function (Request $request, Response $response) {
            $controller = new EmpresaClienteController();
            return $controller->withPeriodoActivo($request, $response);
        });

        // Endpoint for getting all empresa-clientes.
        $app->get('/empresas-clientes', function (Request $request, Response $response) {
            $controller = new EmpresaClienteController();
            return $controller->index($response);
        });

        // Endpoint for getting a specific empresa-cliente.
        $app->get('/empresas-clientes/{id}', function (Request $request, Response $response, array $args) {
            $controller = new EmpresaClienteController();
            return $controller->find($request, $response, $args);
        });

        // Endpoint for getting all periodos for a specific empresa-cliente.
        $app->get('/empresas-clientes/{id}/periodos', function (Request $request, Response $response, array $args) {
            $controller = new EmpresaClienteController();
            return $controller->periodos($request, $response, $args);
        });

        // Endpoint for getting all clases for active periodo for a specific empresa-cliente.
        $app->get('/empresas-clientes/{id}/periodo-activo/clases', function (Request $request, Response $response, array $args) {
            $controller = new EmpresaClienteController();
            return $controller->clases($request, $response, $args);
        });

        // Endpoint for getting all bancos-preguntas for active periodo for a specific empresa-cliente.
        $app->get('/empresas-clientes/{id}/periodo-activo/bancos-preguntas', function (Request $request, Response $response, array $args) {
            $controller = new EmpresaClienteController();
            return $controller->bancosPreguntas($request, $response, $args);
        });

        // Endpoint for getting all trabajadores for a specific empresa-cliente.
        $app->get('/empresas-clientes/{id}/trabajadores', function (Request $request, Response $response, array $args) {
            $controller = new EmpresaClienteController();
            return $controller->trabajadores($request, $response, $args);
        });

        // Endpoint for creating a new file.
        $app->post('/empresas-clientes', function (Request $request, Response $response) {
            $controller = new EmpresaClienteController();
            return $controller->store($request, $response);
        });

        // Endpoint for updating an existing empresa-cliente.
        $app->put('/empresas-clientes/{id}', function (Request $request, Response $response, array $args) {
            $controller = new EmpresaClienteController();
            return $controller->update($request, $response, $args);
        });

        // Endpoint for deleting a existing empresa-cliente.
        $app->delete('/empresas-clientes/{id}', function (Request $request, Response $response, array $args) {
            $controller = new EmpresaClienteController();
            return $controller->delete($request, $response, $args);
        });
    }
}
