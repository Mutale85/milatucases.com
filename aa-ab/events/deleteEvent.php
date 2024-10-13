<?php 
    include("../../includes/db.php");
    include("../../includes/conf.php");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require '../../vendor/autoload.php';  // Ensure you have PHPMailer loaded via Composer or autoload file

    // Check if the request method is POST
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Get the event ID from the POST data
        $eventId = $_POST['eventId'];
        
        try {
            // Get the event details
            $stmt = $connect->prepare("SELECT title, start_date, start_time, end_date, end_time FROM events WHERE event_id = ?");
            $stmt->execute([$eventId]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($event) {
                // Check if the event has already started or is in the future
                $now = new DateTime();
                $eventStartTime = new DateTime($event['start_date'] . ' ' . $event['start_time']);

                if ($eventStartTime <= $now) {
                    // Event has already started, delete without notifying
                    $stmt = $connect->prepare("DELETE FROM events WHERE event_id = ?");
                    $stmt->execute([$eventId]);

                    $stmt = $connect->prepare("DELETE FROM event_attendees WHERE event_id = ?");
                    $stmt->execute([$eventId]);

                    echo json_encode(['message' => 'Event deleted successfully']);
                } else {
                    // Event is in the future, send notifications before deleting
                    $stmt = $connect->prepare("SELECT name, email, phone FROM event_attendees WHERE event_id = ?");
                    $stmt->execute([$eventId]);
                    $attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $lawFirm = $_SESSION['lawFirmName'];

                    // Send notification emails and SMS to attendees
                    foreach ($attendees as $attendee) {
                        $mail = new PHPMailer(true);
                        try {
                            // Email configuration
                            $mail->isSMTP();
                            $mail->Host       = 'smtp.zoho.com'; 
                            $mail->SMTPAuth   = true;                  
                            $mail->Username   = 'support@milatucases.com';    
                            $mail->Password   = 'Javeria##2019';          
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port       = 587;                            

                            // Email recipients
                            $mail->setFrom('support@milatucases.com', "$lawFirm - Event Cancelled");
                            $mail->addAddress($attendee['email'], $attendee['name']);     // Add a recipient

                            // Email content
                            $mail->isHTML(true);                                  // Set email format to HTML
                            $mail->Subject = 'Event Cancelled';
                            $mail->Body    = "<p>Dear " . $attendee['name'] . ",</p><p>We regret to inform you that the following event has been cancelled:</p><p>Title: " . $event['title'] . "<br>Start: " . $event['start_date'] . " " . $event['start_time'] . "<br>End: " . $event['end_date'] . " " . $event['end_time'] . "</p><p>We apologize for any inconvenience this may cause.</p>";

                            $mail->send();

                            // Send SMS notification
                            $smsMessage = "Dear " . $attendee['name'] . ", the event '" . $event['title'] . "' has been cancelled.";
                            sendSMS(API, SENDER, $attendee['phone'], $smsMessage);
                        } catch (Exception $e) {
                            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                        }
                    }

                    // Now delete the event and attendees
                    $stmt = $connect->prepare("DELETE FROM events WHERE event_id = ?");
                    $stmt->execute([$eventId]);

                    $stmt = $connect->prepare("DELETE FROM event_attendees WHERE event_id = ?");
                    $stmt->execute([$eventId]);

                    echo json_encode(['message' => 'Event deleted and notifications sent']);
                }
            } else {
                // Respond with error message if the event is not found
                echo json_encode(['error' => 'Event not found']);
            }
        } catch (PDOException $e) {
            // Respond with error message if there's a PDO exception
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        // Respond with error message if request method is not POST
        echo json_encode(['error' => 'Invalid request method']);
    }
?>
