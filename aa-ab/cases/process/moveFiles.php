<?php
    include '../../../includes/db.php';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    function moveFile($oldPath, $newPath) {
        return rename($oldPath, $newPath);
    }

    if (isset($_POST['folderId']) && isset($_POST['files']) && isset($_POST['caseId'])) {
        $folderId = $_POST['folderId'];
        $files = json_decode($_POST['files'], true);
        $caseId = $_POST['caseId'];
        $userId = $_SESSION['user_id'];
        $lawFirmId = $_SESSION['parent_id'];

        if (!is_array($files)) {
            echo "Invalid file data received";
            exit;
        }

        try {
            
            $newFolderName = fetchCaseFolderNameById($folderId);

            if (!$newFolderName) {
                throw new Exception("Invalid folder ID");
            }

            $stmtUpdate = $connect->prepare("UPDATE caseDocuments SET folderId = ? WHERE id = ? AND caseId = ?");
            $movedFiles = [];
            $errors = [];

            foreach ($files as $fileId) {                    
                $stmtFile = $connect->prepare("SELECT documentName FROM caseDocuments WHERE id = ? AND caseId = ?");
                $stmtFile->execute([$fileId, $caseId]);
                $fileInfo = $stmtFile->fetch(PDO::FETCH_ASSOC);

                if ($fileInfo) {
                    $oldFolderName = "caseDocuments";
                    $oldPath = "../../$oldFolderName/" . $fileInfo['documentName'];
                    $newPath = "../$newFolderName/" . $fileInfo['documentName'];

                    if (file_exists($oldPath)) {
                        if (moveFile($oldPath, $newPath)) {
                            $stmtUpdate->execute([$folderId, $fileId, $caseId]);
                            $movedFiles[] = $fileInfo['documentName'];
                        } else {
                            $errors[] = "Failed to move file: " . $fileInfo['documentName'];
                        }
                    } else {
                        $errors[] = "File not found: " . $fileInfo['documentName'];
                    }
                } else {
                    $errors[] = "File ID $fileId not found in the database";
                }
            }

            if (!empty($movedFiles)) {
                echo "Files moved successfully: " . implode(", ", $movedFiles);
                if (!empty($errors)) {
                    echo "\nSome issues occurred: " . implode(", ", $errors);
                }
            } else {
                echo "No files were moved. Errors: " . implode(", ", $errors);
            }

        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Missing required parameters";
    }
?>