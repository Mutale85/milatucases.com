<?php
include("../../includes/db.php");

$stmt = $connect->prepare("SELECT event_id, title, description, start_date AS start, end_date AS end, color, start_time, end_time FROM events WHERE lawFirmId = ? ");
$stmt->execute([$_SESSION['parent_id']]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$events = [];

foreach ($results as $row) {
    $startDateTime = $row['start'] . 'T' . $row['start_time'];
    $endDateTime = $row['end'] . 'T' . $row['end_time'];

    $events[] = [
        'id' => $row['event_id'],
        'title' => $row['title'],
        'description' => $row['description'],
        'start' => $startDateTime,
        'end' => $endDateTime,
        'color' => $row['color'],
        'className' => getBootstrapClassName($row['color']),
    ];
}

echo json_encode($events);
?>
