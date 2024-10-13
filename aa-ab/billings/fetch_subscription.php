<?php
    require '../../includes/db.php'; // Your database connection setup

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $lawFirmId = $_POST['lawFirmId'];

        $subscriptionData = getSubscriptionData($lawFirmId);

        if ($subscriptionData) {
            $remainingTime = calculateRemainingTime($subscriptionData[0]['end_date']);
            lockProfileIfExpired($remainingTime);

            $response = [
                'remaining_time' => $remainingTime,
                'profile_locked' => isset($_SESSION['profile_locked']) ? $_SESSION['profile_locked'] : false
            ];

            echo json_encode($response);
        } else {
            echo json_encode(['error' => 'No subscription data found.']);
        }
    }
