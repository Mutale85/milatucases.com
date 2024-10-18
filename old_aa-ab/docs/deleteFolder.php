<?php
    include '../../includes/db.php';

    // Check if folder_id is set and is numeric
    if (isset($_POST['folder_id']) && is_numeric($_POST['folder_id'])) {
        $folderId = $_POST['folder_id'];

        // Delete the folder from the database
        $stmt = $connect->prepare("DELETE FROM lawFirmFolders WHERE id = ?");
        $stmt->execute([$folderId]);

        // Check if the folder was deleted successfully
        if ($stmt->rowCount() > 0) {
            // Return a success message
            echo 'Folder deleted successfully.';
        } else {
            // Return an error message
            echo 'Failed to delete folder.';
        }
    } else {
        // Return an error message if folder_id is missing or not numeric
        echo 'Invalid folder ID.';
    }
?>
