<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include 'AuthController.php';

$controller = new AuthController();
$data = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? '';

switch($action) {
    case 'register':
        $result = $controller->register(
            $data['username'] ?? '', 
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

    case 'validate-token':
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';
        $result = $controller->validateToken($token);
        echo json_encode($result);
        break;

    case 'logout':
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';
        $result = $controller->logout($token);
        echo json_encode($result);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid Action']);
        break;
}
