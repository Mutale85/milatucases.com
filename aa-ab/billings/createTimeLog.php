<?php

    include '../../includes/db.php';

    $lawFirmId = $_SESSION['parent_id'];

    try {
        // Fetch currencies from countries table
        $currencyStmt = $connect->query("SELECT currency FROM countries WHERE currency IS NOT NULL AND currency != '' GROUP BY currency ORDER BY currency");
        $currencies = $currencyStmt->fetchAll(PDO::FETCH_COLUMN);

        // Common input sanitization
        $timerId = filter_input(INPUT_POST, 'timerId', FILTER_SANITIZE_NUMBER_INT);
        $caseId = filter_input(INPUT_POST, 'caseId', FILTER_SANITIZE_SPECIAL_CHARS);
        $userId = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_SPECIAL_CHARS);
        $dateCreated = filter_input(INPUT_POST, 'dateCreated', FILTER_SANITIZE_SPECIAL_CHARS);
        $timeCreated = filter_input(INPUT_POST, 'timeCreated', FILTER_SANITIZE_SPECIAL_CHARS);
        $hours = filter_input(INPUT_POST, 'hours', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $minutes = filter_input(INPUT_POST, 'minutes', FILTER_SANITIZE_NUMBER_INT);
        // $currency = filter_input(INPUT_POST, 'currency', FILTER_SANITIZE_SPECIAL_CHARS);
        // $hourlyRate = filter_input(INPUT_POST, 'hourlyRate', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        // $cost = filter_input(INPUT_POST, 'cost', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $currency = filter_input(INPUT_POST, 'currency', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'ZMW';
        $hourlyRate = filter_input(INPUT_POST, 'hourlyRate', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?: 0;
        $cost = filter_input(INPUT_POST, 'cost', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?: 0;
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
        $billableStatus = filter_input(INPUT_POST, 'billableStatus', FILTER_SANITIZE_SPECIAL_CHARS);

        $timeCreated = date("H:i", strtotime($timeCreated));
        $clientId = ($caseId != 'Non-Matter-Related') ? fetchClientIdCaseId($caseId) : null; // Changed to null
        $description = encrypt($description);

        if (empty($timerId)) {
            // Insert new time entry
            $stmt = $connect->prepare("INSERT INTO time_entries 
                (lawFirmId, caseId, clientId, userId, dateCreated, timeCreated, hours, minutes, currency, hourlyRate, cost, description, billableStatus) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$lawFirmId, $caseId, $clientId, $userId, $dateCreated, $timeCreated, $hours, $minutes, $currency, $hourlyRate, $cost, $description, $billableStatus]);
            echo "New time entry created successfully";
        } else {
            // Update existing time entry
            $stmt = $connect->prepare("UPDATE time_entries SET 
                caseId = ?, clientId = ?, dateCreated = ?, timeCreated = ?, hours = ?, minutes = ?, 
                currency = ?, hourlyRate = ?, cost = ?, description = ?, billableStatus = ? 
                WHERE id = ? AND lawFirmId = ? AND userId = ?");
            $stmt->execute([$caseId, $clientId, $dateCreated, $timeCreated, $hours, $minutes, $currency, $hourlyRate, $cost, $description, $billableStatus, $timerId, $lawFirmId, $userId]);
            echo "Time entry updated successfully";
        }
    } catch(PDOException $e) {
        // More detailed error logging
        echo("Database Error: " . $e->getMessage());
        //echo "An error occurred while processing your request. Please try again or contact support.";
    }

    $connect = null;
?>