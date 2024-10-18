<?php
    include '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $logId = $_POST['logId'];

        // Prepare the SQL delete query
        $query = $connect->prepare("DELETE FROM task_billing WHERE id = ?");
        $success = $query->execute([$logId]);

        // Return JSON response
        echo json_encode(['success' => $success]);
    }
?>
