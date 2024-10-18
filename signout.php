<?php 
	include 'includes/db.php';

	// Update logout_time before clearing session variables
	if (isset($_SESSION['login_entry_id'])) {
		$sql = "UPDATE user_logins SET logout_time = NOW() WHERE id = ?";
		$stmt = $connect->prepare($sql);
		$stmt->execute([$_SESSION['login_entry_id']]);
	}

	// Clear the session variables
	$_SESSION = array();

	// If it's desired to kill the session, also delete the session cookie
	if (ini_get("session.use_cookies")) {
	    $params = session_get_cookie_params();
	    setcookie(session_name(), '', time() - 42000,
	        $params["path"], $params["domain"],
	        $params["secure"], $params["httponly"]
	    );
	}

	// Destroy the session
	session_destroy();

	// Unset cookies by setting them to expire in the past
	setcookie("lawFirm", "", time() - 3600, '/');
	setcookie("lawFirmAccount", "", time() - 3600, '/');

	// Redirect to the login page
	header("Location: login");
	exit;
?>
