<?php

namespace App\Application\Auth;

use Firebase\JWT\JWT;

/**
 * Class JWTHandler.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @package App\Auth
 * @subpackage Auth
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0 
 */
class JWTHandler
{
    /**
     * Generate a token.
     * 
     * @param array $data Token data.
     * @param int $expires Token expiration time.
     * @return string Token.
     * */
    public static function generateToken(array $data, int $expires = 3600): string
    {
        $payload = [
            'iss' => 'localhost',
            'sub' => $data['usuario'],
            'iat' => time(),
            'exp' => time() + $expires,
            'data' => $data,
        ];
        return JWT::encode($payload, 'supersecretkeyyoushouldnotcommittogithub', 'HS256');
    }

    /**
     * Verify a token.
     * 
     * @param string $token Token.
     * @return array Token data.
     * */
    public static function verifyToken(string $token): array
    {
        $decoded = JWT::decode($token, 'supersecretkeyyoushouldnotcommittogithub', ['HS256']);
        return $decoded->data;
    }
}