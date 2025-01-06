<?php

require_once 'AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $authController = new AuthController();

    $result = $authController->show_profile();
    echo json_encode($result);
    exit;
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}
