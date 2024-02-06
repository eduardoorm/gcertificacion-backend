<?php

namespace App\Application\Controllers;

use App\Application\Auth\JWTHandler;
use App\Application\Factory\HttpStatusCodes;
use App\Application\Factory\MessageResponse;
use App\Application\Models\Trabajador;
use App\Application\Models\Usuario;
use Illuminate\Database\QueryException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class AuthController.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Controllers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class AuthController implements HttpStatusCodes
{

    /**
     * Sign in a user.
     * 
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @throws None
     * @return ResponseInterface response object containing JSON-encoded usuarios
     * */
    public function login(Request $request, ResponseInterface $response)
    {
        $status = self::HTTP_OK;
        $params = (array)$request->getParsedBody();
        $username = $params['usuario'];
        $password = $params['password'];

        // Generar el hash de la contraseña
        $hash_contraseña_generado = password_hash("test", PASSWORD_DEFAULT);
        print("HOLAAAAAAAAA");
        // Mostrar el hash generado
        echo "Hash generado: " . $hash_contraseña_generado;

        try 
        {
            $usuario = Usuario::where('usuario', $username)->first();
            $trabajador = $usuario->trabajador;
            $empresa = $trabajador ? $trabajador->empresaCliente : null;
            if (!$usuario) 
            {
                $status = self::HTTP_BAD_REQUEST;
                $res = MessageResponse::getInstance($status, "El usuario y contraseña no coinciden.", []);    
            }
            else if (!password_verify($password, $usuario->clave)) 
            {
                $status = self::HTTP_BAD_REQUEST;
                $res = MessageResponse::getInstance($status, "El usuario y contraseña no coincidennn.", []);    
            }
            else if ($usuario->activo == 0)
            {
                $status = self::HTTP_BAD_REQUEST;
                $res = MessageResponse::getInstance($status, "Su cuenta no se encuentra activa, por favor contacte a su empleador.", []);
            }
            else if($usuario->tipo == 'admin' || $usuario->tipo == 'empresa')
            {
                $dataToken = ['usuario' => $usuario->usuario,];
                $token = JWTHandler::generateToken($dataToken);
                $data = [
                    'token' => $token, 
                    'expiresIn' => 3600, 
                    'tokenType' => 'Bearer',
                    'id_trabajador' => $usuario->id_trabajador,
                    'id_empresa_cliente' => $empresa ? $empresa->id : null,
                    'razon_social' => $empresa ? $empresa->razon_social : null,
                    'ruc' => $empresa ? $empresa->ruc : null,
                    'logo' => $empresa ? $empresa->logo : null,
                    'nombres' => $usuario->nombres,
                    'apellidos' => $usuario->apellidos,
                    'tipo' => $usuario->tipo
                ];
                $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $data);
            }
            else if($usuario->tipo == 'trabajador')
            {
                $hayPeriodoActivo = false;
                foreach ($usuario->trabajador->clases as $clase) {
                    if ($clase->periodo->activo == 1){
                        $hayPeriodoActivo = true;
                        break;
                    }
                };
    
                if (!$hayPeriodoActivo)
                {
                    $status = self::HTTP_BAD_REQUEST;
                    $res = MessageResponse::getInstance($status, "Actualmente, no se encuentra matriculado en ningún curso", []);
                }
                else 
                    {
                    $dataToken = ['usuario' => $usuario->usuario,];
                    $token = JWTHandler::generateToken($dataToken);
                    $data = [
                        'token' => $token, 
                        'expiresIn' => 3600, 
                        'tokenType' => 'Bearer',
                        'id_trabajador' => $usuario->id_trabajador,
                        'id_empresa_cliente' => $empresa->id,
                        'razon_social' => $empresa->razon_social,
                        'ruc' => $empresa ? $empresa->ruc : null,
                        'logo' => $empresa->logo,
                        'nombres' => $usuario->nombres,
                        'apellidos' => $usuario->apellidos,
                        'tipo' => $usuario->tipo
                    ];
                    $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, $data);
                }
            }
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al autenticarse: " . ($e->errorInfo[2] ?? ""), []);
        }
        
        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }
}