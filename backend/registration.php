<?php
require_once 'database.php';
require_once 'AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'], $_POST['firstname'], $_POST['lastname'], $_POST['pass'], $_POST['confirm'], $_POST['submit'])) {
        $authController = new AuthController();

        $email = $_POST['email'];
        $username = $_POST['firstname'] . ' ' . $_POST['lastname'];
        $password = $_POST['pass'];

        if ($_POST['pass'] !== $_POST['confirm']) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
            exit;
        }

        $result = $authController->register($username, $email, $password);

        echo json_encode($result);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}
