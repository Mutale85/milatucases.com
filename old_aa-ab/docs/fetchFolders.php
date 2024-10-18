<?php
    include '../../includes/db.php';
    if(isset($_POST['getFolders'])){


        $stmt = $connect->prepare("SELECT * FROM lawFirmFolders WHERE lawFirmId = ?");
        $stmt->execute([$_SESSION['parent_id']]);
        $folders = $stmt->fetchAll(PDO::FETCH_ASSOC);
         echo "<option value=''>Select</option>";
        foreach ($folders as $folder) {
            echo "<option value='{$folder['id']}'>{$folder['folder_name']}</option>";
        }
    }
  ?>