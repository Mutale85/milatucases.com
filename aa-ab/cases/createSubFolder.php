<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    include("../../includes/db.php");

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $folderName = $_POST['folderName'];
        $caseId = $_POST['caseId'];
        $caseNo = $_POST['caseNo'];
        $uploadedBy = $_SESSION['user_id'];
        $lawFirmId = $_SESSION['parent_id'];

        // Sanitize and format the folder name
        $formattedFolderName = strtolower(str_replace(' ', '_', $folderName));

        // Create the folder in the directory
        $folderPath = $formattedFolderName;
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        // Insert the folder information into the database
        $query = $connect->prepare("INSERT INTO caseFolders (lawFirmId, caseId, caseNo, uploaded_by, folder_name, basename, created_at, archived) VALUES (?, ?, ?, ?, ?, ?, NOW(), 0)");
        $query->execute(array($lawFirmId, $caseId, $caseNo, $uploadedBy, $formattedFolderName, $folderName));

        if ($query->rowCount() > 0) {
            echo "Folder created successfully.";
        } else {
            echo "Failed to create folder.";
        }
    }
?>