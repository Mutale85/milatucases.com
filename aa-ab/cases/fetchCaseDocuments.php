<?php 
	include "../../includes/db.php";
	if(isset($_POST['caseId'])){
		$caseId = $_POST['caseId'];
		$caseNo = $_POST['caseNo'];
		echo displayCaseDocuments($caseId, $caseNo);
	}
?>