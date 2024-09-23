<?php
require 'db_connection.php';

// Fehleranzeige aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Debugging: Daten prüfen
    if ($data === null) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
        exit;
    }

    if (isset($data['username']) && !empty($data['username'])) {
        $username = $conn->real_escape_string($data['username']);
        $password = $conn->real_escape_string($data['password']);
        
        // Username und Passwort überprüfen
        if (!empty($password)) {
            // Passwort muss übereinstimmen
            $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        } else {
            // Nur Username überprüfen (falls kein Passwort gesetzt wurde)
            $sql = "SELECT * FROM users WHERE username = '$username'";
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Erfolgreicher Login
            echo json_encode(['success' => true, 'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ]]);
        } else {
            // Ungültiger Username oder Passwort
            echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Username is required.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
