<?php 
	include '../../includes/db.php';
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		$caseId = $_POST['caseId'];
		$lawFirmId = $_POST['lawFirmId'];
		echo displayFolders($caseId, $lawFirmId);
	}
?>