<?php
	include '../../includes/db.php';
	include '../../includes/conf.php';

	if(isset($_POST['message']) && isset($_POST['attendees']) && is_array($_POST['attendees']) && count($_POST['attendees']) > 0) {
		$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);
		$subject = 'Event Reminder';
		foreach ($_POST['attendees'] as $key => $value) {
			$attendee = $_POST['attendees'][$key];
			$names = $_POST['names'][$key];
			$phone = $attendee;
			echo sendSMS(API, SENDER, $phone, $message);
			
		}
	} else {
		echo "No attendees selected to send the message.";
	}
?>
