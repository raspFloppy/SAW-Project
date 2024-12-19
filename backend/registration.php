<?php
require_once 'database.php';
require_once 'AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'], $_POST['firstname'], $_POST['lastname'], $_POST['pass'], $_POST['confirm'], $_POST['submit'])) {
        $authController = new AuthController();

        $email = trim($_POST['email']);
        $firstname = trim($_POST['firstname']);
        $lastname = trim($_POST['lastname']);
        $password = trim($_POST['pass']);

        if (strlen($password) < 8) {
            echo json_encode(['success' => false, 'message' => 'Password too short minimum 8 characters']);
            exit;
        }

        if ($_POST['pass'] !== $_POST['confirm']) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
            exit;
        }

        $result = $authController->register($firstname, $lastname, $email, $password);

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
