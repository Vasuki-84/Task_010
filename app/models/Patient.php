<?php

class Patient
{
    private $conn;

    private $table = "patients";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // =========================
    // GET ALL PATIENTS
    // =========================
    public function getAll($userId)
    {
        $query = "SELECT * FROM {$this->table}  WHERE user_id = ?";

        $stmt = mysqli_prepare($this->conn,$query);

        mysqli_stmt_bind_param( $stmt, "i", $userId);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $patients = [];

        while ($row = mysqli_fetch_assoc($result)) {

            $patients[] = $row;
        }

        return $patients;
    }

    // =========================
    // CREATE PATIENT
    // =========================
    public function create($data)
    {
        $query = "INSERT INTO {$this->table}( name, age, gender, phone, address, diagnosis, user_id ) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare(
            $this->conn,
            $query
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sissssi",
            $data['name'],
            $data['age'],
            $data['gender'],
            $data['phone'],
            $data['address'],
            $data['diagnosis'],
            $data['user_id']
        );

        return mysqli_stmt_execute($stmt);
    }

    // =========================
    // UPDATE PATIENT
    // =========================
    public function update($id, $data)
    {
        $query = "UPDATE {$this->table} SET name = ?, age = ?, gender = ?, phone = ?, address = ?,  diagnosis = ? WHERE id = ?";

        $stmt = mysqli_prepare( $this->conn, $query);

        mysqli_stmt_bind_param(
            $stmt,
            "sissssi",
            $data['name'],
            $data['age'],
            $data['gender'],
            $data['phone'],
            $data['address'],
            $data['diagnosis'],
            $id
        );

        return mysqli_stmt_execute($stmt);
    }

    // =========================
    // DELETE PATIENT
    // =========================
    public function delete($id)
    {
        $query = "DELETE FROM {$this->table} WHERE id = ?";

        $stmt = mysqli_prepare(
            $this->conn,
            $query
        );

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $id
        );

        return mysqli_stmt_execute($stmt);
    }

    // =========================
    // FIND PATIENT BY ID
    // =========================
    public function findById($id, $userId)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = ? AND user_id = ?";

        $stmt = mysqli_prepare( $this->conn,  $query);

        mysqli_stmt_bind_param( $stmt, "ii", $id,$userId );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }
}
?>