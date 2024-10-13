<?php
	
	include("../../includes/db.php");
	$lawFirmId = $_SESSION['parent_id'];
	$userId = $_SESSION['user_id'];
	echo fetchAndDisplayPersonalEvent($userId, $lawFirmId);
?>