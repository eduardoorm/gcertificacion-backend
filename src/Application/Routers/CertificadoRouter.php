<?php

namespace App\Application\Routers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Psr7\Stream;

/**
 * Class CertificadoRouter.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Routers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class CertificadoRouter {

    /**
     * Constructor for the class.
     * 
     * @param App $app The application instance.
     */
    public function __construct(App $app) 
    {
        $app->get('/certificados/{name}', function (Request $request, Response $response, array $args) {
            $directory = $this->get('directory_certificates');
            $file = $directory . DIRECTORY_SEPARATOR . $args['name'];
            
            $fh = fopen($file, 'rb');
            $file_stream = new Stream($fh);

            return $response->withBody($file_stream)
                ->withHeader('Content-Disposition', 'attachment; filename='.$args['name'].';')
                ->withHeader('Content-Type', 'application/pdf')
                ->withHeader('Content-Length', filesize($file));
        });
    }
}