<?php
namespace App\Utils;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class JsonResponseFactory
{
  public static function success(mixed $data = null, int $status = Response::HTTP_OK): JsonResponse
    {
        if (is_string($data) && !self::isJson($data)) {
            return new JsonResponse([
                'success' => true,
                'message' => $data,
            ], $status);
        }

        
        if (is_string($data) && self::isJson($data)) {
            return new JsonResponse([
                'success' => true,
                'data' => json_decode($data, true),
            ], $status);
        }

    
        return new JsonResponse([
            'success' => true,
            'data' => $data,
        ], $status);
    }

    public static function error(string|array $message, int $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {

        $response = [
            'success' => false,
            'error' => [
                'code' => $code,
            ],
        ];

        if (is_array($message)) {
            $response['error']['messages'] = $message;
        } else {
            $response['error']['message'] = $message;
        }

        return new JsonResponse($response, $code);
    }

    private static function isJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
  
}