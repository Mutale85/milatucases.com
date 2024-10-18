<?php
    require '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $incomeId = filter_input(INPUT_POST, 'incomeId', FILTER_SANITIZE_NUMBER_INT);

        $stmt = $connect->prepare("DELETE FROM tableIncome WHERE id = ?");
        $stmt->execute([$incomeId]);

        $action = "Income deleted successfully!";
        insertAuditTrail($_SESSION['parent_id'], $_SESSION['user_id'], $action);
        echo $action;
    }
?>
