<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	include("../includes/db.php");
	require '../vendor/autoload.php';

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	    $newPassword = $_POST['newPassword'];
	    $confirmPassword = $_POST['confirmPassword'];
	    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
	    $passtoken = $_POST['passtoken'];

	    if (empty($email)) {
	        echo "Email address is required.";
	        exit();
	    }

	    if ($newPassword !== $confirmPassword) {
	        echo "Passwords do not match.";
	        exit();
	    }

	    $query = $connect->prepare("SELECT * FROM lawFirms WHERE email = ? AND reset_token = ? ");
	    $query->execute(array($email, $passtoken));

	    if ($query->rowCount() > 0) {
	        $user = $query->fetch(PDO::FETCH_ASSOC);
	        $userId = $user['id'];
	        $lawFirmName = $user['firmName'];
	       
	        // Hash the new password
	        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

	        // Update the password and reset token in the database
	        $updateQuery = $connect->prepare("UPDATE lawFirms SET password = ?, reset_token = '', reset_token_expiration = NOW() WHERE reset_token = ? AND email = ?");
	        $updateQuery->execute(array($hashedPassword, $passtoken, $email));

	        /*/ Send the password reset confirmation email
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
	        $mail->Subject = 'Password Reset Confirmation';
	        $mail->Body = "Your password has been successfully reset. If you did not request this change, please contact our support team immediately.";

	        if ($mail->send()) {
	            echo "Password has been updated successfully.";
	        } else {
	            echo "Failed to send password reset confirmation email.";
	        }
	        */
	         echo "Password has been updated successfully";
	    } else {
	        echo 'Reset Token Invalid';
	    }
	}
?>