<?php

class AuthMiddleware
{
    public static function handle()
    {
        $headers = getallheaders();

        // Check authorization header
        if (!isset($headers['Authorization'])) {

            http_response_code(401);

            echo json_encode([
                "status" => false,
                "message" => "Authorization header missing"
            ]);

            exit;
        }

        $authHeader = $headers['Authorization'];

        // Extract Bearer token
        if (
            !preg_match(
                '/Bearer\s(\S+)/',
                $authHeader,
                $matches
            )
        ) {

            http_response_code(401);

            echo json_encode([
                "status" => false,
                "message" => "Invalid token format"
            ]);

            exit;
        }
        // token after login
        $token = $matches[1];

        
        // Validate token
        $user = JWT::validate($token);

        if (!$user) {

            http_response_code(401);

            echo json_encode([
                "status" => false,
                "message" => "Invalid or expired token"
            ]);

            exit;
        }

        // Store authenticated user
        $_REQUEST['user'] = $user;
    }
}