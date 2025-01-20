<?php

require_once 'database.php';

class ArticleController extends AuthController
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

    public function get_articles(int $page = 1, int $per_page = 3)
    {
        try {
            $offset = ($page - 1) * $per_page;

            $count_stmt = $this->conn->query("SELECT COUNT(*) FROM Article");
            $total = $count_stmt->fetchColumn();

            $stmt = $this->conn->prepare("SELECT * FROM Article ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'articles' => $articles,
                'total' => $total,
                'current_page' => $page,
                'per_page' => $per_page,
                'last_page' => ceil($total / $per_page)
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function get_article(int $article_id, int $user_id = null)
    {
        try {
            if ($user_id) {
                $query = "
                SELECT 
                    a.*,
                    CASE WHEN uf.article_id IS NOT NULL THEN TRUE ELSE FALSE END as is_favorite
                FROM Article a
                LEFT JOIN UserFavorite uf ON a.id = uf.article_id AND uf.user_id = :user_id
                WHERE a.id = :article_id";

                $stmt = $this->conn->prepare($query);
                $stmt->execute([
                    'article_id' => $article_id,
                    'user_id' => $user_id
                ]);
            } else {
                $query = "
                SELECT 
                    a.*,
                    FALSE as is_favorite
                FROM Article a
                WHERE a.id = :article_id
            ";

                $stmt = $this->conn->prepare($query);
                $stmt->execute([
                    'article_id' => $article_id
                ]);
            }

            $article = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($article) {
                $article['is_favorite'] = (bool)$article['is_favorite'];
                return ['success' => true, 'article' => $article];
            }

            return ['success' => false, 'message' => 'Article not found'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function set_favorite(int $article_id, int $user_id)
    {
        try {
            $check_stmt = $this->conn->prepare("SELECT * FROM UserFavorite WHERE article_id = :article_id AND user_id = :user_id");
            $check_stmt->execute(['article_id' => $article_id, 'user_id' => $user_id]);
            $favorite = $check_stmt->fetch(PDO::FETCH_ASSOC);

            if ($favorite) {
                $stmt = $this->conn->prepare("DELETE FROM UserFavorite WHERE article_id = :article_id AND user_id = :user_id");
            } else {
                $stmt = $this->conn->prepare("INSERT INTO UserFavorite (article_id, user_id) VALUES (:article_id, :user_id)");
            }
            $stmt->execute(['article_id' => $article_id, 'user_id' => $user_id]);
            return ['success' => true, 'message' => 'Article preference updated'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function get_favorites(int $user_id)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM UserFavorite WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $user_id]);
            $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ['success' => true, 'favorites' => $favorites];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function get_favorites_count(int $user_id)
    {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM UserFavorite WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $user_id]);
            $count = $stmt->fetchColumn();
            return ['success' => true, 'count' => $count];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function get_article_favorites_count(int $article_id)
    {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM UserFavorite WHERE article_id = :article_id");
            $stmt->execute(['article_id' => $article_id]);
            $count = $stmt->fetchColumn();
            return ['success' => true, 'count' => $count];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function isFavorite(int $article_id, int $user_id)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM UserFavorite WHERE article_id = :article_id AND user_id = :user_id");
            $stmt->execute(['article_id' => $article_id, 'user_id' => $user_id]);
            $favorite = $stmt->fetch(PDO::FETCH_ASSOC);
            return ['success' => true, 'is_favorite' => $favorite ? true : false];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function writeArticle(string $title, string $content, string $author, int $admin_id)
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
            $stmt = $this->conn->prepare("INSERT INTO Article (title, content, author) VALUES (:title, :content, :author)");
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt->bindParam(':author', $author, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $article_id = $this->conn->lastInsertId();
                return ['success' => true, 'message' => 'Article inserted', 'article_id' => $article_id];
            }
            return ['success' => false, 'message' => 'Error inserting article'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}
