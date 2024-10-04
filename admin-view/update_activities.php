<?php
require "../db_connection.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['house_id'], $data['activities']) && is_array($data['activities'])) {

        $house_id = $conn->real_escape_string($data['house_id']);
        $activities = $data['activities']; 

        $houseCheckQuery = "SELECT id FROM houses WHERE id = '$house_id'";
        $houseCheckResult = $conn->query($houseCheckQuery);

        if ($houseCheckResult->num_rows == 0) {
            echo json_encode(['success' => false, 'message' => 'House not found']);
            exit;
        }

        $conn->begin_transaction();

        try {
            $deleteQuery = "DELETE FROM house_activities WHERE house_id = '$house_id'";
            if (!$conn->query($deleteQuery)) {
                throw new Exception('Error deleting existing activities: ' . $conn->error);
            }

            foreach ($activities as $activity_id) {
                $activity_id = $conn->real_escape_string($activity_id);
                
                $activityCheckQuery = "SELECT id FROM activities WHERE id = '$activity_id'";
                $activityCheckResult = $conn->query($activityCheckQuery);
                if ($activityCheckResult->num_rows == 0) {
                    throw new Exception('Activity not found: ' . $activity_id);
                }

                $insertQuery = "INSERT INTO house_activities (house_id, activity_id) 
                                VALUES ('$house_id', '$activity_id')";
                if (!$conn->query($insertQuery)) {
                    throw new Exception('Error inserting activity: ' . $conn->error);
                }
            }

            $conn->commit();

            echo json_encode([
                'success' => true, 
                'message' => 'Activities successfully updated'
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
?>
