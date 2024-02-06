<?php

namespace App\Application\Controllers;

use App\Application\Models\ExamenAzarPregunta;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class ExamenAzarPreguntaController.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Controllers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class ExamenAzarPreguntaController
{
    /**
     * Retrieves all the clases and returns a JSON-encoded response.
     *
     * @param ResponseInterface $response response object to be returned
     * @throws None
     * @return ResponseInterface response object containing JSON-encoded clases
     */
    public function index(ResponseInterface $response)
    {
        $examenAzarPregunta = ExamenAzarPregunta::all();
        $examenAzarPregunta = ['examenes_azar_preguntas' => $examenAzarPregunta];
        $response->getBody()->write(json_encode($examenAzarPregunta, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Retrieves a clase by ID and returns it as a JSON response.
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
        $examenAzarPregunta = ExamenAzarPregunta::find($id);
        $response->getBody()->write(json_encode($examenAzarPregunta, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Creates a new Clase object from the parsed body of the HTTP request and writes the 
     * JSON-encoded object to the response body. Sets the response content type to JSON.
     *
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @throws Some_Exception_Class If there was an error creating the Clase object
     * @return ResponseInterface The HTTP response object with the JSON-encoded Clase object in its body and a Content-Type header of 'application/json'
     */
    public function store(Request $request, ResponseInterface $response)
    {
        $examenAzarPregunta = ExamenAzarPregunta::create($request->getParsedBody());
        $response->getBody()->write(json_encode($examenAzarPregunta, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Updates a clase with the given ID.
     *
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if clase is not found
     * @return ResponseInterface HTTP response object with JSON data and headers
     */
    public function update(Request $request, ResponseInterface $response, array $args)
    {
        $id = $args['id'];
        $examenAzarPregunta = ExamenAzarPregunta::find($id);
        $examenAzarPregunta->update($request->getParsedBody());
        $response->getBody()->write(json_encode($examenAzarPregunta, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Deletes a clase with the given ID.
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if clase is not found
     * @return ResponseInterface HTTP response object with JSON data and headers
     */
    public function delete(Request $request, ResponseInterface $response, array $args)
    {
        $id = $args['id'];
        $examenAzarPregunta = ExamenAzarPregunta::find($id);
        $examenAzarPregunta->delete();
        $response->getBody()->write(json_encode($examenAzarPregunta, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }
}