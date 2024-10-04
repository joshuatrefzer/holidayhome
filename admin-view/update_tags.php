<?php
require "../db_connection.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['house_id'], $data['tag_id']) && is_array($data['tag_id'])) {

        $house_id = $conn->real_escape_string($data['house_id']);
        $tag_ids = $data['tag_id']; 

        $houseCheckQuery = "SELECT id FROM houses WHERE id = '$house_id'";
        $houseCheckResult = $conn->query($houseCheckQuery);

        if ($houseCheckResult->num_rows == 0) {
            echo json_encode(['success' => false, 'message' => 'House not found']);
            exit;
        }

        $conn->begin_transaction();

        try {
            $deleteQuery = "DELETE FROM house_tags WHERE house_id = '$house_id'";
            if (!$conn->query($deleteQuery)) {
                throw new Exception('Error deleting existing tags: ' . $conn->error);
            }

            foreach ($tag_ids as $tag_id) {
                $tag_id = $conn->real_escape_string($tag_id);

                $tagCheckQuery = "SELECT id FROM tags WHERE id = '$tag_id'";
                $tagCheckResult = $conn->query($tagCheckQuery);
                if ($tagCheckResult->num_rows == 0) {
                    throw new Exception('Tag not found: ' . $tag_id);
                }

                $insertQuery = "INSERT INTO house_tags (house_id, tag_id) 
                                VALUES ('$house_id', '$tag_id')";
                if (!$conn->query($insertQuery)) {
                    throw new Exception('Error inserting tag: ' . $conn->error);
                }
            }

            $conn->commit();

            echo json_encode([
                'success' => true, 
                'message' => 'Tags successfully updated'
            ]);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required fields or invalid data format']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();

