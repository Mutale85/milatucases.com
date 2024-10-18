<?php 
	include '../../includes/db.php';
	echo fetchLawFirmExpenses($_SESSION['parent_id']);
?>