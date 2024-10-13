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
	    	$matterTitle = fetchCaseTitleById($caseId);
	    	$title = $matterTitle;
	    }
	    /*
	    try {
	        // Start transaction
	        $connect->beginTransaction();

	        if (!empty($event_id)) {
	            $stmt = $connect->prepare("UPDATE events_personal SET title = ?, description = ?, start_date = ?, start_time = ?, end_date = ?, end_time = ?, color = ?, is_case_related = ?, case_id = ? WHERE event_id = ?");
	            $stmt->execute([$title, $description, $start_date, $start_time, $end_date, $end_time, $color, $is_case_related, $caseId, $event_id]);
	            echo "Event updated successfully!";

	        } else {
	            $stmt = $connect->prepare("INSERT INTO events_personal (created_by, lawFirmId, is_case_related, case_id, title, description, start_date, start_time, end_date, end_time, color) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	            $stmt->execute([$userId, $lawFirmId, $is_case_related, $caseId, $title, $description, $start_date, $start_time, $end_date, $end_time, $color]);
	            $event_id = $connect->lastInsertId();
	            echo "New event saved successfully!";
	        }
	            
            $person_name = fetchLawFirmUserName($userId, $lawFirmId);
            $person_phone = fetchLawFirmUserPhone($userId, $lawFirmId);
            $person_email = fetchLawFirmUserEmail($userId, $lawFirmId);

            $title = html_entity_decode($title);
            $description = html_entity_decode($description);

            $mail = new PHPMailer(true);
            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; 
                $mail->SMTPAuth   = true;                  
                $mail->Username   = 'mutamuls@gmail.com';    
                $mail->Password   = 'mdbm npox ftcj ougf';          
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;                            

                //Recipients
                $mail->setFrom('mutamuls@gmail.com', 'Personal Event');
                $mail->addAddress($person_email, $person_name);     // Add a recipient

                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Event Set Up';
            	$mail->Body    = "<p>Dear $person_name,</p><p>You have set up an event:</p><p>Title: $title<br>Description: $description<br>Start: $start_date $start_time<br>End: $end_date $end_time</p>";

                $mail->send();
                //echo "$person_name added to the team and email with login credentials sent";
                $smsMessage = "Dear $person_name, You have set up an event: Title: $title,  Start: $start_date $start_time End: $end_date $end_time";
                sendSMS(API, SENDER, $person_phone, $smsMessage);
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
	        

	        // Commit transaction
	        $connect->commit();
	    } catch (Exception $e) {
	        // Rollback transaction if an error occurs
	        $connect->rollback();
	        echo "Error: " . $e->getMessage();
	    }
	    */

	    try {
		    // Start transaction
		    $connect->beginTransaction();

		    // Convert times to comparable format (e.g., timestamps) if needed
		    // $start_datetime = $start_date . ' ' . $start_time;
		    // $end_datetime = $end_date . ' ' . $end_time;

		    // // Check for overlapping events
		    // $check_stmt = $connect->prepare("
		    //     SELECT COUNT(*) FROM events_personal
		    //     WHERE (start_date = :start_date AND start_time < :end_time)
		    //        OR (end_date = :end_date AND end_time > :start_time)
		    //        OR (start_date < :end_date AND end_date > :start_date)
		    //        AND event_id != :event_id
		    // ");
		    // $check_stmt->execute([
		    //     ':start_date' => $start_date,
		    //     ':end_date' => $end_date,
		    //     ':start_time' => $start_time,
		    //     ':end_time' => $end_time,
		    //     ':event_id' => $event_id // Exclude the current event if updating
		    // ]);

		    // $conflicting_events = $check_stmt->fetchColumn();

		    // if ($conflicting_events > 0) {
		    //     throw new Exception("Cannot add or update event. The time slot conflicts with an existing event.");
		    // }

		    // If no conflict, proceed with insert or update
		    if (!empty($event_id)) {
		        // Update existing event
		        $stmt = $connect->prepare("
		            UPDATE events_personal 
		            SET title = ?, description = ?, start_date = ?, start_time = ?, end_date = ?, end_time = ?, color = ?, is_case_related = ?, case_id = ? 
		            WHERE event_id = ?
		        ");
		        $stmt->execute([$title, $description, $start_date, $start_time, $end_date, $end_time, $color, $is_case_related, $caseId, $event_id]);
		        echo "Event updated successfully!";
		    } else {
		        // Insert new event
		        $stmt = $connect->prepare("
		            INSERT INTO events_personal (created_by, lawFirmId, is_case_related, case_id, title, description, start_date, start_time, end_date, end_time, color) 
		            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
		        ");
		        $stmt->execute([$userId, $lawFirmId, $is_case_related, $caseId, $title, $description, $start_date, $start_time, $end_date, $end_time, $color]);
		        $event_id = $connect->lastInsertId();
		        echo "New event saved successfully!";
		    }

		    // Send email and SMS (code unchanged)
		    $person_name = fetchLawFirmUserName($userId, $lawFirmId);
		    $person_phone = fetchLawFirmUserPhone($userId, $lawFirmId);
		    $person_email = fetchLawFirmUserEmail($userId, $lawFirmId);

		    $title = html_entity_decode($title);
		    $description = html_entity_decode($description);

		    $mail = new PHPMailer(true);
		    try {
		        //Server settings
		        $mail->isSMTP();
		        $mail->Host       = 'smtp.zoho.com'; 
		        $mail->SMTPAuth   = true;                  
		        $mail->Username   = 'support@milatucases.com';    
		        $mail->Password   = 'mdbm npox ftcj ougf';          
		        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		        $mail->Port       = 587;                            

		        //Recipients
		        $mail->setFrom('support@milatucases.com', 'Personal Event Calendar');
		        $mail->addAddress($person_email, $person_name);     // Add a recipient

		        // Content
		        $mail->isHTML(true);                                  // Set email format to HTML
		        $mail->Subject = 'Event Set Up';
		        $mail->Body    = "<p>Dear $person_name,</p><p>You have set up an event:</p><p>Title: $title<br>Description: $description<br>Start: $start_date $start_time<br>End: $end_date $end_time</p>";

		        $mail->send();
		        $smsMessage = "Dear $person_name, You have set up an event: Title: $title,  Start: $start_date $start_time End: $end_date $end_time";
		        sendSMS(API, SENDER, $person_phone, $smsMessage);
		    } catch (Exception $e) {
		        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
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