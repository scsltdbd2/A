<?php
function jsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function successResponse(array $data = [], string $message = 'Success'): void
{
    jsonResponse([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
}

function errorResponse(string $message, int $statusCode = 400, array $errors = []): void
{
    $response = [
        'success' => false,
        'message' => $message
    ];
    if (!empty($errors)) {
        $response['errors'] = $errors;
    }
    jsonResponse($response, $statusCode);
}

function notFoundResponse(string $message = 'Resource not found'): void
{
    errorResponse($message, 404);
}

function unauthorizedResponse(string $message = 'Unauthorized'): void
{
    errorResponse($message, 401);
}

function serverErrorResponse(string $message = 'Internal server error'): void
{
    errorResponse($message, 500);
}