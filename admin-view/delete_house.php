
<?php
require "../db_connection.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'])) {

        $id = $conn->real_escape_string($data['id']);

        $query = "DELETE FROM houses WHERE id = '$id'";

        if ($conn->query($query) === TRUE) {
            if ($conn->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'House successfully deleted']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No house found with the given ID']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Missing house ID']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
