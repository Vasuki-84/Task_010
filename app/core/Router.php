<?php

class Router
{
    private $routes = [];

    // Add route
    public function add($method, $uri, $action)
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action
        ];
    }

    // Dispatch route
    public function dispatch($requestUri, $requestMethod)
    {
        foreach ($this->routes as $route) {

            // Dynamic route parameter conversion => /api/patients/([0-9]+)
            $pattern = preg_replace(
                '#\{id\}#',
                '([0-9]+)',
                $route['uri']
            );

            // Add start and end regex symbols
            $pattern = "#^" . $pattern . "$#";

            // Check route match
            if (
                $route['method'] === $requestMethod &&
                preg_match($pattern, $requestUri, $matches)
            ) {

                // Remove full match
                array_shift($matches);
                
                // Array Destructuring
                [$controller, $method] = $route['action'];

                // Call controller method
                call_user_func_array(
                    [$controller, $method],
                    $matches
                );

                return;
            }
        }

        // Route not found
        http_response_code(404);

        echo json_encode([
            "status" => false,
            "message" => "Route not found"
        ]);
    }
}