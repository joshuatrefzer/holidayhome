<?php 

require '../db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $searchTerms = isset($data['search']) ? explode(',', $data['search']) : [];
    
    $conditions = [];
    $params = [];

    foreach ($searchTerms as $term) {
        $term = trim($term);
        if (!empty($term)) {
            $conditions[] = "(h.name LIKE ? OR 
                              f.facility_name LIKE ? OR 
                              a.activity_name LIKE ? OR 
                              t.tag_name LIKE ? OR 
                              h.postal_code LIKE ? OR 
                              h.country LIKE ? OR 
                              h.street LIKE ?)";

            $params[] = "%$term%"; 
            $params[] = "%$term%"; 
            $params[] = "%$term%"; 
            $params[] = "%$term%"; 
            $params[] = "%$term%"; 
            $params[] = "%$term%";
            $params[] = "%$term%"; 
        }
    }

    $sql = "
        SELECT h.*, hi.image_url AS main_img 
        FROM houses h
        LEFT JOIN house_facilities hf ON h.id = hf.house_id
        LEFT JOIN facilities f ON hf.facility_id = f.id
        LEFT JOIN house_activities ha ON h.id = ha.house_id
        LEFT JOIN activities a ON ha.activity_id = a.id
        LEFT JOIN house_tags ht ON h.id = ht.house_id
        LEFT JOIN tags t ON ht.tag_id = t.id
        LEFT JOIN house_images hi ON h.id = hi.house_id AND hi.is_main_image = TRUE
    ";

    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

   
    $sql .= " GROUP BY h.id LIMIT 10";

    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $houses = [];
    while ($row = $result->fetch_assoc()) {
        $houses[] = $row;
    }

    echo json_encode(['success' => true, 'houses' => $houses]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
