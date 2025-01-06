<?php

require_once 'database.php';

class CommentController
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function get_comments(int $article_id)
    {
        try {
            $query = "SELECT c.id, c.comment, c.created_at, 
                             u.firstname, u.lastname
                      FROM Comment c
                      INNER JOIN User u ON c.user_id = u.id
                      WHERE c.article_id = :article_id
                      ORDER BY c.created_at DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ['success' => true, 'comments' => $result];
        } catch (PDOException $e) {
            error_log("Error getting comments: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function write_comment(int $user_id, int $article_id, string $comment)
    {
        try {
            $query = "INSERT INTO Comment (user_id, article_id, comment)
                     VALUES (:user_id, :article_id, :comment)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $result = $this->conn->lastInsertId();
                return ['success' => true, 'message' => 'Comment inserted', 'id' => $result];
            }
            return ['success' => false, 'message' => 'Error inserting comment'];
        } catch (PDOException $e) {
            error_log("Error inserting comment: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}
