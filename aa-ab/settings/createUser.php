<?php
include '../../includes/db.php';
include '../../includes/conf.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lawFirmId = filter_input(INPUT_POST, 'parentId', FILTER_SANITIZE_SPECIAL_CHARS);
    $names = filter_input(INPUT_POST, 'names', FILTER_SANITIZE_SPECIAL_CHARS);
    $firmName = filter_input(INPUT_POST, 'firmName', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phonenumber = filter_input(INPUT_POST, 'phonenumber', FILTER_SANITIZE_SPECIAL_CHARS);
    $passwordPlain = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
    $job = filter_input(INPUT_POST, 'job', FILTER_SANITIZE_SPECIAL_CHARS);
    $lawFirmUserId = filter_input(INPUT_POST, 'lawFirmUserId', FILTER_SANITIZE_NUMBER_INT);
    $country = $_SESSION['country'];
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
    
    // Get the selected permission
    $userRole = filter_input(INPUT_POST, 'permissions', FILTER_SANITIZE_SPECIAL_CHARS);

    if (!empty($lawFirmUserId)) {
        // Update existing user
        $stmt = $connect->prepare("UPDATE lawFirms SET title = ?, names = ?, firmName = ?, email = ?, phonenumber = ?, userRole = ?, job = ?, country = ? WHERE id = ? AND firmId = ?");
        $result = $stmt->execute([$title, $names, $firmName, $email, $phonenumber, $userRole, $job, $country, $lawFirmUserId, $lawFirmId]);

        if ($result) {
            echo "User $names updated successfully";

            // Update password if a new one is provided
            if (!empty($passwordPlain)) {
                $password = password_hash($passwordPlain, PASSWORD_DEFAULT);
                $pwdStmt = $connect->prepare("UPDATE lawFirms SET password = ? WHERE id = ? AND firmId = ?");
                $pwdStmt->execute([$password, $lawFirmUserId, $lawFirmId]);
            }
        } else {
            echo "Error updating user";
        }
    } else {
        // Insert new user
        $joinDate = date('Y-m-d H:i:s');
        $activate = 1;  // Assuming newly added members are activated by default
        $password = password_hash($passwordPlain, PASSWORD_DEFAULT);

        // Check if user already exists
        $checkStmt = $connect->prepare("SELECT COUNT(*) FROM lawFirms WHERE email = ? AND firmId = ?");
        $checkStmt->execute([$email, $lawFirmId]);
        $userExists = $checkStmt->fetchColumn();

        if ($userExists) {
            echo "User: $names already exists in the database";
        } else {
            // Step 1: Get the allocation limit
            $countStmt = $connect->prepare("SELECT members AS allocation_limit FROM subscriptions WHERE lawFirmId = ?");
            $countStmt->execute([$lawFirmId]);
            $subscription = $countStmt->fetch(PDO::FETCH_ASSOC);

            if (!$subscription) {
                die("Subscription not found.");
            }

            $allocationLimit = $subscription['allocation_limit'];

            $userCountStmt = $connect->prepare("SELECT COUNT(*) FROM lawFirms WHERE firmId = ?");
            $userCountStmt->execute([$lawFirmId]);
            $currentUserCount = $userCountStmt->fetchColumn();

            if ($currentUserCount >= $allocationLimit) {
                echo "<span class='text-danger'>You have reached the limit of users for your subscription. Please Upgrade to add more Users.</span>";
            } else {
                $stmt = $connect->prepare("INSERT INTO lawFirms (firmId, title, names, firmName, email, phonenumber, password, parentId, joinDate, activate, userRole, job, country) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $result = $stmt->execute([$lawFirmId, $title, $names, $firmName, $email, $phonenumber, $password, $lawFirmId, $joinDate, $activate, $userRole, $job, $country]);

                if ($result) {
                    $newUserId = $connect->lastInsertId();
                    $lawFirm = $_SESSION['lawFirmName'];

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
                        $mail->setFrom('support@milatucases.com', "Welcome to $lawFirm");
                        $mail->addAddress($email, $names);     

                        // Content
                        $mail->isHTML(true);                                  
                        $mail->Subject = "Your Law Firm ($lawFirm) Account Details";
                        $mail->Body    = "Dear $title $names,<br><br>You have been added to the law firm ($firmName) as $job. Here are your login details:<br>Email: $email<br>Password: $passwordPlain<br><br>Please login at: <a href='https://milatucases.com/login'>https://milatucases.com/login</a><br><br>Thank you.";
                        $mail->AltBody = "Dear $title $names,\n\nYou have been added to the law firm. Here are your login details:\nEmail: $email\nPassword: $passwordPlain\n\nPlease login at: https://milatucases.com/login\n\nThank you.";

                        $mail->send();
                        echo "<span class='text-success'>$title $names added to the team and email with login credentials sent</span>";

                        $smsMessage = "You have been added to the law firm ($firmName). Here are your login details: Email: $email, Password: $passwordPlain";
                        sendSMS(API, SENDER, $phonenumber, $smsMessage);
                    } catch (Exception $e) {
                        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }
                } else {
                    echo "<span class='text-danger'>Error adding $title $names to the team</span>";
                }
            }
        }
    }
}
?>