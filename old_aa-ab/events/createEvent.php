<?php 
	error_reporting(E_ALL);
    ini_set('display_errors', 1);
	include("../../includes/db.php");
	include("../../includes/conf.php");
	require '../../vendor/autoload.php'; // Make sure to include the autoload file for PHPMailer and Twilio

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	if ($_SERVER['REQUEST_METHOD'] === "POST") {
	    // Event details
	    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
	    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
	    $start_date = filter_input(INPUT_POST, 'start_date', FILTER_SANITIZE_SPECIAL_CHARS);
	    $end_date = filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_SPECIAL_CHARS);
	    $color = filter_input(INPUT_POST, 'color', FILTER_SANITIZE_SPECIAL_CHARS);
	    $event_id = filter_input(INPUT_POST, 'eventId', FILTER_SANITIZE_SPECIAL_CHARS);
	    $start_time = filter_input(INPUT_POST, 'start_time', FILTER_SANITIZE_SPECIAL_CHARS);
	    $end_time = filter_input(INPUT_POST, 'end_time', FILTER_SANITIZE_SPECIAL_CHARS);
	    $is_case_related = filter_input(INPUT_POST, 'is_case_related', FILTER_SANITIZE_SPECIAL_CHARS);
	    $caseId = filter_input(INPUT_POST, 'case_id', FILTER_SANITIZE_NUMBER_INT);
	    $lawFirmId = $_SESSION['parent_id'];
	    $userId = $_SESSION['user_id'];

	    if($title == ""){
	    	$matterNo = fetchCaseNumber($caseId, $lawFirmId);
	    	$title = fetchCaseTitleById($caseId);
	    	// $title = "Matter: $matterNo";
	    }
	    // echo $title;
	    

	    try {
	        // Start transaction
	        $connect->beginTransaction();

	        if (!empty($event_id)) {
	            $stmt = $connect->prepare("UPDATE events SET title = ?, description = ?, start_date = ?, start_time = ?, end_date = ?, end_time = ?, color = ?, is_case_related = ?, case_id = ? WHERE event_id = ?");
	            $stmt->execute([$title, $description, $start_date, $start_time, $end_date, $end_time, $color, $is_case_related, $caseId, $event_id]);
	            echo "Event updated successfully!";

	            $stmt_delete = $connect->prepare("DELETE FROM event_attendees WHERE event_id = ?");
	            $stmt_delete->execute([$event_id]);
	            // update event
	            foreach ($_POST['attendees'] as $key => $attendeeId) {
	            
		            $person_name = fetchLawFirmUserName($attendeeId, $lawFirmId);
		            $person_phone = fetchLawFirmUserPhone($attendeeId, $lawFirmId);
		            $person_email = fetchLawFirmUserEmail($attendeeId, $lawFirmId);
		            $lawFirm = $_SESSION['lawFirmName'];

		            $stmt_insert_attendee = $connect->prepare("INSERT INTO `event_attendees`(`event_id`, `attendeeId`, `posted_by`, `name`, `email`, `phone`) VALUES (?, ?, ?, ?, ?, ?)");
		            $stmt_insert_attendee->execute([$event_id, $attendeeId, $userId, $person_name, $person_email, $person_phone]);

		            $start = date("D d M, Y", strtotime($start_date)) .' '.date("H:i A", strtotime($start_time));
		            $end = date("D d M, Y", strtotime($end_date)) .' '.date("H:i A", strtotime($end_time));

		            $mail = new PHPMailer(true);
		            try {
		                //Server settings
		                $mail->isSMTP();
		                $mail->Host       = 'smtp.zoho.com'; 
		                $mail->SMTPAuth   = true;                  
		                $mail->Username   = 'support@milatucases.com';    
		                $mail->Password   = 'Javeria##2019';          
		                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		                $mail->Port       = 587;                            

		                //Recipients
		                $mail->setFrom('support@milatucases.com', "$lawFirm Calendar Events");
		                $mail->addAddress($person_email, $person_name);     // Add a recipient

		                // Content
		                $mail->isHTML(true);                                  // Set email format to HTML
		                $mail->Subject = 'Event Updated';
	                	$mail->Body    = "<p>Dear $person_name,</p><p>Event has been rescheduled as follows:</p><p>Title: $title<br>Description: $description<br>From: $start <br>To: $end</p>";

		                $mail->send();
		                //echo "$person_name added to the team and email with login credentials sent";
		                $smsMessage = "Dear $person_name, event has been rescheduled as follows; Title: $title,  From: $start To: $end";
		                sendSMS(API, SENDER, $person_phone, $smsMessage);
		            } catch (Exception $e) {
		                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		            }
		        }
	        } else {
	            $stmt = $connect->prepare("INSERT INTO events (created_by, lawFirmId, is_case_related, case_id, title, description, start_date, start_time, end_date, end_time, color) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	            $stmt->execute([$userId, $lawFirmId, $is_case_related, $caseId, $title, $description, $start_date, $start_time, $end_date, $end_time, $color]);
	            $event_id = $connect->lastInsertId();
	            echo "New event saved successfully!";
	        

		        // Insert attendees into event_attendees table
		        foreach ($_POST['attendees'] as $key => $attendeeId) {
		            
		            $person_name = fetchLawFirmUserName($attendeeId, $lawFirmId);
		            $person_phone = fetchLawFirmUserPhone($attendeeId, $lawFirmId);
		            $person_email = fetchLawFirmUserEmail($attendeeId, $lawFirmId);
		            $lawFirm = $_SESSION['lawFirmName'];

		            $stmt_insert_attendee = $connect->prepare("INSERT INTO `event_attendees`(`event_id`, `attendeeId`, `posted_by`, `name`, `email`, `phone`) VALUES (?, ?, ?, ?, ?, ?)");
		            $stmt_insert_attendee->execute([$event_id, $attendeeId, $userId, $person_name, $person_email, $person_phone]);

		            $start = date("D d M, Y", strtotime($start_date)) .' '.date("H:i A", strtotime($start_time));
		            $end = date("D d M, Y", strtotime($end_date)) .' '.date("H:i A", strtotime($end_time));

		            $mail = new PHPMailer(true);
		            try {
		                //Server settings
		                $mail->isSMTP();
		                $mail->Host       = 'smtp.zoho.com'; 
		                $mail->SMTPAuth   = true;                  
		                $mail->Username   = 'support@milatucases.com';    
		                $mail->Password   = 'Javeria##2019';          
		                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		                $mail->Port       = 587;                            

		                //Recipients
		                $mail->setFrom('support@milatucases.com', "$lawFirm Calendar Events");
		                $mail->addAddress($person_email, $person_name);     // Add a recipient

		                // Content
		                $mail->isHTML(true);                                  // Set email format to HTML
		                $mail->Subject = 'Event Set Up';
	                	$mail->Body    = "<p>Dear $person_name,</p><p>You have been invited to the following event:</p><p>Title: $title<br>Description: $description<br>From: $start <br>To: $end</p>";

		                $mail->send();
		                //echo "$person_name added to the team and email with login credentials sent";
		                $smsMessage = "Dear $person_name, You have been invited to the event: Title: $title,  From: $start To: $end";
		                sendSMS(API, SENDER, $person_phone, $smsMessage);
		            } catch (Exception $e) {
		                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		            }
		        }
	    	}

	        // Commit transaction
	        $connect->commit();
	    } catch (Exception $e) {
	        // Rollback transaction if an error occurs
	        $connect->rollback();
	        echo "Error: " . $e->getMessage();
	    }
	}

?>