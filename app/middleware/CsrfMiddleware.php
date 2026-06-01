<?php

class CsrfMiddleware
{
    public static function handle()
    {
        if (session_status() === PHP_SESSION_NONE) {
           session_start();
        }    
        $headers = getallheaders();
        $csrfToken = $headers['X-CSRF-Token'] ?? '';

        if (
            empty($_SESSION['csrf_token']) ||
            !hash_equals( $_SESSION['csrf_token'], $csrfToken)
        ) {

            http_response_code(403);

            echo json_encode([
                "status" => false,
                "message" =>
                "Invalid CSRF token"
            ]);

            exit;
        }
    }
}