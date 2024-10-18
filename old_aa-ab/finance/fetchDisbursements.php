<?php 
	include '../../includes/db.php';
	echo fetchLawFirmDisbursements($_SESSION['parent_id']);
?>