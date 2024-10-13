<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    error_log("Delete time entry script started");

    require_once '../../includes/db.php';
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $timeEntryId = $_POST['id'];
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];
        
        $isSuperAdmin = ($userRole === 'superAdmin');
        if ($isSuperAdmin) {
            $query = $connect->prepare("DELETE FROM time_entries WHERE id = ?");
            $params = [$timeEntryId];
        } else {
            $query = $connect->prepare("DELETE FROM time_entries WHERE id = ? AND userId = ?");
            $params = [$timeEntryId, $userId];
        }
        
        try {
            if ($query->execute($params)) {
                $rowCount = $query->rowCount();
                if ($rowCount > 0) {
                    error_log("Successfully deleted time entry");
                    echo json_encode(['success' => true, 'message' => 'Timer entry deleted successfully']);
                } else {
                    error_log("No matching time entry found or user does not have permission");
                    echo json_encode(['success' => false, 'message' => 'No matching time entry found or you do not have permission to delete this entry']);
                }
            } else {
                error_log("Failed to execute delete query");
                echo json_encode(['success' => false, 'message' => 'Failed to delete time entry']);
            }
        } catch (PDOException $e) {
            error_log("PDO Exception: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error occurred']);
        }
    }
    error_log("Delete time entry script completed");
?>