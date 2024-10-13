<?php
    include '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_SPECIAL_CHARS);
        if ($userId) {
            try {
                $stmt = $connect->prepare("SELECT * FROM lawFirms WHERE id = ?");
                $stmt->execute([$userId]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($data) {
                    echo json_encode(['success' => true, 'data' => $data]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No data found.']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid law firm ID.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    }
?>
