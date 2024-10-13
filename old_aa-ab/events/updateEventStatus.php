<?php 

	include "../../includes/db.php";

	$eventId = $_POST['eventId'];
    $isChecked = $_POST['isChecked'] ? 1 : 0;

    // Update the event status in the database
    $sql = $connect->prepare("UPDATE events SET status = ? WHERE event_id = ?");
    $sql->execute([$isChecked, $eventId]);
    echo "Event status updated successfully";
?>