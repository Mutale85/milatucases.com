<?php
    require_once '../vendor/autoload.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    include("../includes/db.php");
    include("../includes/conf.php");

    // $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    // $dotenv->load();

    $names = filter_input(INPUT_POST, 'names', FILTER_SANITIZE_SPECIAL_CHARS);
    $firmName = filter_input(INPUT_POST, 'firmName', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phonenumber', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
    $members = filter_input(INPUT_POST, 'members', FILTER_SANITIZE_NUMBER_INT);
    $job = filter_input(INPUT_POST, 'job', FILTER_SANITIZE_SPECIAL_CHARS);

    $stmt = $connect->prepare("SELECT COUNT(*) FROM lawFirms WHERE email = ? ");
    $stmt->execute([$email]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        exit('Email already registered');
    }

    if ($email === false) {
        exit('Invalid email format');
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $firmId = generateRandomPassword();
    $start_date = date("Y-m-d");
    $tier = 'trial';
    try {
        $connect->beginTransaction();

        $stmt = $connect->prepare("INSERT INTO lawFirms (names, firmId, firmName, email, phonenumber, password, parentId, job) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$names, $firmId, $firmName, $email, $phone, $hashedPassword, $firmId, $job]);

        $parentId = $connect->lastInsertId();
        $trialEndDate = date('Y-m-d H:i:s', strtotime('+21 days'));

        $sql = $connect->prepare("INSERT INTO subscriptions (lawFirmId, parentId, tier, start_date, end_date, members) VALUES(?, ?, ?, ?, ?, ?)"); 
        $sql->execute([$firmId, $parentId, $tier, $start_date, $trialEndDate, $members]);

        $add = 1;
        $edit = 1;
        $delete = 1;
        $view = 1;

        $permStmt = $connect->prepare("INSERT INTO permissions (userId, edit_, delete_, add_, view_) VALUES (?, ?, ?, ?, ?)");
        $permResult = $permStmt->execute([$parentId, $edit, $delete, $add, $view]);

        $connect->commit();

        sendWelcomeEmail($email, $names, $trialEndDate);
        $message = "Welcome to milatucases.com! You have a 21-day trial period under the trial tier. Please upgrade after this period. If you need any help, call +260976330092.";
        sendSMS(API, SENDER, $phone, $message);
        

        echo 'Registration successful.';
    } catch (Exception $e) {
        $connect->rollBack();
        echo 'Error: ' . $e->getMessage();
    }

    function sendWelcomeEmail($email, $names, $trialEndDate) {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->SMTPDebug = 0;  // Enable verbose debug output
            
            $mail->isSMTP();
            $mail->Host = 'smtp.zoho.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mulenga@milatucases.com';
            $mail->Password = 'Javeria##2019'; // Consider using environment variables for credentials
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('mulenga@milatucases.com', 'Welcome to Milatucases');
            $mail->addAddress($email, $names);  // Add a recipient

            // Content
            $mail->isHTML(true);  // Set email format to HTML
            $mail->Subject = 'Welcome to Milatucases.com';
            $mail->Body    = 'Hello ' . $names . ',<br><br>Welcome to Milatucases.com! You have a 14-day trial period under the trial tier. Please upgrade after this period.<br>Your trial ends on ' . $trialEndDate . '.<br><br>If you need any help, call +260976330092.<br><br>Best regards,<br> Milatucases Team';
            $mail->AltBody = 'Hello ' . $names . ',\n\nWelcome to Milatucases.com! You have a 14-day trial period under the trial tier. Please upgrade after this period.\nYour trial ends on ' . $trialEndDate . '.\n\nIf you need any help, call +260976330092.\n\nBest regards,\nChurch Cloud Team';

            $mail->send();
            // echo 'Welcome email has been sent';
            
        } catch (Exception $e) {
            echo "Welcome email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    $admin_email = "mutamuls@gmail.com";
    $newMsg = "$firmName has joined milatucases and is on trial till {$trialEndDate}";
    sendWelcomeEmail($admin_email, $firmName, $trialEndDate);
    $adminPhone = '260976330092';
    sendSMS(API, SENDER, $adminPhone, $newMsg);

?>
