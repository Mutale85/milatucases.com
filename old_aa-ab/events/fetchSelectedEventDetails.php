<?php
    include("../../includes/db.php");
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $eventId = $_POST['eventId'];

        // Fetch event details
        $query = "SELECT * FROM events WHERE event_id = :eventId";
        $stmt = $connect->prepare($query);
        $stmt->bindParam(':eventId', $eventId);
        $stmt->execute();
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch event attendees
        $query = "SELECT attendeeId FROM event_attendees WHERE event_id = :eventId";
        $stmt = $connect->prepare($query);
        $stmt->bindParam(':eventId', $eventId);
        $stmt->execute();
        $attendees = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $response = [
            'event' => $event,
            'attendees' => $attendees
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
    }
?>