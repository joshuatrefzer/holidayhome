<?php
require "../db_connection.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id']) && (
        isset($data['name']) || 
        isset($data['street']) || 
        isset($data['house_number']) || 
        isset($data['postal_code']) || 
        isset($data['country']) || 
        isset($data['price_per_day']) || 
        isset($data['landlord'])
    )) {

        $house_id = $conn->real_escape_string($data['id']);
        $update_fields = [];

        if (isset($data['name'])) {
            $name = $conn->real_escape_string($data['name']);
            $update_fields[] = "name = '$name'";
        }

        if (isset($data['street'])) {
            $street = $conn->real_escape_string($data['street']);
            $update_fields[] = "street = '$street'";
        }

        if (isset($data['house_number'])) {
            $house_number = $conn->real_escape_string($data['house_number']);
            $update_fields[] = "house_number = '$house_number'";
        }

        if (isset($data['postal_code'])) {
            $postal_code = $conn->real_escape_string($data['postal_code']);
            $update_fields[] = "postal_code = '$postal_code'";
        }

        if (isset($data['country'])) {
            $country = $conn->real_escape_string($data['country']);
            $update_fields[] = "country = '$country'";
        }

        if (isset($data['price_per_day'])) {
            $price_per_day = $conn->real_escape_string($data['price_per_day']);
            $update_fields[] = "price_per_day = '$price_per_day'";
        }

        if (isset($data['landlord'])) {
            $landlord = $conn->real_escape_string($data['landlord']);
            $update_fields[] = "landlord = '$landlord'";
        }

        if (count($update_fields) > 0) {
            $query = "UPDATE houses SET " . implode(', ', $update_fields) . " WHERE id = '$house_id'";

            if ($conn->query($query) === TRUE) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'House data successfully updated'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
