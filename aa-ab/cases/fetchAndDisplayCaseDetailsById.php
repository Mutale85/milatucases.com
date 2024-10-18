<?php 
	include '../../includes/db.php';
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$caseId = preg_replace("[^0-9]", "", $_POST['caseId']);
		$lawFirmId = filter_input(INPUT_POST, 'lawFirmId', FILTER_SANITIZE_SPECIAL_CHARS);
		echo displayCaseDetailsById($caseId, $lawFirmId);
	}
?>