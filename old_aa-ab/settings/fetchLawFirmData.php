<?php
include '../../includes/db.php'; // Adjust the path to your database connection script

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lawFirmId = filter_input(INPUT_POST, 'lawFirmId', FILTER_SANITIZE_SPECIAL_CHARS);

    if ($lawFirmId) {
        try {
            $stmt = $connect->prepare("SELECT company_name, address, postal_code, telephone, email, logo FROM company_info WHERE lawFirmId = ?");
            $stmt->execute([$lawFirmId]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No data found.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid law firm ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
