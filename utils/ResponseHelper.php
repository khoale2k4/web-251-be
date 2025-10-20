<?php

class ResponseHelper {
    
    /**
     * Trả về response success
     */
    public static function success($data = null, $message = 'Success', $statusCode = 200) {
        http_response_code($statusCode);
        $response = [
            'success' => true,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response);
    }
    
    /**
     * Trả về response error
     */
    public static function error($message = 'Error', $statusCode = 400, $errors = null) {
        http_response_code($statusCode);
        $response = [
            'success' => false,
            'message' => $message
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        echo json_encode($response);
    }
    
    /**
     * Trả về response với pagination
     */
    public static function paginated($data, $page, $limit, $total, $message = 'Success') {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ]);
    }
    
    /**
     * Response 404 Not Found
     */
    public static function notFound($message = 'Resource not found') {
        self::error($message, 404);
    }
    
    /**
     * Response 401 Unauthorized
     */
    public static function unauthorized($message = 'Unauthorized') {
        self::error($message, 401);
    }
    
    /**
     * Response 403 Forbidden
     */
    public static function forbidden($message = 'Forbidden') {
        self::error($message, 403);
    }
    
    /**
     * Response 500 Internal Server Error
     */
    public static function serverError($message = 'Internal server error') {
        self::error($message, 500);
    }
    
    /**
     * Response 201 Created
     */
    public static function created($data = null, $message = 'Created successfully') {
        self::success($data, $message, 201);
    }
    
    /**
     * Response 204 No Content
     */
    public static function noContent() {
        http_response_code(204);
        exit;
    }
    
    /**
     * Response validation error
     */
    public static function validationError($errors, $message = 'Validation failed') {
        self::error($message, 422, $errors);
    }
}
