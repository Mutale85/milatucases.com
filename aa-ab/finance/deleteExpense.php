<?php
    require '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $expenseId = filter_input(INPUT_POST, 'expenseId', FILTER_SANITIZE_NUMBER_INT);

        $stmt = $connect->prepare("DELETE FROM tableExpenses WHERE id = ?");
        $stmt->execute([$expenseId]);

        $action = "Expense deleted successfully!";
        insertAuditTrail($_SESSION['parent_id'], $_SESSION['user_id'], $action);
        echo $action;
    }
?>
