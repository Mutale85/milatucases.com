<?php
    include '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $lawFirmId = $_POST['lawFirmId'];
        $stmt = $connect->prepare("SELECT * FROM `subscriptions` WHERE lawFirmId = ?");
        $stmt->execute([$lawFirmId]);
        $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calculate the remaining days, hours, minutes, and seconds
        $endDate = new DateTime($subscription['end_date']);
        $currentDate = new DateTime();
        $interval = $currentDate->diff($endDate);
        $daysRemaining = $interval->days;
        $hoursRemaining = $interval->h;
        $minutesRemaining = $interval->i;
        $secondsRemaining = $interval->s;

        // If the days remaining is 0, create a session and redirect to the subscription page
        if ($daysRemaining <= 0 && $hoursRemaining <= 0 && $minutesRemaining <= 0 && $secondsRemaining <= 0) {
            session_start();
            $_SESSION['subscription_expired'] = true;
            header("Location: billings/subscription");
            exit;
        }

        // Return the subscription data as a JSON response
        echo json_encode(array(
            'daysRemaining' => $daysRemaining,
            'hoursRemaining' => $hoursRemaining,
            'minutesRemaining' => $minutesRemaining,
            'secondsRemaining' => $secondsRemaining
        ));
    }
?>