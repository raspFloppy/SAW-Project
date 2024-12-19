<?php
require_once 'database.php';

class AuthController
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function register(string $username, string $email, string $password): array
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid Email'];
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $this->conn->prepare("SELECT * FROM User WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'User already exists'];
            }

            $stmt = $this->conn->prepare("INSERT INTO User (username, email, password) VALUES (:username, :email, :password)");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashed_password
            ]);
            return ['success' => true, 'message' => 'Registration Successful'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => "Database error: {$e}"];
        }
    }

    public function login(string $email, string $password): array
    {
        session_start();

        try {
            $stmt = $this->conn->prepare("SELECT * FROM User WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION["id"] = $user['id'];
                $_SESSION["username"] = $user['username'];
                $_SESSION["loggedin"] = true;

                return [
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'loggedin' => true,
                    ]

                ];
            }
            return ['success' => false, 'message' => 'Invalid credentials'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error, cannot login'];
        }
    }

    public function logout(): array
    {
        try {
            session_start();
            session_unset();
            session_destroy();
            return ['success' => true, 'message' => 'Logout eseguito'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error, cannot clear session'];
        }
    }
}
