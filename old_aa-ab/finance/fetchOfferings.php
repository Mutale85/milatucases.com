<?php 
	include '../../includes/db.php';
	echo fetchChurchOfferings($_SESSION['parent_id']);
?>