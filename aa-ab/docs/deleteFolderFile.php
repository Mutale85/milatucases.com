<?php
    include '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $file_name = $_POST['file_name'];
        $lawFirmId = $_SESSION['parent_id'];
        $folderId = $_POST['folder_id'];

        // Decrypt the folder ID
        $folder_id = decrypt($folderId);

        // Delete the file from the server
        $filePath = "uploads/" . $file_name;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete the file information from the database
        $stmt = $connect->prepare("DELETE FROM lawFirmFiles WHERE lawFirmId = ? AND file_name = ? AND folder_id = ? ");
        $stmt->execute([$lawFirmId, $file_name, $folder_id]);

        if ($stmt) {
            echo "File deleted successfully.";
        } else {
            echo "Error deleting file from the database.";
        }
    }
?>
