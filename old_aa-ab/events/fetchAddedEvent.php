<?php
	
	include("../../includes/db.php");
	$lawFirmId = $_SESSION['parent_id'];
	echo fetchandDisplayEVent($lawFirmId);
?>