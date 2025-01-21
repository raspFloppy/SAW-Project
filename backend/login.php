<?php
require_once 'database.php';
require_once 'AuthController.php';
require_once 'session_config.php';

init_session();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'], $_POST['pass'])) {
        $authController = new AuthController();
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['pass']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            exit;
        }

        $result = $authController->login($email, $password);
        echo json_encode($result);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters\n']);
        exit;
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}
