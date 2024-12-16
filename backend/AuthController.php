<?php
include 'database.php';

class AuthController {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function register($username, $email, $password) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid Email'];
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            //TODO: make query more efficent
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
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Cannot register user because: ' . $e];
        }
    }

    public function login($email, $password) {
        session_start();

        try {
            $stmt = $this->conn->prepare("SELECT * FROM User WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $token = $this->generateJWT($user);
                
                $_SESSION["id"] = $user['id'];
                $_SESSION["username"] = $user['username'];
                $_SESSION["loggedin"] = TRUE;
                
                return [
                    'success' => true, 
                    'message' => 'Login successful', 
                    'token' => $token,
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username']
                    ]
                ];
            }
                return ['success' => false, 'message' => 'Invalid credentials'];
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Error, cannot login'];
        }
    }

    private function generateJWT($user) {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        
        $payload = base64_encode(json_encode([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'exp' => time() + (60 * 60 * 24)
        ]));

        $signature = hash_hmac('sha256', "$header.$payload", true);
        $signature = base64_encode($signature);

        return "$header.$payload.$signature";
    }

    public function validateToken($token) {
        try {
            list($header, $payload, $signature) = explode('.', $token);

            $valid_signature = hash_hmac('sha256', "$header.$payload", true);
            $valid_signature = base64_encode($valid_signature);

            if (!hash_equals($signature, $valid_signature)) {
                return ['success' => false, 'message' => 'Invalid signature'];
            }

            $payload_decoded = json_decode(base64_decode($payload), true);

            if ($payload_decoded['exp'] < time()) {
                return ['success' => false, 'message' => 'Token expired'];
            }

            return [
                'success' => true, 
                'message' => 'Token valido',
                'user_id' => $payload_decoded['user_id']
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error, cannot validate token'];
        }
    }

    public function logout() {
        try {
            session_start();
            $_SESSION = array();
            session_destroy();
            return ['success' => true, 'message' => 'Logout eseguito'];
        } catch(Exception $e) {
            return ['success' => false, 'message' => 'Error, cannot clear session'];
        }
    }
}