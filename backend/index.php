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

$controller = new AuthController();
$articles_controller = new ArticleController();
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
        $result = $articles_controller->get_articles();
        echo json_encode($result);
        break;
    case 'get_article':
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid article ID']);
            break;
        }
        $result = $articles_controller->get_article($id);
        echo json_encode($result);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid Action']);
        break;
}
