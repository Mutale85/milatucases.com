<?php
    include '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['user_role'] == 'superAdmin') {
        $memberId = filter_input(INPUT_POST, 'memberId', FILTER_SANITIZE_NUMBER_INT);
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

        try {
            $newStatus = ($action == 'grant') ? 1 : 0;
            $stmt = $connect->prepare("UPDATE lawFirms SET login_auth = :status WHERE id = :id");
            $result = $stmt->execute([
                'status' => $newStatus,
                'id' => $memberId
            ]);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => $action == 'grant' ? 'Access granted successfully' : 'Member suspended successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update member status'
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request or insufficient permissions'
        ]);
    }
?>