<?php 
	include '../../../includes/db.php';

	if($_SERVER['REQUEST_METHOD'] === "POST"){
		$folderId = $_POST['folder_id'];
		fetchCaseFolderFiles($folderId);
	}
?>