<?php
    include '../../includes/db.php';
    $data = json_decode(file_get_contents('php://input'), true);

    $folderId = $data['folderId'];
    $files = $data['files'];

    foreach ($files as $file) {
        $stmt = $connect->prepare("UPDATE lawFirmFiles SET folder_id = ? WHERE file_name = ? AND lawFirmId = ?");
        $stmt->execute([$folderId, $file, $_SESSION['parent_id']]);
    }

    echo "Files Moved Successfully";
?>
