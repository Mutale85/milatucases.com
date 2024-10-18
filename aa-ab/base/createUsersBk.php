<?php
    include '../../includes/db.php';
    include '../../includes/conf.php';
    require '../../vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $firmId = filter_input(INPUT_POST, 'parentId', FILTER_SANITIZE_SPECIAL_CHARS);
        $names = filter_input(INPUT_POST, 'names', FILTER_SANITIZE_SPECIAL_CHARS);
        $firmName = filter_input(INPUT_POST, 'firmName', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $phonenumber = filter_input(INPUT_POST, 'phonenumber', FILTER_SANITIZE_SPECIAL_CHARS);
        $passwordPlain = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
        $userRole = filter_input(INPUT_POST, 'userRole', FILTER_SANITIZE_SPECIAL_CHARS);
        $job = filter_input(INPUT_POST, 'job', FILTER_SANITIZE_SPECIAL_CHARS);
        $lawFirmUserId = filter_input(INPUT_POST, 'lawFirmUserId', FILTER_SANITIZE_NUMBER_INT);

        // Check if we're updating or inserting
        if (!empty($lawFirmUserId)) {
            // Update existing user
            $stmt = $connect->prepare("UPDATE lawFirms SET names = ?, firmName = ?, email = ?, phonenumber = ?, userRole = ?, job = ? WHERE id = ? AND firmId = ?");
            $result = $stmt->execute([$names, $firmName, $email, $phonenumber, $userRole, $job, $lawFirmUserId, $firmId]);

            if ($result) {
                echo "User $names updated successfully";
                // Optionally, you can send an email notifying the user of the changes
            } else {
                echo "Error updating user";
            }

            // Update password if a new one is provided
            if (!empty($passwordPlain)) {
                $password = password_hash($passwordPlain, PASSWORD_DEFAULT);
                $pwdStmt = $connect->prepare("UPDATE lawFirms SET password = ? WHERE id = ? AND firmId = ?");
                $pwdStmt->execute([$password, $lawFirmUserId, $firmId]);
            }
        } else {
            // Insert new user
            $joinDate = date('Y-m-d H:i:s');
            $activate = 1;  // Assuming newly added members are activated by default
            $password = password_hash($passwordPlain, PASSWORD_DEFAULT);

            // Check if user already exists
            $checkStmt = $connect->prepare("SELECT COUNT(*) FROM lawFirms WHERE email = ? AND firmId = ?");
            $checkStmt->execute([$email, $firmId]);
            $userExists = $checkStmt->fetchColumn();

            if ($userExists) {
                echo "User: $names already exists in the database";
            } else {
                // Step 1: Get the allocation limit
                $countStmt = $connect->prepare("SELECT members AS allocation_limit FROM subscriptions WHERE lawFirmId = ?");
                $countStmt->execute([$firmId]);
                $subscription = $countStmt->fetch(PDO::FETCH_ASSOC);

                if (!$subscription) {
                    // Handle the case where the subscription is not found
                    die("Subscription not found.");
                }

                $allocationLimit = $subscription['allocation_limit'];

                $userCountStmt = $connect->prepare("SELECT COUNT(*) FROM lawFirms WHERE firmId = ?");
                $userCountStmt->execute([$firmId]);
                $currentUserCount = $userCountStmt->fetchColumn();
                if ($currentUserCount >= $allocationLimit) {
                    echo "<span class='text-danger'>You have reached the limit of users for your subscription. Please Upgrade to add more Users.</span>";

                }else{
                    $stmt = $connect->prepare("INSERT INTO lawFirms (firmId, names, firmName, email, phonenumber, password, parentId, joinDate, activate, userRole, job) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $result = $stmt->execute([$firmId, $names, $firmName, $email, $phonenumber, $password, $firmId, $joinDate, $activate, $userRole, $job]);

                    if ($result) {
                        // Send email with credentials using PHPMailer
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
                            $mail->setFrom('mutamuls@gmail.com', 'Milatucases.com');
                            $mail->addAddress($email, $names);     

                            // Content
                            $mail->isHTML(true);                                  // Set email format to HTML
                            $mail->Subject = 'Your Law Firm Account Details';
                            $mail->Body    = "Dear $names,<br><br>You have been added to the law firm ($firmName) as $job. Here are your login details:<br>Email: $email<br>Password: $passwordPlain<br><br>Please login at: <a href='https://milatucases.com/login'>https://milatucases.com/login</a><br><br>Thank you.";
                            $mail->AltBody = "Dear $names,\n\nYou have been added to the law firm. Here are your login details:\nEmail: $email\nPassword: $passwordPlain\n\nPlease login at: https://milatucases.com/login\n\nThank you.";

                            $mail->send();
                            echo "<span class='text-success'> $names added to the team and email with login credentials sent</span>";

                            $smsMessage = "You have been added to the law firm ($firmName). Here are your login details: Email: $email, Password: $passwordPlain";
                            sendSMS(API, SENDER, $phonenumber, $smsMessage);
                        } catch (Exception $e) {
                            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                        }
                    } else {
                        echo "Error adding new user";
                    }
                }
            }
        }
    }
?>

