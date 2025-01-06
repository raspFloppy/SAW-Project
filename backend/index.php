<?php

session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

$allowed_origin = "http://localhost:5173";
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: " . $allowed_origin);
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'AuthController.php';
include 'ArticleController.php';
include 'CommentController.php';

$controller = new AuthController();
$articles_controller = new ArticleController();
$comments_controller = new CommentController();
$data = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'register':
        $result = $controller->register(
            $data['firstname'] ?? '',
            $data['lastname'] ?? '',
            $data['email'] ?? '',
            $data['password'] ?? ''
        );
        echo json_encode($result);
        break;

    case 'login':
        $result = $controller->login(
            $data['email'] ?? '',
            $data['password'] ?? ''
        );
        echo json_encode($result);
        break;

    case 'logout':
        $result = $controller->logout();
        echo json_encode($result);
        break;

    case 'show_profile':
        $result = $controller->show_profile();
        echo json_encode($result);
        break;

    case 'update_profile':
        $result = $controller->update_profile(
            $data['firstname'] ?? '',
            $data['lastname'] ?? '',
            $data['email'] ?? ''
        );
        echo json_encode($result);
        break;

    case 'get_articles':
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 3;
        $result = $articles_controller->get_articles($page, $per_page);
        echo json_encode($result);
        break;
    case 'get_article':
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid article ID']);
            break;
        }

        $result = $articles_controller->get_article($id, $user_id);
        echo json_encode($result);
        break;
    case 'set_favorite':
        $article_id = $data['article_id'] ?? null;
        $user_id = $data['user_id'] ?? null;
        if (!$article_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid article ID']);
            break;
        }

        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            break;
        }

        $result = $articles_controller->set_favorite($article_id, $user_id);
        echo json_encode($result);
        break;
    case 'get_favorites':
        $user_id = $data['user_id'] ?? null;
        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            break;
        }

        $result = $articles_controller->get_favorites($user_id);
        echo json_encode($result);
        break;
    case 'get_favorites_count':
        $user_id = $_GET['user_id'] ?? null;
        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            break;
        }

        $result = $articles_controller->get_favorites_count($user_id);
        echo json_encode($result);
        break;
    case 'get_comments':
        $article_id = $_GET['article_id'] ?? null;
        $user_id = $_GET['user_id'] ?? null;
        if (!$article_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid article ID']);
            break;
        }

        $result = $comments_controller->get_comments($article_id);
        echo json_encode($result);
        break;
    case 'write_comment':
        $user_id = $data['user_id'] ?? null;
        $article_id = $data['article_id'] ?? null;
        $comment = $data['comment'] ?? null;
        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            break;
        }

        if (!$article_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid article ID']);
            break;
        }

        if (!$comment) {
            echo json_encode(['success' => false, 'message' => 'Invalid comment']);
            break;
        }

        $result = $comments_controller->write_comment($user_id, $article_id, $comment);
        echo json_encode($result);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid Action']);
        break;
}
