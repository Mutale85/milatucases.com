<?php
require '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $caseId = filter_input(INPUT_POST, 'caseId', FILTER_SANITIZE_SPECIAL_CHARS);
    $clientId = filter_input(INPUT_POST, 'clientId', FILTER_SANITIZE_SPECIAL_CHARS);
    $lawFirmId = filter_input(INPUT_POST, 'lawFirmId', FILTER_SANITIZE_SPECIAL_CHARS);
    $caseStatus = filter_input(INPUT_POST, 'caseStatus', FILTER_SANITIZE_SPECIAL_CHARS);
    $dateAdded = date('Y-m-d H:i:s');

    try {
        $sql = "INSERT INTO case_status (caseId, clientId, lawFirmId, case_status, date_added) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($sql);
        if ($stmt->execute([$caseId, $clientId, $lawFirmId, $caseStatus, $dateAdded])) {
            $response = ['success' => true];
        } else {
            $response = ['success' => false, 'message' => 'Failed to update case status'];
        }
    } catch (PDOException $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
