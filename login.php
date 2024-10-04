<?php
require 'db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($data === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
        exit;
    }

    if (isset($data['username']) && !empty($data['username'])) {
        $username = $conn->real_escape_string($data['username']);
        $password = $conn->real_escape_string($data['password']);
        
        if (!empty($password)) {
            $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        } else {
            $sql = "SELECT * FROM users WHERE username = '$username'";
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            echo json_encode(['success' => true, 'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ]]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Username is required.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
