<?php 
	if(isset($_POST['upcomingEvents'])){
		include '../../includes/db.php';
		$churchId = $_SESSION['parent_id'];
		$events = countUpcomingEvents($churchId);
		if($events === 0){
			echo "<a href='events/events'> {$events} Add Events</a>";
		}else{
			echo "<a href='events/events'> {$events} View Events</a>";
		}
	}
?>