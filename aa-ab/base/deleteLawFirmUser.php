<?php
    include '../../includes/db.php';
    include '../../includes/conf.php';
    require '../../vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $lawFirmUserId = filter_input(INPUT_POST, 'lawFirmUserId', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $phonenumber = filter_input(INPUT_POST, 'phonenumber', FILTER_SANITIZE_SPECIAL_CHARS);
        $names = filter_input(INPUT_POST, 'names', FILTER_SANITIZE_SPECIAL_CHARS);
        $firmName = $_SESSION['lawFirmName'];
        
        $checkStmt = $connect->prepare("SELECT COUNT(*) FROM lawFirms WHERE email = ? AND firmId = ? ");
        $checkStmt->execute([$email, $_SESSION['parent_id']]);
        $userExists = $checkStmt->fetchColumn();

        if ($userExists) {
            
            $delete = $connect->prepare("DELETE FROM lawFirms WHERE id = ? ");
            $delete->execute([$lawFirmUserId]);



            // Send email with credentials using PHPMailer
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
                $mail->setFrom('support@milatucases.com', "Removed as User From $firmName");
                $mail->addAddress($email, $names);     // Add a recipient

                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Your Law Firm Account Details';
                $mail->Body    = "Dear $names,<br><br>You have been removed from the law firm $firmName. You nolonger have access to login at: <a href='http://localhost/legalzm.com/login'>http://localhost/legalzm.com/login</a><br><br>Thank you.";
                $mail->AltBody = "Dear $names,\n\nYou have been removed from the law firm $firmName . You nolonger have access to login at: http://localhost/legalzm.com/login\n\nThank you.";

                $mail->send();
                echo "$names removed from the team wont be able to login";
                $smsMessage = "You have been removed from the law firm $firmName . You nolonger have access to login legalzm.com";
                sendSMS(API, SENDER, $phonenumber, $smsMessage);
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    }
?>

