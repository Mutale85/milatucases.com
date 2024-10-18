<?php
include '../../includes/db.php';

if (isset($_POST['docId'])) {
    $docId = $_POST['docId'];

    // Fetch the document name to delete the file
    $stmt = $connect->prepare("SELECT `documentName` FROM `caseDocuments` WHERE `id` = ?");
    $stmt->execute([$docId]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($document) {
        $filePath = '../caseDocuments/' . $document['documentName'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete the document record from the database
        $stmt = $connect->prepare("DELETE FROM `caseDocuments` WHERE `id` = ?");
        $stmt->execute([$docId]);
        echo "Document removed successfully.";
    }
}
?>
