<?php
require_once 'AuthController.php';
require_once 'session_config.php';

init_session();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $authController = new AuthController();

    if ($_SESSION && $_SESSION['id']) {
        $result = $authController->show_profile();
        echo json_encode($result);
        exit;
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'User is not logged']);
        exit;
    }
    exit;
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}
