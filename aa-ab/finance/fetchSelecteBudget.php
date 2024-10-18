<?php
    require '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $budgetId = filter_input(INPUT_POST, 'budget_id', FILTER_SANITIZE_SPECIAL_CHARS);

        $stmt = $connect->prepare("SELECT * FROM church_budgets WHERE id = ?");
        $stmt->execute([$budgetId]);
        $budget = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($budget) {
            echo json_encode($budget);
        } else {
            echo json_encode(['error' => 'Budget not found']);
        }
    }
?>
