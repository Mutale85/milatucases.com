<?php
    include '../../includes/db.php';

    if (isset($_POST['folder_id'])) {
        $folder_id = $_POST['folder_id'];

        try {
            $stmt = $connect->prepare("SELECT * FROM lawFirmFolders WHERE id = ? AND lawFirmId = ?");
            $stmt->execute([$folder_id, $_SESSION['parent_id']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                echo json_encode($row);
            } else {
                // Handle error: folder not found
                echo json_encode(['error' => 'Folder not found']);
            }
        } catch (PDOException $e) {
            // Handle database error
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
