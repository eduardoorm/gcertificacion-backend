<?php

namespace App\Application\Controllers;

use App\Application\Factory\HttpStatusCodes;
use App\Application\Factory\MessageResponse;
use App\Application\Models\ArchivoTrabajador;
use Illuminate\Database\QueryException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class ArchivoTrabajadorController.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Controllers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class ArchivoTrabajadorController implements HttpStatusCodes
{
    /**
     * Retrieves all the declaraciones juradas and returns a JSON-encoded response.
     *
     * @param ResponseInterface $response response object to be returned
     * @throws None
     * @return ResponseInterface response object containing JSON-encoded clases
     */
    public function index(ResponseInterface $response)
    {
        $declaracionJurada = ArchivoTrabajador::all();
        $declaracionJurada = ['declaraciones_juradas' => $declaracionJurada];
        $response->getBody()->write(json_encode($declaracionJurada, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Retrieves a declaracion jurada by ID and returns it as a JSON response.
     *
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if clase is not found
     * @return ResponseInterface HTTP response object with JSON data and headers
     */
    public function find(Request $request, ResponseInterface $response, array $args)
    {
        $id = $args['id'];
        $declaracionJurada = ArchivoTrabajador::find($id);
        $response->getBody()->write(json_encode($declaracionJurada, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Retrieves all the declaraciones juradas for a specific trabajador and returns a JSON-encoded response.
     * 
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if clase is not found
     * @return ResponseInterface HTTP response object with JSON data and headers
     * */
    public function byTrabajador(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id_trabajador = $args['id'];
        try {
            $declaraciones = ArchivoTrabajador::where('id_trabajador', $id_trabajador)->get();
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $declaraciones);
        }
        catch (QueryException $e) {
            $status = self::HTTP_NOT_FOUND;
            $res = MessageResponse::getInstance($status, self::HTTP_NOT_FOUND_MESSAGE, []);
        }
        
        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Creates a new DeclaracionJurada object from the parsed body of the HTTP request and writes the 
     * JSON-encoded object to the response body. Sets the response content type to JSON.
     *
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @throws Some_Exception_Class If there was an error creating the Clase object
     * @return ResponseInterface The HTTP response object with the JSON-encoded Clase object in its body and a Content-Type header of 'application/json'
     */
    public function store(Request $request, ResponseInterface $response)
    {
        $status = self::HTTP_CREATED;
        try {
            $declaracionJurada = ArchivoTrabajador::create($request->getParsedBody());
            $res = MessageResponse::getInstance($status, self::HTTP_CREATED_MESSAGE, [$declaracionJurada]);
        }
        catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, self::HTTP_BAD_REQUEST_MESSAGE, []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        
        return $response->withStatus($status);
    }

    public function accept (Request $request, ResponseInterface $response)
    {
        $status = self::HTTP_OK;
        $data = $request->getParsedBody();
        try {
            $archivoTrabajador = ArchivoTrabajador::firstwhere([
                "id_archivo" => $data['id_archivo'],
                "id_trabajador" => $data['id_trabajador'],
            ]);

            if(!$archivoTrabajador){
                $archivoTrabajador = ArchivoTrabajador::create($request->getParsedBody());
            }
            else{
                $archivoTrabajador->update(['aceptado' => $data['aceptado']]);
            }

            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$archivoTrabajador]);
        }
        catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, self::HTTP_BAD_REQUEST_MESSAGE, []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    public function download (Request $request, ResponseInterface $response)
    {
        $status = self::HTTP_OK;
        $data = $request->getParsedBody();
        try {
            $archivoTrabajador = ArchivoTrabajador::firstwhere([
                "id_archivo" => $data['id_archivo'],
                "id_trabajador" => $data['id_trabajador'],
            ]);

            if(!$archivoTrabajador){
                $archivoTrabajador = ArchivoTrabajador::create($request->getParsedBody());
            }
            else{
                $archivoTrabajador->update(['descargado' => $data['descargado']]);
            }

            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$archivoTrabajador]);
        }
        catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, self::HTTP_BAD_REQUEST_MESSAGE, []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Updates a declaracion jurada with the given ID.
     *
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if clase is not found
     * @return ResponseInterface The HTTP response object with the JSON-encoded Clase object in its body and a Content-Type header of 'application/json'
     */
    public function update(Request $request, ResponseInterface $response, array $args)
    {
        $id = $args['id'];
        $declaracionJurada = ArchivoTrabajador::find($id);
        $declaracionJurada->update($request->getParsedBody());
        $response->getBody()->write(json_encode($declaracionJurada, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Deletes a declaracion jurada with the given ID.
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if clase is not found
     * @return ResponseInterface The HTTP response object with the JSON-encoded Clase object in its body and a Content-Type header of 'application/json'
     */
    public function delete(Request $request, ResponseInterface $response, array $args)
    {
        $id = $args['id'];
        $declaracionJurada = ArchivoTrabajador::find($id);
        $declaracionJurada->delete();
        $response->getBody()->write(json_encode($declaracionJurada, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }
}