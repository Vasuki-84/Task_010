<?php

class Response
{
    // Success Response
    public static function success(
        $message = "Success",
        $data = [],
        $statusCode = 200
    ) {

        http_response_code($statusCode);

        echo json_encode([
            "status" => true,
            "message" => $message,
            "data" => $data
        ]);

        exit;
    }

    // Error Response
    public static function error(
        $message = "Something went wrong",
        $statusCode = 500,
        $data = []
    ) {

        http_response_code($statusCode);

        echo json_encode([
            "status" => false,
            "message" => $message,
            "data" => $data
        ]);

        exit;
    }
}