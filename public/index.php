<?php

require_once "../config/config.php";

require_once "../app/core/Database.php";
require_once "../app/core/Router.php";

require_once "../app/helpers/JWT.php";
require_once "../app/helpers/Response.php";

require_once "../app/middleware/JsonMiddleware.php";
require_once "../app/middleware/AuthMiddleware.php";

require_once "../app/models/User.php";
require_once "../app/models/Patient.php";

require_once "../app/controllers/AuthController.php";
require_once "../app/controllers/PatientController.php";

require_once "../app/middleware/CsrfMiddleware.php";
require_once "../app/helpers/Encryption.php";
// Run JSON middleware - Ensures request is JSON
JsonMiddleware::handle();

// Database connection - Connects MySQL using  mysqli
$db = (new Database())->connect();

// Initialize router - Used to match URL → controller function
$router = new Router();

// Controllers
$authController = new AuthController($db);

$patientController = new PatientController($db);

// Auth Routes
$router->add(
    'POST',
    '/api/register',
    [$authController, 'register']
);

$router->add(
    'POST',
    '/api/login',
    [$authController, 'login']
);

// Patient Routes
$router->add(
    'GET',
    '/api/patients',
    [$patientController, 'index']
);

$router->add(
    'POST',
    '/api/patients',
    [$patientController, 'store']
);

$router->add(
    'PUT',
    '/api/patients/{id}',
    [$patientController, 'update']
);

$router->add(
    'DELETE',
    '/api/patients/{id}',
    [$patientController, 'delete']
);

// Gets URL path from browser/Postman
$requestUri = parse_url(
    $_SERVER['REQUEST_URI'],
    PHP_URL_PATH
);

$requestUri = str_replace(
    '/task_010/public',
    '',
    $requestUri
);

// REFRESH token route
$router->add(
    'POST',
    '/api/token/refresh',
    [$authController, 'refresh']
);

$router->add(
    'POST',
    '/api/logout',
    [$authController, 'logout']
);

if (
    in_array(
        $requestUri,
        [
            '/api/token/refresh',
            '/api/logout'
        ]
    )
) {
    CsrfMiddleware::handle();
}

// FINAL step: 1. finds matching route, 2.calls controller method 3.returns response
$router->dispatch(
    $requestUri,
    $_SERVER['REQUEST_METHOD']
);




?>