<?php
	include '../../includes/db.php'; // Include your database connection file



	$sql = "SELECT `id`, `title`, `start`, `start_time`, `end`, `end_time`, `reminder_time`, `color` FROM `calendar`";
	$stmt = $connect->prepare($sql);
	$stmt->execute();
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$events = [];

	foreach ($results as $row) {
	    // Combine the date and time for start and end
	    $startDateTime = $row['start'] . 'T' . $row['start_time'];
	    $endDateTime = $row['end'] . 'T' . $row['end_time'];

	    $events[] = [
	        'id' => $row['id'],
	        'title' => $row['title'],
	        'start' => $startDateTime,
	        'end' => $endDateTime,
	        'reminder_time' => $row['reminder_time'],
	        'color' => $row['color'],
	        // Add more fields as needed
	    ];
	}

	echo json_encode($events);

?>
