<?php

namespace App\Application\Routers;

use App\Application\Controllers\ReporteController;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\App;

/**
 * Class ReporteRouter.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Routers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class ReporteRouter {
    
    /**
     * Constructor for the class.
     * 
     * @param App $app The application instance.
     */
    public function __construct(App $app) {
        
        $app->get('/report/induccion/empresa-cliente/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ReporteController();
            return $controller->induccion($request, $response, $args);
        });
        
        // Deprecated
        $app->get('/report/capacitacion/empresa-cliente/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ReporteController();
            return $controller->capacitacion($request, $response, $args);
        });
        
        // Deprecated
        $app->get('/report/documentacion/empresa-cliente/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ReporteController();
            return $controller->documentacion($request, $response, $args);
        });

        $app->get('/report/capacitacion/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ReporteController();
            return $controller->capacitacion($request, $response, $args);
        });
        $app->get('/report/documentacion/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ReporteController();
            return $controller->documentacion($request, $response, $args);
        });
        
        $app->get('/informe/induccion/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ReporteController();
            return $controller->induccionInforme($request, $response, $args);
        });
        
        $app->get('/informe/{idCurso}/capacitacion/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ReporteController();
            return $controller->capacitacionInforme($request, $response, $args);
        });

        $app->get('/informe/documentacion/{id}', function (Request $request, Response $response, array $args) {
            $controller = new ReporteController();
            return $controller->documentacionInforme($request, $response, $args);
        });
    }
}