<?php
    include '../../includes/db.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = $_POST['id'];
        

        // Validate input
        if (!filter_var($id, FILTER_VALIDATE_INT) ) {
            echo 'Invalid input';
            exit;
        }

        // Update status in the database
        $stmt = $connect->prepare("DELETE FROM `eventsChecklist` WHERE `id` = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            echo 'Item Deleted';
        } else {
            echo 'Failed';
        }
    }
?>
