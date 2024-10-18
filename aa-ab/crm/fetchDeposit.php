<?php 
	include '../../includes/db.php';
	if(isset($_POST['clientId'])){
		$clientId = $_POST['clientId'];
		fetchDeposits($clientId);
		
	}
?>