<?php

declare(strict_types=1);

use \App\Application\Routers\ArchivoRouter;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Controllers\UsuarioController;
use App\Application\Routers\ArchivoTrabajadorRouter;
use App\Application\Routers\AuthRouter;
use App\Application\Routers\BancoPreguntasRouter;
use App\Application\Routers\CertificadoRouter;
use App\Application\Routers\ClaseRouter;
use App\Application\Routers\ClasesArchivoRouter;
use App\Application\Routers\ClaseTrabajadorRouter;
use App\Application\Routers\EmpresaClienteRouter;
use App\Application\Routers\ExamenAzarPreguntaRouter;
use App\Application\Routers\ExamenAzarRouter;
use App\Application\Routers\PeriodoRouter;
use App\Application\Routers\PreguntaRouter;
use App\Application\Routers\ReporteRouter;
use App\Application\Routers\RespuestaRouter;
use App\Application\Routers\TrabajadorRouter;
use App\Application\Routers\UsuarioRouter;
use App\Application\Routers\UploadRouter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Exception\HttpNotFoundException;

return function (App $app) {
    $app->options('/{routes:.+}', function (Request $request, Response $response, $args) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->add(function (Request $request, $handler): Response {
        $response = $handler->handle($request);

        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader(
                'Access-Control-Allow-Headers',
                'X-Requested-With, Content-Type, Accept, Origin, Authorization'
            )
            ->withHeader(
                'Access-Control-Allow-Methods',
                'GET, POST, PUT, DELETE, PATCH, OPTIONS'
            );
    });

    $authRouter = new AuthRouter($app);
    $uploadRouter = new UploadRouter($app);
    $certificadoRouter = new CertificadoRouter($app);
    $archivoRouter = new ArchivoRouter($app);
    $archivoTrabajador = new ArchivoTrabajadorRouter($app);
    $bancoPreguntasRouter = new BancoPreguntasRouter($app);
    $claseRouter = new ClaseRouter($app);
    $clasesArchivoRouter = new ClasesArchivoRouter($app);
    $claseTrabajadorRouter = new ClaseTrabajadorRouter($app);
    $empresaClienteRouter = new EmpresaClienteRouter($app);
    $examenAzarRouter = new ExamenAzarRouter($app);
    $examenAzarPreguntaRouter = new ExamenAzarPreguntaRouter($app);
    $periodoRouter = new PeriodoRouter($app);
    $preguntaRouter = new PreguntaRouter($app);
    $respuestaRouter = new RespuestaRouter($app);
    $trabajadorRouter = new TrabajadorRouter($app);
    $usuarioRouter = new UsuarioRouter($app);
    $reporteRouter = new ReporteRouter($app);
};
