<?php
    include("../../includes/db.php");

    // Get the event ID from the POST data
    $eventId = filter_input(INPUT_POST, 'eventId', FILTER_SANITIZE_NUMBER_INT);

    if ($eventId) {
        // Prepare and execute a query to fetch event attendees
        $stmt = $connect->prepare("SELECT * FROM event_attendees WHERE event_id = ?");
        $stmt->execute([$eventId]);
        
        // Fetch the results as an associative array
        $attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the attendee data as JSON
        echo json_encode($attendees);
    } else {
        // If no event ID is provided, return an empty array
        echo json_encode([]);
    }
?>
