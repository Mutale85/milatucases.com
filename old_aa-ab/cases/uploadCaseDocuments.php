<?php
include '../../includes/db.php';

if (isset($_POST['newcaseId']) && isset($_POST['newcaseNo']) && isset($_FILES['documents'])) {
    $caseId = $_POST['newcaseId'];
    $caseNo = $_POST['newcaseNo'];
    $documents = $_FILES['documents'];
    

    $uploadDir = '../caseDocuments/';
    foreach ($documents['name'] as $key => $name) {
        $tmpName = $documents['tmp_name'][$key];
        $fileName = basename($name);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $filePath)) {
            $stmt = $connect->prepare("INSERT INTO `caseDocuments` (`caseId`, `caseNo`, `documentName`, `userId`, `lawFirmId`) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$caseId, $caseNo, $fileName, $_SESSION['user_id'], $_SESSION['parent_id']]);
        }
    }
    echo "Documents uploaded successfully.";
}
?>
