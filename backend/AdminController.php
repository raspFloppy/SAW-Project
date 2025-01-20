<?php

require_once 'database.php';
require_once 'AuthController.php';

class AdminController extends AuthController
{

    private $db;
    private $conn;
    private $auth;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
        $this->auth = new AuthController();
    }

    public function get_all_users(int $admin_id)
    {
        if (!$this->auth->isUserLogged()) {
            return ['success' => false, 'message' => 'A User already logged in'];
        }

        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("SELECT * FROM User WHERE admin_id != :admin_id");
            $stmt->execute(['admin_id' => $admin_id]);
            if ($stmt->rowCount() > 0) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'No users found'];
            }
            $this->conn->commit();
            $result = $stmt->fetchAll();
            return ['success' => true, 'message' => 'Users found', 'data' => $result];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}
