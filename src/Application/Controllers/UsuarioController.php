<?php

namespace App\Application\Controllers;

use App\Application\Factory\HttpStatusCodes;
use App\Application\Factory\MessageResponse;
use App\Application\Models\Usuario;
use Illuminate\Database\QueryException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class UsuarioController.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Controllers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class UsuarioController implements HttpStatusCodes
{
    /**
     * Retrieves all the usuarios and returns a JSON-encoded response.
     *
     * @param Request $request HTTP request object
     * @param ResponseInterface $response response object to be returned
     * @throws None
     * @return ResponseInterface response object containing JSON-encoded usuarios
     */
    public function index(Request $request, ResponseInterface $response)
    {
        $status = self::HTTP_OK;
        try {
            $usuarios = Usuario::all();
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $usuarios);
        }
        catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la lista de usuarios: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Retrieves a user by ID and returns it as a JSON response.
     *
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if user is not found
     * @return ResponseInterface HTTP response object with JSON data and headers
     */
    public function find(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];
        try {
            $usuario = Usuario::find($id);
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $usuario);
        }
        catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener el usuario: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Creates a new Usuario object from the parsed body of the HTTP request and writes the 
     * JSON-encoded object to the response body. Sets the response content type to JSON.
     *
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @throws Some_Exception_Class If there was an error creating the Usuario object
     * @return ResponseInterface The HTTP response object with the JSON-encoded Usuario object in its body and a Content-Type header of 'application/json'
     */
    public function store(Request $request, ResponseInterface $response)
    {
        $status = self::HTTP_CREATED;
        try {
            $usuario = Usuario::create($request->getParsedBody());
            $res = MessageResponse::getInstance($status, self::HTTP_CREATED_MESSAGE, $usuario);
        }
        catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al crear el usuario: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Updates a user with the given ID.
     *
     * @param Request $request The HTTP request object.
     * @param ResponseInterface $response The HTTP response object.
     * @param array $args An array of route parameters.
     *
     * @throws Some_Exception_Class If the user cannot be found or updated.
     *
     * @return ResponseInterface The updated user as a JSON string.
     */
    public function update(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];

        try {
            $usuario = Usuario::find($id);
            $usuario->update($request->getParsedBody());
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $usuario);
        }
        catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al actualizar el usuario: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    /**
     * Deletes the user with the given ID and returns a JSON response
     * containing the deleted user.
     *
     * @param Request $request The HTTP request object.
     * @param ResponseInterface $response The HTTP response object.
     * @param array $args An array containing the ID of the user to delete.
     * @throws Some_Exception_Class If the user cannot be found or deleted.
     * @return ResponseInterface A JSON response containing the deleted user.
     */
    public function delete(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];
        try {
            $usuario = Usuario::find($id);
            $usuario->delete();
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $usuario);
        }
        catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al eliminar el usuario: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }
}