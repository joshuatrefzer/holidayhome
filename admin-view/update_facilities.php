<?php
require "../db_connection.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['house_id'], $data['facilities']) && is_array($data['facilities'])) {

        $house_id = $conn->real_escape_string($data['house_id']);
        $facilities = $data['facilities']; 

        $houseCheckQuery = "SELECT id FROM houses WHERE id = '$house_id'";
        $houseCheckResult = $conn->query($houseCheckQuery);

        if ($houseCheckResult->num_rows == 0) {
            echo json_encode(['success' => false, 'message' => 'House not found']);
            exit;
        }

        $conn->begin_transaction();

        try {
            $deleteQuery = "DELETE FROM house_facilities WHERE house_id = '$house_id'";
            if (!$conn->query($deleteQuery)) {
                throw new Exception('Error deleting existing facilities: ' . $conn->error);
            }

            foreach ($facilities as $facility_id) {
                $facility_id = $conn->real_escape_string($facility_id);
                
                $facilityCheckQuery = "SELECT id FROM facilities WHERE id = '$facility_id'";
                $facilityCheckResult = $conn->query($facilityCheckQuery);
                if ($facilityCheckResult->num_rows == 0) {
                    throw new Exception('Facility not found: ' . $facility_id);
                }

                $insertQuery = "INSERT INTO house_facilities (house_id, facility_id) 
                                VALUES ('$house_id', '$facility_id')";
                if (!$conn->query($insertQuery)) {
                    throw new Exception('Error inserting facility: ' . $conn->error);
                }
            }

            $conn->commit();

            echo json_encode([
                'success' => true, 
                'message' => 'Facilities successfully updated'
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
