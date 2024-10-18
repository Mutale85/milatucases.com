<?php
    include '../../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $file_name = $_POST['file_name'];
        $lawFirmId = $_SESSION['parent_id'];
        $folderId = $_POST['folder_id'];
        $folderName = fetchCaseFolderNameById($folderId);
        $documentId = $_POST['documentId'];

        $filePath = "../$folderName/" . $file_name;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $stmt = $connect->prepare("DELETE FROM caseDocuments WHERE id = ? AND lawFirmId = ? AND folderId = ? ");
        $stmt->execute([$documentId, $lawFirmId, $folderId]);

        if ($stmt) {
            echo "File deleted successfully.";
        } else {
            echo "Error deleting file from the database.";
        }
    }
?>
