<?php

namespace App\Application\Controllers;

use App\Application\Factory\HttpStatusCodes;
use App\Application\Factory\MessageResponse;
use App\Application\Models\Clase;
use App\Application\Models\Trabajador;
use Illuminate\Database\QueryException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class TrabajadorController.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Controllers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class TrabajadorController implements HttpStatusCodes
{
    /**
     * Retrieves all the trabajadores and returns a JSON-encoded response.
     *
     * @param ResponseInterface $response response object to be returned
     * @throws None
     * @return ResponseInterface response object containing JSON-encoded trabajadores
     */
    public function index(ResponseInterface $response)
    {
        $status = self::HTTP_OK;
        try {
            $trabajadores = Trabajador::all();
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $trabajadores);
        }
        catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la lista de trabajadores: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Retrieves a trabajador by ID and returns it as a JSON response.
     *
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if trabajador is not found
     * @return ResponseInterface HTTP response object with JSON data and headers
     */
    public function find(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];
        try {
            $trabajador = Trabajador::find($id);

            if (!$trabajador) {
                $status = self::HTTP_NOT_FOUND;
                $res = MessageResponse::getInstance($status, self::HTTP_NOT_FOUND_MESSAGE, []);
            }
            else {
                $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$trabajador]);
            }
        }
        catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener el trabajador: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Retrieves all clases for a trabajador and returns a JSON-encoded response.
     * 
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @param array $args Route parameters
     * @throws None
     * @return ResponseInterface response object containing JSON-encoded clases
     * */
    public function clases(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];
        try {
            $trabajador = Trabajador::find($id);

            if(!$trabajador) {
                $status = self::HTTP_NOT_FOUND;
                $res = MessageResponse::getInstance($status, "Trabajador no encontrado", []);
            }
            else {
                $clases = $trabajador->clases()->whereRelation('periodo', 'activo', 1)->get();
                $clases->load('archivos');
                $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $clases);
            }
        }
        catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener las clases: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Retrieves all clases of a tipo for a trabajador and returns a JSON-encoded response.
     * 
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @param array $args Route parameters
     * @throws None
     * @return ResponseInterface response object containing JSON-encoded clases
     * */
    public function clasesByTipo(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];
        $tipo_clase = $args['tipo'];
        try {
            $trabajador = Trabajador::find($id);

            if(!$trabajador) {
                $status = self::HTTP_NOT_FOUND;
                $res = MessageResponse::getInstance($status, "Trabajador no encontrado", []);
            }
            else {
                $clases = $trabajador->clases()->where('tipo', $tipo_clase)->whereRelation('periodo', 'activo', 1)->get();
                $clases->load('archivos');
                $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $clases);
            }
        }
        catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener las clases: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**  
     * 
     */

    /**
     * Retrieves all trabajadores matriculados and returns a JSON-encoded response.
     * 
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @param array $args Route parameters
     * @throws None
     * @return ResponseInterface response object containing JSON-encoded trabajadores
     * */
    public function matriculados(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        try {
            $clase = Clase::find($args['id']);
            $trabajadores = $clase->trabajadores;
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $trabajadores);
        }
        catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la lista de trabajadores matriculados: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Deletes a multiple trabajadores from the database.
     * 
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * 
     * @throws None
     * @return ResponseInterface The HTTP response object
     * */
    public function deleteMultiple(Request $request, ResponseInterface $response)
    {
        $status = self::HTTP_OK;
        try {
            $body = $request->getParsedBody();
            $ids_trabajadores = $body['ids_trabajadores'];

            //TODO: Validar que el trabajador no tenga clases
            //TODO: Validar que el trabajador no tenga archivos
            //TODO: Validar si se puede hacer una eliminación en bloque
            $trabajadores = Trabajador::whereIn('id', $ids_trabajadores)->get();

            foreach ($ids_trabajadores as $id_trabajador) {
                Trabajador::find($id_trabajador)->usuario()->delete();
                Trabajador::find($id_trabajador)->delete();
            }
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$trabajadores]);
        }
        catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al eliminar los trabajadores: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /*  Retrieve the signature of a trabajador by ID and return it as a JSON response.
     * 
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if trabajador is not found
     * @return ResponseInterface The HTTP response object with the JSON-encoded Trabajador object in its body and a Content-Type header of 'application/json'
     */

    public function getSignature(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];
        try {
            $trabajador = Trabajador::find($id);

            if (!$trabajador) {
                $status = self::HTTP_NOT_FOUND;
                $res = MessageResponse::getInstance($status, "Trabajador no encontrado", []);
            } else {
                $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $trabajador->signature);
            }
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la firma: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status); 
    }

    /**
     * Add imagen signature to camp of table trabajador utilizando algo similar:    'certificado' => 'https://backlab.gcertificacion.pe/api/certificados/' . $filename . '.pdf',
.
     */
    public function addSignature(Request $request, ResponseInterface $response)
    {
        $status = self::HTTP_OK;
        try {
            // Get the trabajador ID from the request
            $id = $request->getParsedBody()['id_trabajador'];
            // Check if the trabajador exists
            $trabajador = Trabajador::find($id);
            if (!$trabajador) {
                $status = self::HTTP_NOT_FOUND;
                $res = MessageResponse::getInstance($status, "Trabajador no encontrado", []);
            } else {
                // Get the image data from the request
                $imageData = $request->getParsedBody()['image'];

                // Decode the image data
                $image = base64_decode($imageData);

                // Generate a unique filename for the image
                $filename = uniqid() . '.png'; // You may need to change the extension depending on the image type

                // Save the image to the uploads folder
                file_put_contents(dirname(__DIR__).'/../../uploads/signature/' . $filename, $image);

                // Update the trabajador's firma field with the image path
                $trabajador->signature = dirname(__DIR__).'/../../uploads/signature/' . $filename;
                $trabajador->save();
    
                $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $trabajador);
            }
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al agregar la firma: " . $e->errorInfo[2], []);
        }
    
        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
    
        return $response->withStatus($status);
    }

    /**
     * Creates a new Trabajador object from the parsed body of the HTTP request and writes the 
     * JSON-encoded object to the response body. Sets the response content type to JSON.
     *
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @throws Some_Exception_Class If there was an error creating the Trabajador object
     * @return ResponseInterface The HTTP response object with the JSON-encoded Trabajador object in its body and a Content-Type header of 'application/json'
     */
    public function store(Request $request, ResponseInterface $response)
    {
        $trabajador = Trabajador::create($request->getParsedBody());
        $response->getBody()->write(json_encode($trabajador, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Updates a trabajador with the given ID.
     *
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if trabajador is not found
     * @return ResponseInterface The HTTP response object with the JSON-encoded Trabajador object in its body and a Content-Type header of 'application/json'
     */
    public function update(Request $request, ResponseInterface $response, array $args)
    {
        $trabajador = Trabajador::find($args['id']);
        $trabajador->update($request->getParsedBody());
        $response->getBody()->write(json_encode($trabajador, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }
    

    /**
     * Deletes a trabajador with the given ID.
     *
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if trabajador is not found
     * @return ResponseInterface The HTTP response object with the JSON-encoded Trabajador object in its body and a Content-Type header of 'application/json'
     */
    public function delete(Request $request, ResponseInterface $response, array $args)
    {
        $trabajador = Trabajador::find($args['id']);
        $trabajador->delete();
        $response->getBody()->write(json_encode($trabajador, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }
}

