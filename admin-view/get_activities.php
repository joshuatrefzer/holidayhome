<?php
require "../db_connection.php";

header('Content-Type: application/json');

$query = "SELECT id, activity_name FROM activities";
$result = $conn->query($query);

$activities = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
}

echo json_encode($activities);

