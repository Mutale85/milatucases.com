<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['docId'])) {
        $docId = $_POST['docId'];
        $parentId = $_SESSION['parent_id'] ?? null;
        
        if (!$parentId) {
            echo json_encode(['success' => false, 'message' => 'User is not authenticated']);
            exit;
        }
        
        try {
            $sql = $connect->prepare("DELETE FROM generated_documents WHERE id = ? AND parent_id = ?");
            if ($sql->execute([$docId, $parentId])) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete document']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
?>