<?php

namespace App\Application\Controllers;

use App\Application\Factory\HttpStatusCodes;
use App\Application\Factory\MessageResponse;
use App\Application\Models\ArchivoTrabajador;
use App\Application\Models\BancoPreguntas;
use App\Application\Models\ClaseTrabajador;
use App\Application\Models\EmpresaCliente;
use App\Application\Models\ExamenAzar;
use App\Application\Models\Usuario;
use Illuminate\Database\QueryException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class EmpresaClienteController.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Controllers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class EmpresaClienteController implements HttpStatusCodes
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
            $empresas = EmpresaCliente::all();
            $empresas->load('periodos');
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $empresas);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la lista de clientes: " . $e->errorInfo[2], []);
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
            $empresa = EmpresaCliente::find($id);
            $empresa->load(['periodos', 'periodos.clases', 'trabajadores.clases']);
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$empresa]);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la lista de clientes: " . $e->errorInfo[2], []);
        }
        
        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT));

        return $response->withStatus($status);
    }

    /**
     * Retrieves all periodos for a specific empresa and returns a JSON-encoded response.
     * 
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @param array $args Route parameters
     * @throws None
     * @return ResponseInterface HTTP response object with JSON data and headers
     * */
    public function periodos(Request $request, ResponseInterface $response, array $args)
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
     * Retrieves all empresas clientes with active periodos and returns a JSON-encoded response.
     * 
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @param array $args Route parameters
     * @throws None
     * @return ResponseInterface HTTP response object with JSON data and headers
     * */
    public function withPeriodoActivo(Request $request, ResponseInterface $response)
    {
        $status = self::HTTP_OK;
        try {
            $empresas = EmpresaCliente::whereRelation('periodos', 'activo', 1)->get();

            if (!$empresas) {
                $status = self::HTTP_NOT_FOUND;
                $res = MessageResponse::getInstance($status, self::HTTP_NOT_FOUND_MESSAGE, []);
            }
            else {
                $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $empresas);
            }
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la lista de clientes: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Retrieves a list of classes for a given company and active period.
     *
     * @param Request $request The HTTP request object.
     * @param ResponseInterface $response The HTTP response object.
     * @param array $args The route parameters.
     * @throws QueryException If there's an error retrieving the list of classes.
     * @return ResponseInterface The HTTP response object with JSON-encoded message.
     */
    public function clases(Request $request, ResponseInterface $response, array $args)
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
                $periodo = $empresa->periodos()->where('activo', 1)->first();

                if (!$periodo) {
                    $status = self::HTTP_NOT_FOUND;
                    $res = MessageResponse::getInstance($status, self::HTTP_NOT_FOUND_MESSAGE, []);
                }
                else {
                    $clases = $periodo->clases;
                    $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $clases);
                }
            }
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la lista de clases: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Retrieves all bancos-preguntas for a given company and active period.
     * 
     * @param Request $request The HTTP request object.
     * @param ResponseInterface $response The HTTP response object.
     * @param array $args The route parameters.
     * @throws QueryException If there's an error retrieving the list of bancos-preguntas.
     * @return ResponseInterface The HTTP response object with JSON-encoded message.
     * */
    public function bancosPreguntas(Request $request, ResponseInterface $response, array $args)
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
                $periodo = $empresa->periodos()->where('activo', 1)->first();

                if (!$periodo) {
                    $status = self::HTTP_NOT_FOUND;
                    $res = MessageResponse::getInstance($status, self::HTTP_NOT_FOUND_MESSAGE, []);
                }
                else {
                    $clases = $periodo->clases;
                    $bancos = [];
                    foreach ($clases as $clase) {
                        //$clase->bancoPreguntas();
                        $bancos_ = $clase->bancoPreguntas;
                        if($bancos_) {
                            $bancos_['clase'] = $periodo->clases()->where('id', $clase->id)->first();
                            $bancos[] = $bancos_;
                        }
                    }
                    $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $bancos);
                }
            }
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la lista de bancos-preguntas: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Retrieves all trabajadores for a specific empresa-cliente
     * 
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @param array $args Route parameters
     * @throws None
     * @return ResponseInterface HTTP response object with JSON data and headers
     * */
    public function trabajadores(Request $request, ResponseInterface $response, array $args)
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
                $trabajadores = $empresa->trabajadores;
                $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $trabajadores);
            }
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la lista de trabajadores: " . $e->errorInfo[2], []);
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
            $empresa = EmpresaCliente::create($request->getParsedBody());
            $res = MessageResponse::getInstance($status, self::HTTP_CREATED_MESSAGE, [$empresa]);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al crear la Empresa Cliente: " . $e->errorInfo[2], null);
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
     * @param array $args Route parameters
     * @throws Some_Exception_Class if clase is not found
     * @return ResponseInterface The HTTP response object with the JSON-encoded Clase object in its body and a Content-Type header of 'application/json'
     */
    public function update(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];
        try {
            $empresa = EmpresaCliente::find($id);
            $empresa->update($request->getParsedBody());
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$empresa]);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al actualizar la Empresa Cliente: " . $e->errorInfo[2], []);
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
     * @param array $args Route parameters
     * @throws Some_Exception_Class if clase is not found
     * @return ResponseInterface The HTTP response object with the JSON-encoded Clase object in its body and a Content-Type header of 'application/json'
     */
    public function delete(Request $request, ResponseInterface $response, array $args)
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
                foreach ($periodos as $periodo) {
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
                }

                $trabajadores = $empresa->trabajadores;
                foreach ($trabajadores as $trabajador) {
                    Usuario::where('id_trabajador', $trabajador->id)->delete();
                    $trabajador->delete();
                }

                $empresa->delete();
                $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$empresa]);
            }
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al eliminar la Empresa Cliente: " . $e->errorInfo[2], []);
        }
        
        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }
}