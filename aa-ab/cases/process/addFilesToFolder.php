<?php
    include '../../../includes/db.php';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    // Check if folder_id and files are set
    if (isset($_POST['folder_id']) && !empty($_FILES['files']['name'][0])) {
        $folderId = $_POST['folder_id'];
        $folderName = fetchCaseFolderNameById($folderId);
        $lawFirmId = $_SESSION['parent_id'];
        $uploadedBy = $_POST['userId'];
        $uploadDir = "../$folderName/";
        $userId = $_SESSION['user_id'];
        $lawFirmId = $_SESSION['parent_id'];
        $caseId = $_POST['caseId'];
        $caseNo = $_POST['caseNo'];
        $dateAdded = date('Y-m-d H:i:s');

        $fileCount = count($_FILES['files']['name']);
        $uploadedFiles = [];

        for ($i = 0; $i < $fileCount; $i++) {
            $fileName = basename($_FILES['files']['name'][$i]);
            $targetFilePath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $targetFilePath)) {
                // Insert file information into database
                $stmt = $connect->prepare("INSERT INTO `caseDocuments` (`caseId`, `caseNo`, `documentName`, `date_added`, `userId`, `lawFirmId`, `folderId`) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$caseId, $caseNo, $fileName, $dateAdded, $userId, $lawFirmId, $folderId]);
                $uploadedFiles[] = $fileName;
            }
        }

        if (!empty($uploadedFiles)) {
            echo "Files uploaded successfully: " . implode(", ", $uploadedFiles);
        } else {
            echo "Failed to upload files.";
        }
    } else {
        echo "No files selected or folder ID is missing.";
    }


?>
