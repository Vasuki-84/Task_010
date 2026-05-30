<?php

class User
{
    private $conn;

    private $table = "users";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Find user by email
    public function findByEmail($email)
    {
        $query = "SELECT  id, name, email, password  FROM {$this->table} WHERE email = ? LIMIT 1";

        $stmt = mysqli_prepare($this->conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $email
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }

    // Create new user
    public function create($name, $email, $password)
    {
        $query = "INSERT INTO {$this->table}(name,email,password)VALUES (?, ?, ?)";

        $stmt = mysqli_prepare($this->conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "sss",
            $name,
            $email,
            $password
        );

        return mysqli_stmt_execute($stmt);
    }
    
   // Save refresh token & its expiry time in database
   public function saveRefreshToken($userId, $refreshToken, $expiry)
   {
    $query = "UPDATE {$this->table} SET refresh_token = ?,refresh_token_expiry = ? WHERE id = ?";

    $stmt = mysqli_prepare( $this->conn, $query);

    mysqli_stmt_bind_param(
        $stmt,
        "ssi",
        $refreshToken,
        $expiry,
        $userId
    );

    return mysqli_stmt_execute($stmt);
   }

  // Find user by refresh token:
  
  public function findByRefreshToken($refreshToken)
  {
    $query = "SELECT * FROM {$this->table} WHERE refresh_token = ? AND refresh_token_expiry > NOW() LIMIT 1";

    $stmt = mysqli_prepare( $this->conn,  $query );

    mysqli_stmt_bind_param( $stmt, "s", $refreshToken );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
   }

// Logout: REmove refresh token 
   public function removeRefreshToken($userId)
   {
    $query = "UPDATE {$this->table} SET refresh_token = NULL, refresh_token_expiry = NULL WHERE id = ?";

    $stmt = mysqli_prepare( $this->conn, $query);

    mysqli_stmt_bind_param( $stmt, "i", $userId);

    return mysqli_stmt_execute($stmt);
   }
   
 
}