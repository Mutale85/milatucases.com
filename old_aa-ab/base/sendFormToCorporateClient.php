<?php
    include '../../includes/db.php';
    require '../../vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $clientId = decrypt(filter_input(INPUT_POST, 'clientId', FILTER_SANITIZE_SPECIAL_CHARS));
        $email = decrypt(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS));
        $names = decrypt(filter_input(INPUT_POST, 'names', FILTER_SANITIZE_SPECIAL_CHARS));
        $lawFirmId = decrypt(filter_input(INPUT_POST, 'firm', FILTER_SANITIZE_SPECIAL_CHARS));
        $tpin = decrypt(filter_input(INPUT_POST, 'tpin', FILTER_SANITIZE_SPECIAL_CHARS));
        $busiName = filter_input(INPUT_POST, 'busiName', FILTER_SANITIZE_SPECIAL_CHARS);


        // Function to send email with PHPMailer
        function sendEmail($recipientEmail, $subject, $body) {
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.zoho.com'; 
                $mail->SMTPAuth   = true;
                $mail->Username   = 'support@milatucases.com'; 
                $mail->Password   = 'Javeria##2019';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;
                $mail->setFrom('mutamuls@gmail.com', 'milatucases.com');
                $mail->addAddress($recipientEmail);

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
        $encryptId = base64_encode($clientId);
        $encryptEmail = base64_encode($email);
        $encryptNames = base64_encode($names);
        $encryptFirm = base64_encode($lawFirmId);
        $encryptBusiness = base64_encode($busiName);
        $encryptTpin = base64_encode($tpin);
          
        $formLink = "https://milatucases.com/form?clientId={$encryptId}&email={$encryptEmail}&names={$encryptNames}&firm={$encryptFirm}&tpin={$encryptTpin}&busiName={$encryptBusiness}";

        $sql = $connect->prepare("UPDATE `lawFirmClients` SET `kyc` = 1 WHERE id = ? AND `lawFirmId`= ? ");
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
