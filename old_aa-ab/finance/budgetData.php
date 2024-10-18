<?php
    require '../../includes/db.php';

    // Fetch expenses data
    $stmt = $connect->prepare("SELECT budget_id, amount, DATE_FORMAT(date_added, '%Y-%m') as month FROM church_expenses WHERE churchId = ?");
    $stmt->execute([$_SESSION['parent_id']]);
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch budgets data
    $stmt = $connect->prepare("SELECT category, amount FROM church_budgets WHERE church_id = ?");
    $stmt->execute([$_SESSION['parent_id']]);
    $budgets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare data for the chart
    $expenseData = [];
    $categoryData = [];

    foreach ($expenses as $expense) {
        $month = $expense['month'];
        $amount = $expense['amount'];
        
        if (!isset($expenseData[$month])) {
            $expenseData[$month] = 0;
        }
        $expenseData[$month] += $amount;
    }

    foreach ($budgets as $budget) {
        $category = $budget['category'];
        $amount = $budget['amount'];
        
        if (!isset($categoryData[$category])) {
            $categoryData[$category] = 0;
        }
        $categoryData[$category] += $amount;
    }

    echo json_encode(['expenses' => $expenseData, 'categories' => $categoryData]);
?>
