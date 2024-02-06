<?php

namespace App\Application\Controllers;

use App\Application\Models\Respuesta;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class RespuestaController.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Controllers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class RespuestaController
{
    /**
     * Retrieves all the respuestas and returns a JSON-encoded response.
     *
     * @param ResponseInterface $response response object to be returned
     * @throws None
     * @return ResponseInterface response object containing JSON-encoded respuestas
     */
    public function index(ResponseInterface $response)
    {
        $respuestas = Respuesta::all();
        $respuestas = ['respuestas' => $respuestas];
        $response->getBody()->write(json_encode($respuestas, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Retrieves a respuesta by ID and returns it as a JSON response.
     *
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if respuesta is not found
     * @return ResponseInterface HTTP response object with JSON data and headers
     */
    public function find(Request $request, ResponseInterface $response, array $args)
    {
        $id = $args['id'];
        $respuesta = Respuesta::find($id);
        $response->getBody()->write(json_encode($respuesta, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Creates a new Respuesta object from the parsed body of the HTTP request and writes the 
     * JSON-encoded object to the response body. Sets the response content type to JSON.
     *
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @throws Some_Exception_Class If there was an error creating the Respuesta object
     * @return ResponseInterface The HTTP response object with the JSON-encoded Respuesta object in its body and a Content-Type header of 'application/json'
     */
    public function store(Request $request, ResponseInterface $response)
    {
        $respuesta = Respuesta::create($request->getParsedBody());
        $response->getBody()->write(json_encode($respuesta, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Updates a respuesta with the given ID.
     *
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if respuesta is not found
     * @return ResponseInterface The HTTP response object with the JSON-encoded Respuesta object in its body and a Content-Type header of 'application/json'
     */
    public function update(Request $request, ResponseInterface $response, array $args)
    {
        $id = $args['id'];
        $respuesta = Respuesta::find($id);
        $respuesta->update($request->getParsedBody());
        $response->getBody()->write(json_encode($respuesta, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Deletes a respuesta with the given ID.
     *
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if respuesta is not found
     * @return ResponseInterface The HTTP response object with the JSON-encoded Respuesta object in its body and a Content-Type header of 'application/json'
     */
    public function delete(Request $request, ResponseInterface $response, array $args)
    {
        $id = $args['id'];
        $respuesta = Respuesta::find($id);
        $respuesta->delete();
        $response->getBody()->write(json_encode($respuesta, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }
}