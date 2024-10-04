<?php
require 'db_connection.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data === null || !isset($data['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input: User ID is required.']);
        exit;
    }

    $userId = (int)$data['id'];

    
    $query = "SELECT username FROM users WHERE id = $userId";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $username = $row['username'];

        if ($username === 'admin' || $username === 'normaluser') {
            echo json_encode(['success' => false, 'message' => 'Cannot delete this user.']);
        } else {
           
            $deleteQuery = "DELETE FROM users WHERE id = $userId";
            if ($conn->query($deleteQuery) === TRUE) {
                echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error deleting user: ' . $conn->error]);
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
