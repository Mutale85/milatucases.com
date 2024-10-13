<?php 
    include "../../includes/db.php";
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $lawFirmId = $_SESSION['parent_id'];
        $userId = $_SESSION['user_id'];
        $clientId = filter_input(INPUT_POST, 'client_id', FILTER_SANITIZE_NUMBER_INT);
        $caseId = filter_input(INPUT_POST, 'case_id', FILTER_SANITIZE_NUMBER_INT);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
        $hourly_rate = filter_input(INPUT_POST, 'hourly_rate', FILTER_SANITIZE_SPECIAL_CHARS);
        $total_amount = filter_input(INPUT_POST, 'total_amount', FILTER_SANITIZE_SPECIAL_CHARS);
        $start_time = date("Y-m-d H:i:s", strtotime($_POST['start_time']));
        $end_time = date("Y-m-d H:i:s", strtotime($_POST['end_time']));
        $elapsed_time = filter_input(INPUT_POST, 'elapsed_time', FILTER_SANITIZE_SPECIAL_CHARS);
        $created_at = date("Y-m-d H:i:s");

        $result = calculateTimeDifference($start_time, $end_time);
        $elapsed_time = $result['formatted'];

        $sql = $connect->prepare("INSERT INTO task_billing (lawFirmId, userId, clientId, caseId, description, hourly_rate, total_amount, start_time, end_time, elapsed_time, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $sql->execute([$lawFirmId, $userId, $clientId, $caseId, $description, $hourly_rate, $total_amount, $start_time, $end_time, $elapsed_time, $created_at]);


        echo "$total_amount for time $elapsed_time worked posted sucessfully";
    }

    $connect = null;


?>