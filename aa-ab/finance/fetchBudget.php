<?php 
	include '../../includes/db.php';
	echo fetchChurchBudgets($_SESSION['parent_id']);
?>