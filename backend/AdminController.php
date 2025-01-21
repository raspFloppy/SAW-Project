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

    public function get_all_users()
    {
        if (!$_SESSION['id']) {
            return ['success' => false, 'message' => 'User is not logged'];
        }

        if (!$this->auth->isUserAdmin()) {
            return ['success' => false, 'message' => 'User in not admin'];
        }
        $id = $_SESSION['id'];

        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("
                SELECT 
                    u.id, 
                    u.firstname, 
                    u.lastname, 
                    u.email, 
                    u.created_at, 
                    u.type, 
                    COUNT(DISTINCT c.id) AS comments, 
                    COUNT(DISTINCT d.article_id) AS dislikes, 
                    COUNT(DISTINCT f.article_id) AS favorites
                FROM User u
                LEFT JOIN Comment c ON u.id = c.user_id
                LEFT JOIN UserDislikes d ON u.id = d.user_id
                LEFT JOIN UserFavorite f ON u.id = f.user_id
                WHERE u.id != :id
                GROUP BY u.id, u.firstname, u.lastname, u.email, u.created_at, u.type
            ");

            $stmt->execute(['id' => $id]);
            if ($stmt->rowCount() <= 0) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'No users found'];
            }
            $this->conn->commit();
            $result = $stmt->fetchAll();
            return ['success' => true, 'message' => 'Users found', 'users' => $result];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function count_all_users()
    {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM User");
            $count = $stmt->fetchColumn();
            return ['success' => true, 'count' => $count];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function count_all_dislikes()
    {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM UserDislikes");
            $count = $stmt->fetchColumn();
            return ['success' => true, 'count' => $count];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function count_all_favorites()
    {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM UserFavorite");
            $count = $stmt->fetchColumn();
            return ['success' => true, 'count' => $count];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function update_article(int $article_id, int $admin_id, string $title, string $content, string $author)
    {
        if (empty($title) || empty($content) || empty($author)) {
            return ['success' => false, 'message' => 'Title, content and author are required'];
        }

        if (!$this->auth->isUserLogged()) {
            return ['success' => false, 'message' => 'User not logged in'];
        }

        if ($admin_id !== $_SESSION['id']) {
            return ['success' => false, 'message' => 'User not authorized'];
        }

        if ($_SESSION['type'] !== 'admin') {
            return ['success' => false, 'message' => 'User not an Admin'];
        }

        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("UPDATE Article SET title = :title, content = :content, author = :author WHERE id = :article_id");
            $stmt->execute(['title' => $title, 'content' => $content, 'author' => $author, 'article_id' => $article_id]);
            if ($stmt->rowCount() === 0) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Article not found'];
            }
            $this->conn->commit();
            return ['success' => true, 'message' => 'Article updated'];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function delete_article($article_id)
    {
        if (!$this->auth->isUserLogged()) {
            return ['success' => false, 'message' => 'User not logged in'];
        }

        if ($_SESSION['type'] !== 'admin') {
            return ['success' => false, 'message' => 'User not an Admin'];
        }

        try {
            $this->conn->beginTransaction();

            $tables = [
                'Comment',
                'UserDislikes',
                'UserFavorite'
            ];

            foreach ($tables as $table) {
                $stmt = $this->conn->prepare("DELETE FROM $table WHERE article_id = :article_id");
                $stmt->execute(['article_id' => $article_id]);
            }

            $stmt = $this->conn->prepare("DELETE FROM Article WHERE id = :article_id");
            $stmt->execute(['article_id' => $article_id]);

            if ($stmt->rowCount() === 0) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Article not found'];
            }

            $this->conn->commit();
            return ['success' => true, 'message' => 'Article and related records deleted'];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }


    public function delete_user(int $user_id, int $admin_id)
    {
        if (!$this->auth->isUserLogged()) {
            return ['success' => false, 'message' => 'User not logged in'];
        }

        if ($_SESSION['type'] !== 'admin') {
            return ['success' => false, 'message' => 'User not an Admin'];
        }

        if ($admin_id === $user_id) {
            return ['success' => false, 'message' => 'Admin cannot delete itself'];
        }

        try {
            $this->conn->beginTransaction();

            $tables = [
                'Comment',
                'UserDislikes',
                'UserFavorite',
            ];

            foreach ($tables as $table) {
                $stmt = $this->conn->prepare("DELETE FROM $table WHERE user_id = :user_id");
                $stmt->execute(['user_id' => $user_id]);
            }

            $stmt = $this->conn->prepare("DELETE FROM User WHERE id = :user_id");
            $stmt->execute(['user_id' => $user_id]);


            if ($stmt->rowCount() === 0) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'User not found'];
            }

            $this->conn->commit();
            return ['success' => true, 'message' => 'User and related records deleted'];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function change_user_type(int $user_id, int $admin_id, string $new_role)
    {
        if (!$this->auth->isUserLogged()) {
            return ['success' => false, 'message' => 'User not logged in'];
        }

        if ($_SESSION['type'] !== 'admin') {
            return ['success' => false, 'message' => 'User not an Admin'];
        }

        if ($admin_id === $user_id) {
            return ['success' => false, 'message' => 'Admin cannot change their own role'];
        }

        if (!in_array($new_role, ['normal', 'admin'])) {
            return ['success' => false, 'message' => 'Invalid role type ' . $new_role];
        }

        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("UPDATE User SET type = :new_role WHERE id = :user_id");
            $stmt->execute(['new_role' => $new_role, 'user_id' => $user_id]);

            if ($stmt->rowCount() === 0) {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'User not found'];
            }

            $this->conn->commit();
            return ['success' => true, 'message' => 'User role updated successfully'];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}
