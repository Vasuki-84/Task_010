<?php
session_start();

if (empty($_SESSION['csrf_token'])) {

    $_SESSION['csrf_token'] =
        bin2hex(random_bytes(32));
}

class AuthController
{
    private $userModel;

    public function __construct($db)
    {
        $this->userModel = new User($db);
    }

    // Register User
    public function register()
    {   // receives data from postman 
        $data = $_REQUEST['body'];

        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // Validation
        if (!$name || !$email || !$password) {

            http_response_code(400);

            echo json_encode([
                "status" => false,
                "message" => "All fields are required"
            ]);

            return;
        }

        // Check existing email
        $existingUser = $this->userModel->findByEmail($email);

        if ($existingUser) {

            http_response_code(400);

            echo json_encode([
                "status" => false,
                "message" => "Email already exists"
            ]);

            return;
        }

        // Hash password
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT   // Uses secure hashing algorithm automatically.
        );

        // Create user navigates to user.php
        $created = $this->userModel->create(
            $name,
            $email,
            $hashedPassword
        );

        if ($created) {

            echo json_encode([
                "status" => true,
                "message" => "User registered successfully"
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "status" => false,
                "message" => "Registration failed"
            ]);
        }
    }

    // Login User
    public function login()
    {
        $data = $_REQUEST['body'];

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // Check user
        $user = $this->userModel->findByEmail($email);

        if (
            !$user ||
            !password_verify(
                $password,
                $user['password']
            )
        ) {

            http_response_code(401);

            echo json_encode([
                "status" => false,
                "message" => "Invalid credentials"
            ]);

            return;
        }

        // Generate JWT
        $token = JWT::generate($user);

        // Generate refresh token:
        // bin2hex() -> Converts binary → readable string
        // random_bytes(40) -> PHP's built-in cryptographic function,40 bytes secure random binary data,40 bytes = 320 bits random data (more secure)
        $refreshToken = bin2hex(random_bytes(40));

        $expiry = date("Y-m-d H:i:s",  time() + $_ENV['REFRESH_TOKEN_EXPIRY']);
        // store refresh token in databases
        $this->userModel->saveRefreshToken($user['id'],$refreshToken, $expiry);

        // Store refresh token in COOKIE
        setcookie("refresh_token",$refreshToken,
        [
        "expires" => time() + $_ENV['REFRESH_TOKEN_EXPIRY'],
        "path" => "/",
        "httponly" => true
        ]);

        // Postman Response 
        echo json_encode([
           "status" => true,
           "message" => "Login successful",

           "access_token" => $token,
 
           "access_token_expires_in" =>
            $_ENV['JWT_EXPIRY'],

            "csrf_token" => $_SESSION['csrf_token']
        ]);
    }

    public function refresh(){
    $refreshToken =
        $_COOKIE['refresh_token'] ?? null;

    if (!$refreshToken) {

        http_response_code(401);

        echo json_encode([
            "status" => false,
            "message" => "Refresh token missing"
        ]);

        return;
    }

    $user = $this->userModel
        ->findByRefreshToken($refreshToken);

    if (!$user) {

        http_response_code(401);

        echo json_encode([
            "status" => false,
            "message" => "Invalid or expired refresh token"
        ]);

        return;
    }

    // Generate new access token
    $newAccessToken = JWT::generate($user);
 
   echo json_encode([
        "status" => true,
        "message" => "Token refreshed successfully",
        "access_token" => $newAccessToken,
        "access_token_expires_in" => $_ENV['JWT_EXPIRY']
    ]);
    }

    // LOGOUT user
    public function logout()
{
    $refreshToken =
        $_COOKIE['refresh_token'] ?? null;

    if (!$refreshToken) {

        http_response_code(401);

        echo json_encode([
            "status" => false,
            "message" => "Refresh token missing"
        ]);

        return;
    }

    // Find user using refresh token
    $user = $this->userModel
        ->findByRefreshToken($refreshToken);

    if (!$user) {

        http_response_code(401);

        echo json_encode([
            "status" => false,
            "message" => "Invalid refresh token"
        ]);

        return;
    }

    // Remove refresh token from database
    $this->userModel
        ->removeRefreshToken($user['id']);

    // Remove refresh token cookie
    setcookie(
        "refresh_token",
        "",
        [
            "expires" => time() - 3600,
            "path" => "/",
            "httponly" => true
        ]
    );

    // Destroy session
    session_destroy();

    echo json_encode([
        "status" => true,
        "message" => "Logout successful"
    ]);
}

}