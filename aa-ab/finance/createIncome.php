<?php
    require '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['confirmation']) || $_POST['confirmation'] !== 'on') {
            exit('Please confirm that the income is correct.');
        }

        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
        $currency = filter_input(INPUT_POST, 'currency', FILTER_SANITIZE_SPECIAL_CHARS);
        $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
        $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_SPECIAL_CHARS);
        $income_date = date("Y-m-d", strtotime($date));
        $description = encrypt($description);

        if (!$description || !$amount || !$date) {
            exit('Invalid input data.');
        }

        // Check if an income with the same budget_id and date_added already exists
        if (empty($_POST['income_id'])) {
            $checkStmt = $connect->prepare("SELECT * FROM tableIncome WHERE lawFirmId = ? AND description = ? AND amount = ? AND date_added = ?");
            $checkStmt->execute([$_SESSION['parent_id'], $description, $amount, $income_date]);
            $existingIncome = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existingIncome) {
                exit('You posted an income with similar details');
            }

            // Insert the new income
            $stmt = $connect->prepare("INSERT INTO tableIncome (lawFirmId, description, currency, amount, userId, income_date) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['parent_id'], $description, $currency, $amount, $_SESSION['user_id'], $income_date]);

            echo "Income recorded successfully!";
        } else {
            $income_id = filter_input(INPUT_POST, 'income_id', FILTER_VALIDATE_INT);
            if (!$income_id) {
                exit('Invalid income ID.');
            }

            // Update the existing income
            $stmt = $connect->prepare("UPDATE tableIncome SET description = ?, currency = ?, amount = ?, income_date = ? WHERE id = ?");
            $stmt->execute([$description, $currency, $amount, $income_date, $income_id]);

            $action = "Income ID $income_id updated successfully!";
            insertAuditTrail($_SESSION['parent_id'], $_SESSION['user_id'], $action);
            echo $action;
        }
    }
?>
