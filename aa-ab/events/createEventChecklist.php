<?php
    include '../../includes/db.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitize input
        $item = filter_input(INPUT_POST, 'item', FILTER_SANITIZE_SPECIAL_CHARS);
        $event_id = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);

        if ($item !== false && $event_id !== false) {
            $stmt = $connect->prepare("INSERT INTO eventsChecklist (item, event_id) VALUES (?, ?)");
            $result = $stmt->execute([$item, $event_id]);

            if ($result) {
                echo 'CheckList Item added successfully.';
            } else {
                echo 'Failed to add item.';
            }
        } else {
            // Invalid input
            echo 'Invalid input.';
        }
    }
?>
