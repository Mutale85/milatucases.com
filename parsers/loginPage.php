<?php
 	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	include("../includes/db.php");

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS));
		$p_word = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));

		if ($email === "") {
			echo "email address is required";
			exit();
		}
		
		if ($p_word === "") {
			echo "password is required";
			exit();
		}
		
		$query = $connect->prepare("SELECT * FROM lawFirms WHERE email = ? ");
		$query->execute(array($email));

		if ($query->rowCount() > 0) {
			foreach ($query->fetchAll() as $row) {
				extract($row);
				if($activate === 1){
					if (password_verify($p_word, $password)) {
						$_SESSION['email'] 			= $email;
						$_SESSION['names'] 			= $names;
					    $_SESSION['user_id'] 		= $id;
					    $_SESSION['phone'] 			= $phonenumber;
					    $_SESSION['lawFirmName'] 	= $firmName;
					    $_SESSION['user_role'] 		= $userRole;
					    $_SESSION['lawFirm_Account']= $email;
					    $_SESSION['parent_id'] 		= $parentId;
						$_SESSION['userJob'] 		= $job;
						$_SESSION['country'] 		= $country;

					    setcookie("lawFirm", base64_encode($_SESSION['email']. password_hash($_SESSION['email'], PASSWORD_DEFAULT)), time()+60*60*24*30, '/');
						setcookie("lawFirmAccount", $userRole, time()+60*60*24*30, '/');

						$sql = $connect->prepare("INSERT INTO user_logins (user_id, lawFirmId, login_time) VALUES (?, ?, NOW())");
	                    $sql->execute([$id, $parentId]);
	                    
	                    // Store the login entry ID in session to update logout time later
	                    $_SESSION['login_entry_id'] = $connect->lastInsertId();
	                    
	                    // Check if it's the user's first login
	                    $firstLoginQuery = $connect->prepare("SELECT COUNT(*) FROM user_logins WHERE user_id = ?");
	                    $firstLoginQuery->execute([$id]);
	                    $loginCount = $firstLoginQuery->fetchColumn();

	                    if($loginCount == 1){
	                    	$_SESSION['first_login'] = "true";
	                    }else{
	                    	$_SESSION['first_login'] = "false";
	                    }
					    echo "Login Successfull";

					}else{
						echo "Incorrect login credentials";
						exit();
					}
				}else{
					echo "User is not not allowed to login";
					exit();
				}
			}
		}else{
			echo 'User not found';
			exit();
		}
	}
	
?>