<?php

namespace App\Application\Controllers;

use App\Application\Factory\HttpStatusCodes;
use App\Application\Factory\MessageResponse;
use App\Application\Models\BancoPreguntas;
use App\Application\Models\ExamenAzar;
use App\Application\Models\ExamenAzarPregunta;
use Illuminate\Database\QueryException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BancoPreguntasController.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Controllers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class BancoPreguntasController implements HttpStatusCodes
{
    /**
     * Retrieves all the archivos and returns a JSON-encoded response.
     *
     * @param ResponseInterface $response response object to be returned
     * @throws None
     * @return ResponseInterface response object containing JSON-encoded archivos
     */
    public function index(ResponseInterface $response)
    {
        $status = self::HTTP_OK;
        try {
            $bancoPreguntas = BancoPreguntas::all();
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $bancoPreguntas);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la lista de banco de preguntas: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Retrieves a archivo by ID and returns it as a JSON response.
     *
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if archivo is not found
     * @return ResponseInterface HTTP response object with JSON data and headers
     */
    public function find(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];
        try {
            $bancoPreguntas = BancoPreguntas::find($id);

            if (!$bancoPreguntas) {
                $status = self::HTTP_NOT_FOUND;
                $res = MessageResponse::getInstance($status, self::HTTP_NOT_FOUND_MESSAGE, []);
            }
            else {
                $clase = $bancoPreguntas->clase;
                $periodo = $clase->periodo;
                $empresa = $periodo->empresaCliente;
                $bancoPreguntas->load(['preguntas','preguntas.respuestas']);
                $bancoPreguntas['clase'] = $clase;
                $bancoPreguntas['periodo'] = $periodo;
                $bancoPreguntas['empresaCliente'] = $empresa;
                //$bancoPreguntas['preguntas'] = $bancoPreguntas->preguntas;
                $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$bancoPreguntas]);
            }
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener el banco de preguntas: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Creates a new Archivo object from the parsed body of the HTTP request and writes the 
     * JSON-encoded object to the response body. Sets the response content type to JSON.
     *
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @throws Some_Exception_Class If there was an error creating the Archivo object
     * @return ResponseInterface The HTTP response object with the JSON-encoded Archivo object in its body and a Content-Type header of 'application/json'
     */
    public function store(Request $request, ResponseInterface $response)
    {
        $status = self::HTTP_CREATED;

        try {
            $bancoPreguntas = BancoPreguntas::create($request->getParsedBody());
            $bancoPreguntas['clase'] = $bancoPreguntas->clase;
            $res = MessageResponse::getInstance($status, self::HTTP_CREATED_MESSAGE, [$bancoPreguntas]);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al crear banco de preguntas: " . $e->errorInfo[2], null);
        }
        
        $response->withHeader('Content-Type', 'application/json')
                ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
                
        return $response->withStatus($status);
    }

    /**
     * Updates a archivo with the given ID.
     *
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if archivo is not found
     * @return ResponseInterface The HTTP response object with the JSON-encoded Archivo object in its body and a Content-Type header of 'application/json'
     */
    public function update(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];
        try {
            $bancoPreguntas = BancoPreguntas::find($id);
            $bancoPreguntas->update($request->getParsedBody());
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$bancoPreguntas]);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al actualizar el banco de preguntas: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Deletes a archivo with the given ID.
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if archivo is not found
     * @return ResponseInterface The HTTP response object with the JSON-encoded Archivo object in its body and a Content-Type header of 'application/json'
     * */
    public function delete(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];
        try {
            $bancoPreguntas = BancoPreguntas::find($id);
            if (!$bancoPreguntas) {
                $status = self::HTTP_NOT_FOUND;
                $res = MessageResponse::getInstance($status, self::HTTP_NOT_FOUND_MESSAGE, []);
            }
            else {
                $preguntas = $bancoPreguntas->preguntas;
                foreach ($preguntas as $pregunta) {
                    $eap_s = ExamenAzarPregunta::where('id_pregunta', $pregunta->id)->get();
                    if ($eap_s) {
                        foreach ($eap_s as $eap) {
                            $examenAzar = ExamenAzar::find($eap->id_examen_azar);
                            if ($examenAzar) {
                                $examenAzar->delete();
                            }
                            $eap->delete();
                        }
                    }
                    $respuestas = $pregunta->respuestas;
                    foreach ($respuestas as $respuesta) {
                        $respuesta->delete();
                    }
                    $pregunta->delete();
                }
                $bancoPreguntas->delete();
                $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$bancoPreguntas]);
            }
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al eliminar el banco de preguntas: " . $e->errorInfo[2], []);
        }
        
        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }
}