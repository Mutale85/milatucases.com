<?php
    /*
    include '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $logId = $_POST['logId'];
        $taskDescription = $_POST['taskDescription'];
        $hourlyRate = $_POST['hourlyRate'];

        // Fetch the time spent for the log entry
        $query = $connect->prepare("SELECT elapsed_time FROM task_billing WHERE id = ?");
        $query->execute([$logId]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $timeSpent = $result['elapsed_time'];

        echo strtotime($timeSpent);
        // Calculate the new total charge
        // $totalCharge = ($timeSpent / 60) * $hourlyRate;

        // // Prepare the SQL update query
        // $query = $connect->prepare("UPDATE task_billing SET description = ?, hourly_rate = ? WHERE id = ?");
        // $success = $query->execute([$taskDescription, $hourlyRate, $logId]);

        // // Return JSON response with the new total charge
        // echo json_encode(['success' => $success, 'timeSpent' => $timeSpent]);
    }
    */

    include '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $logId = $_POST['logId'];
        $taskDescription = $_POST['taskDescription'];
        $hourlyRate = $_POST['hourlyRate'];

        // Fetch the time spent for the log entry
        $query = $connect->prepare("SELECT elapsed_time FROM task_billing WHERE id = ?");
        $query->execute([$logId]);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $timeSpent = $result['elapsed_time'];

            // Convert elapsed_time to seconds if it's stored as HH:MM:SS
            if (strpos($timeSpent, ':') !== false) {
                $timeParts = explode(':', $timeSpent);
                $timeSpentSeconds = $timeParts[0] * 3600 + $timeParts[1] * 60 + $timeParts[2];
            } else {
                $timeSpentSeconds = intval($timeSpent);
            }

            // Calculate the new total charge
            $totalCharge = ($timeSpentSeconds / 3600) * $hourlyRate;

            // Prepare the SQL update query
            $query = $connect->prepare("UPDATE task_billing SET description = ?, hourly_rate = ?, total_amount = ? WHERE id = ?");
            $success = $query->execute([$taskDescription, $hourlyRate, $totalCharge, $logId]);

            // Return JSON response with the new total charge and time spent
            echo json_encode([
                'success' => $success,
                'timeSpent' => $timeSpentSeconds,
                'totalCharge' => $totalCharge,
                'formattedTime' => sprintf('%02d:%02d:%02d', ($timeSpentSeconds/3600),($timeSpentSeconds/60%60), $timeSpentSeconds%60)
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Log entry not found']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    }
?>
