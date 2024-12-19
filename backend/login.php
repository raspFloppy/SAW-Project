<?php
require_once 'database.php';
require_once 'AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'], $_POST['pass'])) {
        $authController = new AuthController();
        $email = trim($_POST['email']);
        $password = trim($_POST['pass']);

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
