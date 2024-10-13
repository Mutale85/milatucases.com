<?php
require_once '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("../includes/db.php");
include("../includes/conf.php");

// Input sanitization
$names = filter_input(INPUT_POST, 'names', FILTER_SANITIZE_SPECIAL_CHARS);
$firmName = filter_input(INPUT_POST, 'firmName', FILTER_SANITIZE_SPECIAL_CHARS);
$country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$phone = filter_input(INPUT_POST, 'phonenumber', FILTER_SANITIZE_SPECIAL_CHARS);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
$members = filter_input(INPUT_POST, 'members', FILTER_SANITIZE_NUMBER_INT);
$job = filter_input(INPUT_POST, 'job', FILTER_SANITIZE_SPECIAL_CHARS);
$appsumocode = filter_input(INPUT_POST, 'appsumocode', FILTER_SANITIZE_SPECIAL_CHARS);
$userRole = 'superAdmin';
// Check if email already exists
$stmt = $connect->prepare("SELECT COUNT(*) FROM lawFirms WHERE email = ?");
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
$tier = 'appsumo';

try {
    $connect->beginTransaction();

    $statement = $connect->prepare("SELECT * FROM appsumo_sales_code WHERE unique_code = ? LIMIT 1");
    $statement->execute([$appsumocode]);
    $countRows = $statement->rowCount();

    if ($countRows == 1) {
        $row = $statement->fetch();
        $sold_status = $row['sold_status'];
        $unique_code = $row['unique_code'];
        
        if ($sold_status == "1") {
            echo $appsumocode . " has already been redeemed";
            exit();
        }

        // Update AppSumo code status
        $update_stmt = $connect->prepare("UPDATE appsumo_sales_code SET sold_status = '1', buyer_email = ?, date_sold = NOW() WHERE unique_code = ?");
        $update_stmt->execute([$email, $appsumocode]);

        // Insert new law firm
        $stmt = $connect->prepare("INSERT INTO lawFirms (names, firmId, firmName, email, phonenumber, password, parentId, userRole, job, country) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$names, $firmId, $firmName, $email, $phone, $hashedPassword, $firmId, $userRole, $job, $country]);

        $parentId = $connect->lastInsertId();
        $lifetimeEndDate = '2099-12-31 23:59:59'; // Set a far future date for lifetime deal

        // Insert subscription
        $sql = $connect->prepare("INSERT INTO subscriptions (lawFirmId, parentId, tier, start_date, end_date, transaction_id, members) VALUES(?, ?, ?, ?, ?, ?, ?)"); 
        $sql->execute([$firmId, $parentId, $tier, $start_date, $lifetimeEndDate, $appsumocode, $members]);

        $connect->commit();

        sendWelcomeEmail($email, $names, $lifetimeEndDate);
        $message = "Welcome to milatucases.com! You have redeemed your AppSumo lifetime deal. If you need any help, call +260976330092.";
        if($country == 'Zambia'){
            sendSMS(API, SENDER, $phone, $message);
        } else {
            sendIntSMS($phone, $message);
        }

        echo 'Registration successful. Lifetime deal activated.';

    } else {
        exit("Invalid AppSumo code");
    }
} catch (Exception $e) {
    $connect->rollBack();
    echo 'Error: ' . $e->getMessage();
}

function sendWelcomeEmail($email, $names, $endDate) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.zoho.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'info@milatucases.com';
        $mail->Password = 'Javeria##2019'; // Consider using environment variables for credentials
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('info@milatucases.com', 'Welcome to Milatucases');
        $mail->addAddress($email, $names);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Milatucases.com - Lifetime Deal Activated';
        $mail->Body    = 'Hello ' . $names . ',<br><br>Welcome to Milatucases.com! Your AppSumo lifetime deal has been successfully activated.<br><br>If you need any help, call +260976330092.<br><br>Best regards,<br>Milatucases Team';
        $mail->AltBody = 'Hello ' . $names . ',\n\nWelcome to Milatucases.com! Your AppSumo lifetime deal has been successfully activated.\n\nIf you need any help, call +260976330092.\n\nBest regards,\nMilatucases Team';

        $mail->send();
    } catch (Exception $e) {
        echo "Welcome email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

$admin_email = "mutamuls@gmail.com";
$newMsg = "$firmName has joined milatucases with an AppSumo lifetime deal";
sendWelcomeEmail($admin_email, $firmName, $lifetimeEndDate);
$adminPhone = '260976330092';
sendSMS(API, SENDER, $adminPhone, $newMsg);
?>
