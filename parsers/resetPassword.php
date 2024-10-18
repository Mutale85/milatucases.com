<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	include("../includes/db.php");
	require '../vendor/autoload.php';

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

	    if (empty($email)) {
	        echo "Email address is required.";
	        exit();
	    }

	    $query = $connect->prepare("SELECT * FROM lawFirms WHERE email = ?");
	    $query->execute(array($email));

	    if ($query->rowCount() > 0) {
	        $user = $query->fetch(PDO::FETCH_ASSOC);
	        $userId = $user['id'];
	        $lawFirmName = $user['firmName'];
	        $resetToken = bin2hex(random_bytes(32));

	        // Update the reset token in the database
	        $updateQuery = $connect->prepare("UPDATE lawFirms SET reset_token = ?, reset_token_expiration = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?");
	        $updateQuery->execute(array($resetToken, $userId));

	        // Send the password reset email
	        $mail = new PHPMailer();
	        $mail->isSMTP();
	        $mail->Host = 'smtp.zoho.com';
	        $mail->SMTPAuth = true;
	        $mail->Username = 'support@milatucases.com';
	        $mail->Password = 'Javeria##2019';
	        $mail->SMTPSecure = 'tls';
	        $mail->Port = 587;

	        $mail->setFrom('support@milatucases.com', $lawFirmName);
	        $mail->addAddress($email);
	        $mail->Subject = 'Password Reset Request';
	        $mail->Body = "Please click the following link to reset your password: https://milatucases.com/reset-password?token=$resetToken&email=$email";

	        if ($mail->send()) {
	            echo "Password reset link has been sent to your email.";
	        } else {
	            echo "Failed to send password reset email.";
	        }
	    } else {
	        echo 'User not found.';
	    }
	}
?>