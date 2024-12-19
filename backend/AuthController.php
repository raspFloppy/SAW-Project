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

    public function register(string $firstname, string $lastname, string $email, string $password): array
    {
        if ($this->is_user_logged()) {
            return ['success' => false, 'message' => 'A User already logged in'];
        }

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

            $stmt = $this->conn->prepare("INSERT INTO User (firstname, lastname, email, password) VALUES (:firstname, :lastname, :email, :password)");
            $stmt->execute([
                'firstname' => $firstname,
                'lastname' => $lastname,
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
        if ($this->is_user_logged()) {
            return ['success' => false, 'message' => 'A User already logged in'];
        }

        try {
            $stmt = $this->conn->prepare("SELECT * FROM User WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION["id"] = $user['id'];
                $_SESSION["firstname"] = $user['firstname'];
                $_SESSION["lastname"] = $user['lastname'];
                $_SESSION["email"] = $user['email'];
                $_SESSION["loggedin"] = true;

                return ['success' => true, 'message' => 'Login successful'];
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
            return ['success' => true, 'message' => 'Logout successful'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error, cannot clear session: ' . $e];
        }
    }

    public function show_profile(): array
    {
        session_start();

        if ($this->is_user_logged()) {
            return [
                'success' => true,
                'firstname' => $_SESSION['firstname'],
                'lastname' => $_SESSION['lastname'],
                'email' => $_SESSION['email']
            ];
        }

        return ['success' => false, 'message' => 'No user logged, nothing to show'];
    }

    public function update_profile(string $firstname, string $lastname, string $email): array
    {
        session_start();

        if ($this->is_user_logged()) {
            try {
                $stmt = $this->conn->prepare("UPDATE User SET firstname = :firstname, lastname = :lastname, email = :email WHERE id = :id");
                $stmt->execute([
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'email' => $email,
                    'id' => $_SESSION['id']
                ]);

                $_SESSION['firstname'] = $firstname;
                $_SESSION['lastname'] = $lastname;
                $_SESSION['email'] = $email;

                return ['success' => true, 'message' => 'Profile update successful'];
            } catch (PDOException $e) {
                return ['success' => false, 'message' => 'Error, cannot update profile: ' . $e];
            }
        }

        return ['success' => false, 'message' => 'No user logged, cannot update profile'];
    }

    private function is_user_logged(): bool
    {
        session_start();
        return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['id']);
    }
}
