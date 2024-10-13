<?php
    include("../../includes/db.php");
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $eventId = $_POST['eventId'];

        // Fetch event details
        $query = "SELECT * FROM events_personal WHERE event_id = :eventId";
        $stmt = $connect->prepare($query);
        $stmt->bindParam(':eventId', $eventId);
        $stmt->execute();
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        $response = [
            'event' => $event,
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
    }
?>