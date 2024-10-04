<?php
require "../db_connection.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['name'], $data['street'], $data['house_number'], $data['postal_code'], $data['country'], $data['price_per_day'], $data['landlord'])) {

        $name = $conn->real_escape_string($data['name']);
        $street = $conn->real_escape_string($data['street']);
        $house_number = $conn->real_escape_string($data['house_number']);
        $postal_code = $conn->real_escape_string($data['postal_code']);
        $country = $conn->real_escape_string($data['country']);
        $price_per_day = $conn->real_escape_string($data['price_per_day']);
        $landlord = $conn->real_escape_string($data['landlord']);

        $query = "INSERT INTO houses (name, street, house_number, postal_code, country, price_per_day, landlord) 
                  VALUES ('$name', '$street', '$house_number', '$postal_code', '$country', '$price_per_day', '$landlord')";

        if ($conn->query($query) === TRUE) {
            $last_id = $conn->insert_id;
            echo json_encode([
                'success' => true, 
                'message' => 'House data successfully inserted',
                'id' => $last_id
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();

