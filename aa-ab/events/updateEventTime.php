<?php
	// Include your database connection file
	include("../../includes/db.php");

	// Check if the request is a POST request
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	    // Get the event ID, new start time, and new end time from the POST data
	    $eventId = isset($_POST['eventId']) ? $_POST['eventId'] : null;
	    $newStart = isset($_POST['start']) ? $_POST['start'] : null;
	    $newEnd = isset($_POST['end']) ? $_POST['end'] : null;

	    // Check if the required data is provided
	    if ($eventId && $newStart) {
	        // Extract the date and time from the new start time
	        $startDate = date('Y-m-d', strtotime($newStart));
	        $startTime = date('H:i:s', strtotime($newStart));

	        // If the new end time is provided, extract the date and time from it
	        if ($newEnd) {
	            $endDate = date('Y-m-d', strtotime($newEnd));
	            $endTime = date('H:i:s', strtotime($newEnd));
	        } else {
	            // If the end time is not provided, use the start date and time
	            $endDate = $startDate;
	            $endTime = $startTime;
	        }

	        // Prepare the SQL statement to update the event times
	        $stmt = $connect->prepare("UPDATE events SET start_date = ?, start_time = ?, end_date = ?, end_time = ? WHERE event_id = ?");
	        // Execute the statement with the new times and the event ID
	        $stmt->execute([$startDate, $startTime, $endDate, $endTime, $eventId]);

	        // Check if the update was successful
	        if ($stmt->rowCount() > 0) {
	            echo json_encode(['success' => true, 'message' => 'Event time updated successfully']);
	        } else {
	            echo json_encode(['success' => false, 'message' => 'Failed to update event time']);
	        }
	    } else {
	        echo json_encode(['success' => false, 'message' => 'Missing required data']);
	    }
	} else {
	    // If the request is not a POST request, return an error message
	    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
	}
?>
