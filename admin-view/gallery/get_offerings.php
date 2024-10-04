<?php
require "../../db_connection.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($_GET['landlord'])) {
        $landlord = $conn->real_escape_string($_GET['landlord']);


        $query = "SELECT * FROM houses WHERE landlord = '$landlord'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $houses = [];
            while($row = $result->fetch_assoc()) {
                $houses[] = $row;
            }

            echo json_encode(['success' => true, 'houses' => $houses]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Es gibt noch kein Haus für diesen Vermieter.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Landlord ID fehlt.']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfragemethode.']);
}

$conn->close();
