<?php
require "db_connection.php";


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] === 'GET') {

   
    if (isset($_GET['house_id'])) {
        $house_id = $conn->real_escape_string($_GET['house_id']);

       
        $query = "SELECT * FROM house_images WHERE house_id = '$house_id' AND image_type = 'main' LIMIT 1";
        $result = $conn->query($query);

        
        if ($result->num_rows > 0) {
         
            $image = $result->fetch_assoc();

            
            echo json_encode(['success' => true, 'image' => $image]);
        } else {
            
            echo json_encode(['success' => false, 'message' => 'Kein Hauptbild für dieses Haus gefunden.']);
        }
    } else {
       
        echo json_encode(['success' => false, 'message' => 'House ID fehlt.']);
    }

} else {
    
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfragemethode.']);
}

$conn->close();
