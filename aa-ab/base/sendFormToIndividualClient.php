<?php
    include '../../includes/db.php';
    require '../../vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $clientId = decrypt(filter_input(INPUT_POST, 'clientId', FILTER_SANITIZE_SPECIAL_CHARS));
        $email = decrypt(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS));
        $names = filter_input(INPUT_POST, 'names', FILTER_SANITIZE_SPECIAL_CHARS);
        $lawFirmId = decrypt(filter_input(INPUT_POST, 'firm', FILTER_SANITIZE_SPECIAL_CHARS));
        $client_tpin = decrypt(filter_input(INPUT_POST, 'tpin', FILTER_SANITIZE_SPECIAL_CHARS));

        

        //Function to send email with PHPMailer
        function sendEmail($recipientEmail, $subject, $body) {
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.zoho.com';  // Specify SMTP server
                $mail->SMTPAuth   = true;
                $mail->Username   = 'support@milatucases.com';  // SMTP username
                $mail->Password   = 'Javeria##2019';     // SMTP password
                $mail->SMTPSecure = 'tls';               // Enable TLS encryption
                $mail->Port       = 587;                 // TCP port to connect to

                //Recipients
                $mail->setFrom('mutamuls@gmail.com', 'milatucases.com');
                $mail->addAddress($recipientEmail);      // Add a recipient

                //Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $body;
                // $mail->SMTPDebug = 2;

                $mail->send();
                return true;
            } catch (Exception $e) {
                return false;
            }
        }

        // Encrypt the data
        $encryptId = encrypt($clientId);
        $encryptEmail = encrypt($email);
        $encryptNames = encrypt($names);
        $encryptFirm = encrypt($lawFirmId);
        $encryptTpin = encrypt($client_tpin);
          
        $formLink = "https://milatucases.com/individualKycform?clientId={$encryptId}&email={$encryptEmail}&names={$encryptNames}&firm={$encryptFirm}&tpin={$encryptTpin}";

        $sql = $connect->prepare("UPDATE `lawFirmClients` SET `kyc`= 1 WHERE id = ? AND `lawFirmId`= ? ");
        $sql->execute([$clientId, $lawFirmId]);

        // Compose the email
        $subject = 'KYC Form Link';
        $body = 'Dear ' . $names . ',<br><br>';
        $body .= 'Please fill in the KYC form by clicking the link below:<br>';
        $body .= '<a href="' . $formLink . '">Fill in KYC Form</a><br><br>';
        $body .= 'Thank you.<br>';
        $body .= fetchCompanyName();

        // Send the email
        if (sendEmail($email, $subject, $body)) {
            echo 'Email sent successfully';
        } else {
            echo 'Email sending failed';
        }
    }
?>
