	<?php
		include("../../includes/db.php");
		$lawFirmId = $_SESSION['parent_id'];
		$userId = $_SESSION['user_id'];

		$color_classes = [
		    'red' => 'bg-danger',      // Example: red maps to Bootstrap danger
		    'blue' => 'bg-primary',    // Example: blue maps to Bootstrap primary
		    'green' => 'bg-success',   // Example: green maps to Bootstrap success
		    'yellow' => 'bg-warning',  // Example: yellow maps to Bootstrap warning
		    'gray' => 'bg-secondary',  // Example: gray maps to Bootstrap secondary
		    // Add more mappings as needed
		];

		// Calculate the start and end dates for the current week
		$today = date("Y-m-d");
		$start_of_week = date("Y-m-d", strtotime('monday this week'));
		$end_of_week = date("Y-m-d", strtotime('sunday next week'));

		// Fetch events for the week
		$query = $connect->prepare("
		    SELECT * FROM events_personal 
		    WHERE created_by = ? 
		      AND start_date BETWEEN ? AND ? 
		      AND lawFirmId = ?
		");
		$query->execute([$userId, $start_of_week, $end_of_week, $lawFirmId]);
		$events = $query->fetchAll();
	?>

	<div class="container">
	    <h5>Events for the Week: <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#emailCalendarModal">Email Calendar</button></h5>

	    <?php
	    $week_days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
	    $events_by_day = [];

	    foreach ($events as $event) {
	        $day_of_week = date('l', strtotime($event['start_date']));
	        $events_by_day[$day_of_week][] = $event;
	    }

	    foreach ($week_days as $day) {
	        echo "<h6>$day</h6>";
	        if (isset($events_by_day[$day])) {
	            foreach ($events_by_day[$day] as $event) {
	                // Determine the Bootstrap class based on the color field
	                $color_class = isset($color_classes[$event['color']]) ? $color_classes[$event['color']] : 'bg-light';
	                $start_time = date("H:i A", strtotime($event['start_time']));
	                $end_time = date("H:i A", strtotime($event['end_time']));
	                ?>
	                <div class="event <?php echo $color_class; ?> text-white p-2 mb-2">
					    <div class="mb-0">
					        <strong><?php echo htmlspecialchars(html_entity_decode($event['title'])); ?></strong>
					        <br>
					        <small><?php echo htmlspecialchars(html_entity_decode($event['description'])); ?></small><br>
					        <em class="text-dark"><i class="bi bi-clock-history"></i> <?php echo htmlspecialchars($start_time); ?> - <?php echo htmlspecialchars($end_time); ?></em>
					    </div>
					    <!-- Add edit and delete buttons as needed -->
					    <!-- <button type="button" class="btn btn-primary btn-sm" id="editEventBtn<?php echo $event['event_id']; ?>">Edit</button>
					    <button type="button" class="btn btn-danger btn-sm" id="deleteEventBtn<?php echo $event['event_id']; ?>">Remove</button> -->
					</div>
	                <?php
	            }
	        } else {
	            echo '<p class="alert alert-danger">No events for this day</p>';
	        }
	    }
	?>
	</div>