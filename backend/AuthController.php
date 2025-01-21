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
        if ($this->isUserLogged()) {
            return ['success' => false, 'message' => 'A User already logged in'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid Email'];
        }

        if (empty($firstname) || empty($lastname) || empty($password)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("SELECT * FROM User WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->rowCount() > 0) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'User already exists'];
            }

            $stmt = $this->conn->prepare("INSERT INTO User (firstname, lastname, email, password) VALUES (:firstname, :lastname, :email, :password)");
            $stmt->execute([
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'password' => $hashed_password
            ]);

            $this->conn->commit();
            return ['success' => true, 'message' => 'Registration Successful'];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function login(string $email, string $password): array
    {
        if ($this->isUserLogged()) {
            return ['success' => false, 'message' => 'A User already logged in'];
        }

        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }

        try {
            $stmt = $this->conn->prepare("SELECT * FROM User WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);

                $_SESSION["id"] = $user['id'];
                $_SESSION["firstname"] = $user['firstname'];
                $_SESSION["lastname"] = $user['lastname'];
                $_SESSION["email"] = $user['email'];
                $_SESSION["created_at"] = $user['created_at'];
                $_SESSION["type"] = $user['type'];
                $_SESSION["loggedin"] = true;

                return [
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => [
                        'id' => $user['id'],
                        'firstname' => $user['firstname'],
                        'lastname' => $user['lastname'],
                        'email' => $user['email'],
                        'type' => $user['type'],
                        'created_at' => $user['created_at']
                    ]
                ];
            }
            return ['success' => false, 'message' => 'Invalid credentials'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function logout(): array
    {
        if (!$this->isUserLogged()) {
            return ['success' => false, 'message' => 'No user is logged in'];
        }

        try {
            $_SESSION = array();

            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 3600, '/');
            }

            session_destroy();
            return ['success' => true, 'message' => 'Logout successful'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error during logout: ' . $e->getMessage()];
        }
    }

    public function show_profile(): array
    {
        if (!$this->isUserLogged()) {
            return ['success' => false, 'message' => 'No user logged, nothing to show'];
        }

        return [
            'success' => true,
            'user' => [
                'id' => $_SESSION['id'],
                'firstname' => $_SESSION['firstname'],
                'lastname' => $_SESSION['lastname'],
                'email' => $_SESSION['email'],
                'type' => $_SESSION['type'],
                'created_at' => $_SESSION['created_at']
            ]
        ];
    }

    public function update_profile(string $firstname, string $lastname, string $email): array
    {
        if (!$this->isUserLogged()) {
            return ['success' => false, 'message' => 'No user logged, cannot update profile'];
        }

        if (empty($firstname) || empty($lastname) || empty($email)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid Email'];
        }

        try {
            $this->conn->beginTransaction();

            if ($email !== $_SESSION['email']) {
                $stmt = $this->conn->prepare("SELECT * FROM User WHERE email = :email AND id != :id");
                $stmt->execute([
                    'email' => $email,
                    'id' => $_SESSION['id']
                ]);
                if ($stmt->rowCount() > 0) {
                    $this->conn->rollBack();
                    return ['success' => false, 'message' => 'Email already exists'];
                }
            }

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

            $this->conn->commit();
            return [
                'success' => true,
                'message' => 'Profile update successful',
                'user' => [
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'email' => $email
                ]
            ];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function delete_profile(int $user_id)
    {
        if (!$this->isUserLogged()) {
            return ['success' => false, 'message' => 'No user logged, cannot delete profile'];
        }

        if ($user_id !== $_SESSION['id']) {
            return ['success' => false, 'message' => 'User not authorized'];
        }

        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("DELETE FROM Comment WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $user_id]);

            $stmt = $this->conn->prepare("DELETE FROM UserFavorite WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $user_id]);

            $stmt = $this->conn->prepare("DELETE FROM UserDislikes WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $user_id]);


            $stmt = $this->conn->prepare("DELETE FROM User WHERE id = :id");
            $stmt->execute(['id' => $user_id]);

            if ($stmt->rowCount() === 0) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'User not found'];
            }

            $this->conn->commit();
            return ['success' => true, 'message' => 'Profile deleted'];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    protected function isUserLogged(): bool
    {
        return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
    }

    protected function isUserAdmin(): bool
    {
        return isset($_SESSION['type']) && $_SESSION['type'] === 'admin';
    }
}
