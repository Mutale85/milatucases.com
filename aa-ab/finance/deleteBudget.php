<?php
require '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $budgetId = filter_input(INPUT_POST, 'budget_id', FILTER_SANITIZE_SPECIAL_CHARS);

    $stmt = $connect->prepare("DELETE FROM church_budgets WHERE id = ?");
    $stmt->execute([$budgetId]);

    echo 'Budget deleted successfully!';
}
?>
