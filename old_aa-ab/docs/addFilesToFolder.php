<?php
    include '../../includes/db.php';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    // Check if folder_id and files are set
    if (isset($_POST['folder_id']) && !empty($_FILES['files']['name'][0])) {
        $folderId = $_POST['folder_id'];

        // Decrypt the folder ID
        $folder_id = base64_decode($folderId);
        $lawFirmId = $_SESSION['parent_id'];
        $uploadedBy = $_POST['uploaded_by'];
        $uploadDir = "uploads/";
        // echo $folder_id;

        $fileCount = count($_FILES['files']['name']);
        $uploadedFiles = [];

        for ($i = 0; $i < $fileCount; $i++) {
            $fileName = basename($_FILES['files']['name'][$i]);
            $targetFilePath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $targetFilePath)) {
                // Insert file information into database
                $stmt = $connect->prepare("INSERT INTO lawFirmFiles (lawFirmId, file_name, file_path, uploaded_by, folder_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([ $lawFirmId, $fileName, $targetFilePath, $uploadedBy, $folder_id]);

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
