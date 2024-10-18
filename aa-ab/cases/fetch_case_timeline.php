<?php
require '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $caseId = filter_input(INPUT_POST, 'caseId', FILTER_SANITIZE_SPECIAL_CHARS);
    $lawFirmId = $_SESSION['parent_id']; // Assuming lawFirmId is stored in session

    $timelineData = fetchCaseStatusTimeline($caseId, $lawFirmId);

    header('Content-Type: application/json');
    echo json_encode($timelineData);
}

function fetchCaseStatusTimeline($caseId, $lawFirmId) {
    global $connect;

    try {
        // Fetch from the case_status table
        $query = $connect->prepare("SELECT case_status, date_added FROM case_status WHERE caseId = ? AND lawFirmId = ? ORDER BY date_added ASC");
        $query->execute([$caseId, $lawFirmId]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        // If no results from case_status, check in the cases table
        if (empty($result)) {
            $query = $connect->prepare("SELECT caseStatus AS case_status, created_at AS date_added FROM cases WHERE id = ? AND lawFirmId = ?");
            $query->execute([$caseId, $lawFirmId]);
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        }

        return $result;

    } catch (PDOException $e) {
        return 'Error: ' . $e->getMessage();
    }
}


?>
