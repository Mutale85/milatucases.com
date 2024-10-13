<?php
    include '../../../includes/db.php';
    if(isset($_POST['caseId'])){
        $caseId = $_POST['caseId'];
        $lawFirmId = $_POST['lawFirmId'];

        $stmt = $connect->prepare("SELECT * FROM caseFolders WHERE caseId = ? AND lawFirmId = ?");
        $stmt->execute([$caseId, $lawFirmId]);
        $folders = $stmt->fetchAll(PDO::FETCH_ASSOC);
         echo "<option value=''>Select</option>";
        foreach ($folders as $row) {
            $folderName = fetchCaseFolderNameById($row['id']);
            echo "<option value='{$row['id']}'>{$folderName}</option>";
        }
    }
  ?>