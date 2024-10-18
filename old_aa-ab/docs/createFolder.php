<?php
include '../../includes/db.php';
include '../../includes/conf.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $folderName = $_POST['folderName'];
    $lawFirmId = $_SESSION['parent_id'];
    $user_id = $_SESSION['user_id'];
    $folderId = $_POST['folderId'];

    if(empty($folderId)){

        $stmt = $connect->prepare("INSERT INTO lawFirmFolders (lawFirmId, uploaded_by, folder_name) VALUES (?, ?, ?)");
        $stmt->execute([$lawFirmId, $user_id, $folderName]);

        if ($stmt) {
            echo "Folder created successfully!";
        } else {
            echo "Error creating folder.";
        }
    }else{
        $update = $connect->prepare("UPDATE lawFirmFolders SET folder_name = ?, uploaded_by = ? WHERE id = ?");
        if($update->execute([$folderName, $user_id, $folderId])){
            echo "Folder Name Updated successfully";
        }
    }
}
?>
