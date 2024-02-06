<?php
namespace App\Application\Routers;

use App\Application\Controllers\AuthController;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

/**
 * Class AuthRouter.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Routers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class AuthRouter
{
    /**
     * Constructor for the class.
     *
     * @param App $app The application instance.
     */
    public function __construct(App $app)
    {
        // Endpoint for signin
        $app->post('/auth/login', function (Request $request, Response $response) {
            $controller = new AuthController();
            return $controller->login($request, $response);
        });
    }
}
