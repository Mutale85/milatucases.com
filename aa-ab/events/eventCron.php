<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    include("../../includes/db.php");
    include("../../includes/conf.php");
    require '../../vendor/autoload.php'; // Make sure to include the autoload file for PHPMailer and Twilio

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    function sendEventReminders() {
        global $connect;
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        
        $stmt = $connect->prepare("
            SELECT e.event_id, e.title, e.start_date, e.start_time, e.description,
                   ea.name, ea.email, ea.phone
            FROM events e
            JOIN event_attendees ea ON e.event_id = ea.event_id
            WHERE e.start_date = :tomorrow
            ORDER BY e.start_time
        ");
        $stmt->execute(['tomorrow' => $tomorrow]);
        
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $attendeeEvents = [];
        foreach ($events as $event) {
            $attendeeKey = $event['email'] . '|' . $event['phone'];
            if (!isset($attendeeEvents[$attendeeKey])) {
                $attendeeEvents[$attendeeKey] = [
                    'name' => $event['name'],
                    'email' => $event['email'],
                    'phone' => $event['phone'],
                    'events' => []
                ];
            }
            $attendeeEvents[$attendeeKey]['events'][] = [
                'title' => $event['title'],
                'start_time' => $event['start_time'],
                'description' => $event['description']
            ];
        }
        
        foreach ($attendeeEvents as $attendee) {
            sendEmailReminder($attendee);
            $message = "Reminder: You have " . count($attendee['events']) . " event(s) tomorrow. ";
            $message .= "First event: " . $attendee['events'][0]['title'] . " at " . $attendee['events'][0]['start_time'];
            sendSMS(API, SENDER, $attendee, $message);
        }
    }

    function sendEmailReminder($attendee) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.zoho.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'your_username';
            $mail->Password   = 'your_password';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('support@milatucases', 'Firms Events');
            $mail->addAddress($attendee['email'], $attendee['name']);

            $mail->isHTML(true);
            $mail->Subject = "Reminder: Your events for tomorrow";

            $message = "
            <html>
            <head>
                <style>
                    table {
                        border-collapse: collapse;
                        width: 100%;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: left;
                    }
                    th {
                        background-color: #f2f2f2;
                    }
                </style>
            </head>
            <body>
                <p>Dear {$attendee['name']},</p>
                <p>This is a reminder that you have the following event(s) scheduled for tomorrow:</p>
                <table>
                    <tr>
                        <th>Event</th>
                        <th>Time</th>
                        <th>Description</th>
                    </tr>";

            foreach ($attendee['events'] as $event) {
                $message .= "
                    <tr>
                        <td>{$event['title']}</td>
                        <td>{$event['start_time']}</td>
                        <td>{$event['description']}</td>
                    </tr>";
            }

            $message .= "
                </table>
                <p>Best regards,<br>Your Event Management System</p>
            </body>
            </html>";

            $mail->Body = $message;

            $mail->send();
            echo "Email reminder sent to {$attendee['email']}\n";
        } catch (Exception $e) {
            echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}\n";
        }
    }

    
    try {
        sendEventReminders();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }