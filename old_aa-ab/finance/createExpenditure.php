<?php
require '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['confirmation']) || $_POST['confirmation'] !== 'on') {
        exit('Please confirm that the expense is correct.');
    }

    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
    $currency = filter_input(INPUT_POST, 'currency', FILTER_SANITIZE_SPECIAL_CHARS);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_SPECIAL_CHARS);
    $date_added = date("Y-m-d", strtotime($date));

    if (!$description || !$amount || !$date) {
        exit('Invalid input data.');
    }

    $description = encrypt($description);

    if (empty($_POST['expense_id'])) {
        $checkStmt = $connect->prepare("SELECT * FROM tableExpenses WHERE lawFirmId = ? AND description = ? AND  amount = ? AND date_added = ? ");
        $checkStmt->execute([$_SESSION['parent_id'], $description, $amount, $date_added]);
        $existingExpense = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existingExpense) {
            exit('An expense for with similar details and date already exists. Please update the existing expense if you want to make changes.');
        }

        $stmt = $connect->prepare("INSERT INTO tableExpenses (lawFirmId, description, currency, amount, userId, date_added) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['parent_id'], $description, $currency, $amount, $_SESSION['user_id'], $date_added]);

        echo "Expense recorded successfully!";
    } else {
        $expense_id = filter_input(INPUT_POST, 'expense_id', FILTER_VALIDATE_INT);
        if (!$expense_id) {
            exit('Invalid expense ID.');
        }

        $stmt = $connect->prepare("UPDATE tableExpenses SET  description = ?, currency = ?, amount = ?, date_added = ? WHERE id = ?");
        $stmt->execute([$description, $currency, $amount, $date_added, $expense_id]);

        $action = "Expense ID $expense_id updated successfully!";
        insertAuditTrail($_SESSION['parent_id'], $_SESSION['user_id'], $action);
        echo $action;
    }
}

?>
