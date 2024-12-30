<?php

require_once 'database.php';

class ArticleController
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function get_articles()
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM Article");
            $stmt->execute();
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ['success' => true, 'articles' => $articles];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function get_article(int $id)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM Article WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $article = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$article) {
                return ['success' => false, 'message' => 'Article not found'];
            }
            return ['success' => true, 'article' => $article];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}
