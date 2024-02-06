<?php

namespace App\Application\Controllers;

use App\Application\Factory\HttpStatusCodes;
use App\Application\Factory\MessageResponse;
use App\Application\Models\ArchivoTrabajador;
use App\Application\Models\BancoPreguntas;
use App\Application\Models\ClaseTrabajador;
use App\Application\Models\EmpresaCliente;
use App\Application\Models\ExamenAzar;
use App\Application\Models\Periodo;
use Illuminate\Database\QueryException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class PeriodoController.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Controllers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class PeriodoController implements HttpStatusCodes
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
        $status = self::HTTP_OK;
        try {
            $periodos = Periodo::all();
            $periodos->load('clases');
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $periodos);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la lista de periodos: " . $e->errorInfo[2], []);
        }
        
        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Retieves periodos by empresa and returns a JSON-encoded response.
     * 
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @param array $args Route parameters
     * @throws None
     * @return ResponseInterface HTTP response object with JSON data and headers
     * */
    public function periodosByEmpresa(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];
        try {
            $empresa = EmpresaCliente::find($id);

            if (!$empresa) {
                $status = self::HTTP_NOT_FOUND;
                $res = MessageResponse::getInstance($status, self::HTTP_NOT_FOUND_MESSAGE, []);
            }
            else {
                $periodos = $empresa->periodos;
                $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $periodos);
            }
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la lista de periodos: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
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
        $status = self::HTTP_OK;
        $id = $args['id'];
        try {
            $periodo = Periodo::find($id);

            if (!$periodo) {
                $status = self::HTTP_NOT_FOUND;
                $res = MessageResponse::getInstance($status, self::HTTP_NOT_FOUND_MESSAGE, []);
            }
            else {
                $periodo['clases'] = [$periodo->clases];
                $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$periodo]);
            }
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$periodo]);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener el periodo: " . $e->errorInfo[2], []);
        }
        
        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Retrieves all the clases for a given period and returns a JSON-encoded response.
     * 
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @param array $args Route parameters
     * @throws None
     * @return ResponseInterface HTTP response object with JSON data and headers
     * */
    public function clases (Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];
        try {
            $periodo = Periodo::find($id);

            if (!$periodo) {
                $status = self::HTTP_NOT_FOUND;
                $res = MessageResponse::getInstance($status, self::HTTP_NOT_FOUND_MESSAGE, []);
            }
            else {
                $clases = $periodo->clases;
                $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $clases);
            }
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener el listado de clases: " . $e->errorInfo[2], []);
        }
        
        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
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
        $status = self::HTTP_CREATED;
        try {
            $periodo = Periodo::create($request->getParsedBody());
            $res = MessageResponse::getInstance($status, self::HTTP_CREATED_MESSAGE, [$periodo]);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al crear el periodo: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Updates a clase with the given ID.
     *
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @throws Some_Exception_Class If there was an error updating the Clase object
     * @return ResponseInterface The HTTP response object with the JSON-encoded Clase object in its body and a Content-Type header of 'application/json'
     */
    public function update(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];
        try {
            $periodo = Periodo::find($id);
            $periodo->update($request->getParsedBody());
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$periodo]);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al actualizar el periodo: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Deletes a clase with the given ID.
     *
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @throws Some_Exception_Class If there was an error deleting the Clase object
     * @return ResponseInterface The HTTP response object with the JSON-encoded Clase object in its body and a Content-Type header of 'application/json'
     */
    public function delete(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];

        try {
            $periodo = Periodo::find($id);
            
            if (!$periodo) {
                $status = self::HTTP_NOT_FOUND;
                $res = MessageResponse::getInstance($status, self::HTTP_NOT_FOUND_MESSAGE, []);
            }
            else {
                $clases = $periodo->clases;
                foreach ($clases as $clase) {
                    $archivos = $clase->archivos;
                    foreach ($archivos as $archivo) {
                        ArchivoTrabajador::where('id_archivo', $archivo->id)->delete();
                        $archivo->delete();
                    }

                    $clasesTrabajadores = ClaseTrabajador::where('id_clase', $clase->id)->get();
                    foreach ($clasesTrabajadores as $claseTrabajador) {
                        $examenesAzar = ExamenAzar::where('id_clase_trabajador', $claseTrabajador->id)->get();
                        foreach ($examenesAzar as $examenAzar) {
                            $preguntas = $examenAzar->preguntas;
                            foreach ($preguntas as $pregunta) {
                                $pregunta->delete();
                            }
                            $examenAzar->delete();
                        }
                        $claseTrabajador->delete();
                    }

                    $bancos = BancoPreguntas::where('id_clase', $clase->id)->get();
                    foreach ($bancos as $banco) {
                        $preguntas = $banco->preguntas;
                        foreach ($preguntas as $pregunta) {
                            $respuestas = $pregunta->respuestas;
                            foreach ($respuestas as $respuesta) {
                                $respuesta->delete();
                            }
                            $pregunta->delete();
                        }
                        $banco->delete();
                    }
                    $clase->delete();
                }
                $periodo->delete();
                $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$periodo]);
            }
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al eliminar el periodo: " . $e->errorInfo[2], []);
        }
        
        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }
}