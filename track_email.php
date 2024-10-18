<?php
    include 'includes/db.php';
    require 'vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    if (isset($_GET['id'])) {
        $trackingId = $_GET['id'];
        
        // Update the database to mark the email as opened
        $stmt = $connect->prepare("UPDATE email_tracking SET opened = 1, opened_date = NOW() WHERE tracking_id = ?");
        $stmt->execute([$trackingId]);
        
        // Fetch the email details
        $stmt = $connect->prepare("SELECT client_email, invoice_id FROM email_tracking WHERE tracking_id = ?");
        $stmt->execute([$trackingId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            // Send a thank you email
            sendThankYouEmail($result['client_email'], $result['invoice_id']);
        }
    }

    // Output a 1x1 transparent GIF
    header('Content-Type: image/gif');
    echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

    function sendThankYouEmail($clientEmail, $invoiceId) {
        $mail = new PHPMailer(true);
        try {
            // Server settings (use the same settings as in your original email code)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mutamuls@gmail.com';
            $mail->Password = 'mdbm npox ftcj ougf';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('mutamuls@gmail.com', 'LegalZM');
            $mail->addAddress($clientEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Thank you for reviewing your invoice';
            $mail->Body = "Dear Client,<br><br>Thank you for taking the time to review your invoice #$invoiceId. If you have any questions or concerns, please don't hesitate to contact us.<br><br>Best regards,<br>Your Company Name";

            $mail->send();
        } catch (Exception $e) {
            error_log("Error sending thank you email: " . $mail->ErrorInfo);
        }
    }