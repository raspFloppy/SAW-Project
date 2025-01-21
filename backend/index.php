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
include 'AdminController.php';

$controller = new AuthController();
$articles_controller = new ArticleController();
$comments_controller = new CommentController();
$admin_controller = new AdminController();

$data = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? '';

//TODO:  sanitize input data sqlinjection, xss, csrf
switch ($action) {
    case '':
        echo json_encode(['succcess' => true, 'message' => 'Welcome to the API']);
        break;

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

    case 'set_dislike':
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

        $result = $articles_controller->set_dislike($article_id, $user_id);
        echo json_encode($result);
        break;

    case 'get_article_dislikes_count':
        $article_id = $_GET['article_id'] ?? null;
        if (!$article_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid article ID']);
            break;
        }

        $result = $articles_controller->get_article_dislikes_count($article_id);
        echo json_encode($result);
        break;

    case 'get_favorites':
        $user_id = $_GET['user_id'] ?? null;
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

    case 'get_article_favorites_count':
        $article_id = $_GET['article_id'] ?? null;
        if (!$article_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid article ID']);
            break;
        }

        $result = $articles_controller->get_article_favorites_count($article_id);
        echo json_encode($result);
        break;

    case 'get_article_comments_count':
        $article_id = $_GET['article_id'] ?? null;
        if (!$article_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid article ID']);
            break;
        }

        $result = $comments_controller->get_article_comments_count($article_id);
        echo json_encode($result);
        break;

    case 'get_all_users':
        $result = $admin_controller->get_all_users();
        echo json_encode($result);
        break;

    case 'write_article':
        $title = $data['title'] ?? null;
        $content = $data['content'] ?? null;
        $author = $data['author'] ?? null;
        $author_id = $data['author_id'] ?? null;

        if (!$title) {
            echo json_encode(['success' => false, 'message' => 'Invalid title']);
            break;
        }

        if (!$content) {
            echo json_encode(['success' => false, 'message' => 'Invalid content']);
            break;
        }

        if (!$author_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid author ID']);
            break;
        }

        $result = $articles_controller->writeArticle($title, $content, $author, $author_id);
        echo json_encode($result);
        break;

    case 'update_article':
        $article_id = $data['article_id'] ?? null;
        $admin_id = $data['admin_id'] ?? null;
        $title = $data['title'] ?? null;
        $content = $data['content'] ?? null;
        $author = $data['author'] ?? null;

        if (!$article_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid article ID']);
            break;
        }

        if (!$admin_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid admin ID']);
            break;
        }

        if (!$title) {
            echo json_encode(['success' => false, 'message' => 'Invalid title']);
            break;
        }

        if (!$content) {
            echo json_encode(['success' => false, 'message' => 'Invalid content']);
            break;
        }

        if (!$author) {
            echo json_encode(['success' => false, 'message' => 'Invalid author']);
            break;
        }

        $result = $admin_controller->update_article($article_id, $admin_id, $title, $content, $author);
        echo json_encode($result);
        break;

    case 'delete_article':
        $article_id = $data['article_id'] ?? null;

        if (!$article_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid article ID']);
            break;
        }

        $result = $admin_controller->delete_article($article_id);
        echo json_encode($result);
        break;

    case 'delete_user':
        $user_id = $data['user_id'] ?? null;
        $admin_id = $data['admin_id'] ?? null;

        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            break;
        }

        if (!$admin_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid admin ID']);
            break;
        }

        $result = $admin_controller->delete_user($user_id, $admin_id);
        echo json_encode($result);
        break;

    case 'get_all_dislikes':
        $result = $admin_controller->count_all_dislikes();
        echo json_encode($result);
        break;

    case 'get_all_favorites':
        $result = $admin_controller->count_all_favorites();
        echo json_encode($result);
        break;

    case 'change_user_type':
        $user_id = $data['user_id'] ?? null;
        $admin_id = $data['admin_id'] ?? null;
        $type = $data['type'] ?? null;

        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            break;
        }

        if (!$admin_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid admin ID']);
            break;
        }

        if (!$type) {
            echo json_encode(['success' => false, 'message' => 'Invalid user type']);
            break;
        }

        $result = $admin_controller->change_user_type($user_id, $admin_id, $type);
        echo json_encode($result);
        break;

    case 'delete_profile':
        $user_id = $data['user_id'] ?? null;
        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            break;
        }

        $result = $controller->delete_profile($user_id);
        echo json_encode($result);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid Action']);
        break;
}
