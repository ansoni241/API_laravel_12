<?php
namespace App\Http\Responses;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Generate a success response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @param array $meta
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data, $message = 'Success', $statusCode = 200, $meta = [])
    {
        return response()->json([
            'success' => true,
            'statusCode' => $statusCode,
            'message' => $message,
            'error' => false,
            'data' => $data,
            'meta' => $meta,
        ], $statusCode);
    }

    /**
     * Generate an error response.
     *
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($message = 'Error', $statusCode, $data = [])
    {
        return response()->json([
            'success' => false,
            'statusCode' => $statusCode,
            'message' => $message,
            'error' => true,
            'data' => $data,
        ], $statusCode);
    }
}
