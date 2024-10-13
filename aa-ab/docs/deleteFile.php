<?php
    include '../../includes/db.php';
    include '../../includes/conf.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $file_name = $_POST['file_name'];
        $lawFirmId = $_SESSION['parent_id'];

        // Delete the file from the server
        $filePath = "../uploads/files/" . $file_name;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete the file information from the database
        $stmt = $connect->prepare("DELETE FROM lawFirmFiles WHERE lawFirmId = ? AND file_name = ?");
        $stmt->execute([$lawFirmId, $file_name]);

        if ($stmt) {
            echo "File deleted successfully.";
        } else {
            echo "Error deleting file from the database.";
        }
    }
?>
