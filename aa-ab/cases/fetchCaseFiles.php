<?php 
	include '../../includes/db.php';
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		$caseId = $_POST['caseId'];
		$lawFirmId = $_POST['lawFirmId'];
		$caseNo = $_POST['caseNo'];
		echo fetchCasePostedDocuments($caseId, $lawFirmId);
	}
?>