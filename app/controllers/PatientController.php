<?php

class PatientController
{
    private $patientModel;

    public function __construct($db)
    {
        $this->patientModel = new Patient($db);
    }

    // =========================
    // GET ALL PATIENTS
    // =========================
    public function index()
    {
        AuthMiddleware::handle();

        $userId = $_REQUEST['user']['user_id'];

        $patients = $this->patientModel->getAll($userId);

        // Decrypt sensitive fields
        foreach ($patients as &$patient) {

            $patient['name'] =
                Encryption::decrypt(
                    $patient['name']
                );

            $patient['phone'] =
                Encryption::decrypt(
                    $patient['phone']
                );

            $patient['address'] =
                Encryption::decrypt(
                    $patient['address']
                );

            $patient['diagnosis'] =
                Encryption::decrypt(
                    $patient['diagnosis']
                );
        }

        echo json_encode([
            "status" => true,
            "data" => $patients
        ]);
    }

    // =========================
    // CREATE PATIENT
    // =========================
    public function store()
    {
        AuthMiddleware::handle();

        $data = $_REQUEST['body'];

        $data['user_id'] = $_REQUEST['user']['user_id'];

        if (
            empty($data['name']) ||
            empty($data['age']) ||
            empty($data['gender']) ||
            empty($data['phone']) ||
            empty($data['address']) ||
            empty($data['diagnosis'])
        ) {

            http_response_code(400);

            echo json_encode([
                "status" => false,
                "message" => "All fields are required"
            ]);

            return;
        }

        // Encrypt sensitive fields
        
         $data['name'] =
            Encryption::encrypt(
                $data['name']
            );

        $data['phone'] =
            Encryption::encrypt(
                $data['phone']
            );

        $data['address'] =
            Encryption::encrypt(
                $data['address']
            );

        $data['diagnosis'] =
            Encryption::encrypt(
                $data['diagnosis']
            );

        $created =
            $this->patientModel->create($data);

        if ($created) {

            echo json_encode([
                "status" => true,
                "message" =>
                    "Patient created successfully"
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "status" => false,
                "message" =>
                    "Failed to create patient"
            ]);
        }
    }

    // =========================
    // UPDATE PATIENT
    // =========================
    public function update($id)
    {
        AuthMiddleware::handle();

        $data = $_REQUEST['body'];

        $userId = $_REQUEST['user']['user_id'];

        $patient = $this->patientModel->findById( $id, $userId );

        if (!$patient) {

            http_response_code(404);

            echo json_encode([
                "status" => false,
                "message" => "Patient not found"
            ]);

            return;
        }

        // Encrypt sensitive fields

        if (isset($data['name'])) {

            $data['name'] =
                Encryption::encrypt(
                    $data['name']
                );
        }

        if (isset($data['phone'])) {

            $data['phone'] =
                Encryption::encrypt(
                    $data['phone']
                );
        }

        if (isset($data['address'])) {

            $data['address'] =
                Encryption::encrypt(
                    $data['address']
                );
        }

        if (isset($data['diagnosis'])) {

            $data['diagnosis'] =
                Encryption::encrypt(
                    $data['diagnosis']
                );
        }

        $updated =
            $this->patientModel->update( $id, $data );

        if ($updated) {

            echo json_encode([
                "status" => true,
                "message" =>
                    "Patient updated successfully"
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "status" => false,
                "message" =>
                    "Failed to update patient"
            ]);
        }
    }

    // =========================
    // DELETE PATIENT
    // =========================
    public function delete($id)
    {
        AuthMiddleware::handle();

        $userId = $_REQUEST['user']['user_id'];

        $patient =  $this->patientModel->findById($id, $userId );

        if (!$patient) {

            http_response_code(404);

            echo json_encode([
                "status" => false,
                "message" => "Patient not found"
            ]);

            return;
        }

        $deleted =  $this->patientModel->delete($id);

        if ($deleted) {

            echo json_encode([
                "status" => true,
                "message" =>
                    "Patient deleted successfully"
            ]);

        } else {

            http_response_code(500);

            echo json_encode([
                "status" => false,
                "message" =>
                    "Failed to delete patient"
            ]);
        }
    }
}