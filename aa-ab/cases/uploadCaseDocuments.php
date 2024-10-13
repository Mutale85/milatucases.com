<?php
    
    include '../../includes/db.php';

    if (isset($_POST['newcaseId']) && isset($_POST['newcaseNo']) && isset($_FILES['documents'])) {
        $caseId = $_POST['newcaseId'];
        $caseNo = $_POST['newcaseNo'];
        $documents = $_FILES['documents'];
        if(!empty($_POST['folder'])){
           $folderId = $_POST['folder'];
           $folder = fetchCaseFolderNameById($folderId);
           $uploadDir = "$folder/";
        }else{
            $folderId = null;
            $uploadDir = '../caseDocuments/';
        }
        
        foreach ($documents['name'] as $key => $name) {
            $tmpName = $documents['tmp_name'][$key];
            $fileName = basename($name);
            $filePath = $uploadDir . $fileName;
            $dateAdded = date('Y-m-d H:i:s'); // Get the current date and time

            if (move_uploaded_file($tmpName, $filePath)) {
                $stmt = $connect->prepare("INSERT INTO `caseDocuments` (`caseId`, `caseNo`, `documentName`, `date_added`, `userId`, `lawFirmId`, `folderId`) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$caseId, $caseNo, $fileName, $dateAdded, $_SESSION['user_id'], $_SESSION['parent_id'], $folderId]);
            }
        }
        echo "Documents uploaded successfully.";
    }
?>
