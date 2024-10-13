<?php
require '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $caseId = filter_input(INPUT_POST, 'caseId', FILTER_SANITIZE_SPECIAL_CHARS);
    $lawFirmId = $_SESSION['parent_id']; // Assuming lawFirmId is stored in session

    // First, check in the case_status table
    $query = $connect->prepare("SELECT case_status FROM case_status WHERE caseId = ? AND lawFirmId = ?");
    $query->execute([$caseId, $lawFirmId]);
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $response = [
            'success' => true,
            'caseStatus' => $result['case_status']
        ];
    } else {
        // If not found, check in the cases table
        $query = $connect->prepare("SELECT caseStatus FROM cases WHERE id = ? AND lawFirmId = ?");
        $query->execute([$caseId, $lawFirmId]);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $response = [
                'success' => true,
                'caseStatus' => $result['caseStatus']
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Case status not found'
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
