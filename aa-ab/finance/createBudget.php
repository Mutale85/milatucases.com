<?php
    require '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['confirmation']) || $_POST['confirmation'] !== 'on') {
            echo 'Please confirm that the budget information is true.';
            exit;
        }

        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS);
        $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_SPECIAL_CHARS);
        $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_SPECIAL_CHARS);
        $church_id = filter_input(INPUT_POST, 'church_id', FILTER_SANITIZE_SPECIAL_CHARS);
        $year = filter_input(INPUT_POST, 'year', FILTER_SANITIZE_SPECIAL_CHARS);
        $budget_id = filter_input(INPUT_POST, 'budget_id', FILTER_SANITIZE_SPECIAL_CHARS);

        // $checkStmt = $connect->prepare("SELECT * FROM church_budgets WHERE church_id = ? AND category = ? AND year = ?");
        // $checkStmt->execute([$church_id, $category, $year]);
        // $existingBudget = $checkStmt->fetch(PDO::FETCH_ASSOC);

        // if ($existingBudget) {
        //     echo 'A budget for this category and year already exists. Please update the existing budget if you want to make changes.';
        //     exit();
        // }

        if (empty($budget_id)) {
            // Insert new budget
            $stmt = $connect->prepare("INSERT INTO church_budgets (church_id, user_id, category, amount, year) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$church_id, $user_id, $category, $amount, $year]);
            echo "Budget created successfully!";
        } else {
            // Update existing budget
            $stmt = $connect->prepare("UPDATE church_budgets SET category = ?, amount = ?, year = ? WHERE id = ?");
            $stmt->execute([$category, $amount, $year, $budget_id]);
            echo "Budget updated successfully!";
        }
    }

?>
