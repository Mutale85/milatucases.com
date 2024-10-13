<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    include '../../includes/db.php';
    require '../../vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $invoiceId = $_POST['invoiceId'];
        $clientId = $_POST['clientId'];
        $clientEmail = $_POST['clientEmail'];
        $pdfFilename = $_POST['filepath'];
        $company = fetchCompanyName();
        $lawFirmId = $_SESSION['parent_id'];
        $clientNames = getClientNameById($clientId, $lawFirmId);
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.zoho.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'support@milatucases.com';
            $mail->Password = 'Javeria##2019'; // Consider using environment variables for credentials
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('support@milatucases.com', 'Invoice');
            $mail->addAddress($clientEmail);

            // Attachments
            $mail->addAttachment($pdfFilename);
            $lawFirmName = $_SESSION['lawFirmName'];
            // Content
            $mail->isHTML(true);
            $mail->Subject = "Your Invoice from $lawFirmName";
            
            // Personalized email body
            $mail->Body = '
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { width: 100%; margin: 0 auto; }
                    .footer { margin-top: 20px; font-size: 12px; color: #888; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h2>Dear, '.$clientNames.'</h2>
                    <p>We hope this message finds you well.</p>
                    <p>Please find attached the Invoice document for the recent works. If you have any questions or need further assistance, feel free to reach out to us.</p>
                    <p>Thank you for your business!</p>
                    <p>Best regards,</p>
                    
                    <div class="footer">
                        '.$company.'
                    </div>
                </div>
            </body>
            </html>';

            $mail->send();
            echo "The Invoice has been successfully created and sent to $clientEmail.";
            // Optionally delete the file after sending
            // unlink($pdfFilename);
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
?>