<?php

namespace App\Application\Controllers;

use App\Application\Factory\HttpStatusCodes;
use App\Application\Factory\MessageResponse;
use App\Application\Models\Clase;
use App\Application\Models\ClaseTrabajador;
use Illuminate\Database\QueryException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class ClaseTrabajadorController.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Controllers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class ClaseTrabajadorController implements HttpStatusCodes
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
            $clases = ClaseTrabajador::all();
            $clases->load(['clase', 'trabajador', 'usuario']);
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $clases);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la lista de clases: " . $e->errorInfo[2], []);
        }
        
        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT));
        
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
            $clase = ClaseTrabajador::find($id);
            $clase->load(['clase', 'trabajador', 'usuario']);
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$clase]);
        } catch (QueryException $e) {
            $status = self::HTTP_NOT_FOUND;
            $res = MessageResponse::getInstance($status, self::HTTP_NOT_FOUND_MESSAGE, []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT));

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
            $clase = ClaseTrabajador::create($request->getParsedBody());
            $clase->load(['clase', 'trabajador', 'usuario']);
            $res = MessageResponse::getInstance($status, self::HTTP_CREATED_MESSAGE, [$clase]);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al crear la clase: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT));

        return $response->withStatus($status);
    }

    /**
     * Creates multiple clases from the parsed body of the HTTP request and writes the
     * JSON-encoded object to the response body. Sets the response content type to JSON.
     * 
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @throws Some_Exception_Class If there was an error creating the Clase object
     * @return ResponseInterface The HTTP response object with the JSON-encoded Clase object in its body and a Content-Type header of 'application/json'
     * */
    public function matricular(Request $request, ResponseInterface $response)
    {
        $status = self::HTTP_CREATED;
        try {
            $body = $request->getParsedBody();
            $id_clase = $body['id_clase'];
            $ids_trabajadores = $body['ids_trabajadores'];

            foreach ($ids_trabajadores as $id_trabajador) {
                ClaseTrabajador::firstOrCreate([
                    'id_clase' => $id_clase, 
                    'id_trabajador' => $id_trabajador
                ], [
                    'numero_intentos' => 2
                ]);
            }

            $clase = Clase::find($id_clase);
            $clase->load(['trabajadores']);

            $res = MessageResponse::getInstance($status, self::HTTP_CREATED_MESSAGE, [$clase]);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al matricular la clase: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
            
        return $response->withStatus($status);
    }

    /**
     * Delete multiple clases from the parsed body of the HTTP request and writes the
     * JSON-encoded object to the response body. Sets the response content type to JSON.
     * 
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class If there was an error creating the Clase object
     * @return ResponseInterface The HTTP response object with the JSON-encoded Clase object in its body and a Content-Type header of 'application/json'
     * */
    public function desmatricular(Request $request, ResponseInterface $response)
    {
        $status = self::HTTP_OK;
        try {
            $body = $request->getParsedBody();
            $id_clase = $body['id_clase'];
            $ids_trabajadores = $body['ids_trabajadores'];
            ClaseTrabajador::where('id_clase', $id_clase)->whereIn('id_trabajador', $ids_trabajadores)->delete();
            $trabajadores = Clase::find($id_clase)->trabajadores;
            
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $trabajadores);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al desmatricular la clase: " . $e->errorInfo[2], []);
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
            $clase = ClaseTrabajador::find($id);
            $clase->update($request->getParsedBody());
            $clase->load(['clase', 'trabajador', 'usuario']);
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$clase]);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al actualizar la clase: " . $e->errorInfo[2], []);
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
            $clase = ClaseTrabajador::find($id);
            $clase->delete();
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, []);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al eliminar la clase: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }
}