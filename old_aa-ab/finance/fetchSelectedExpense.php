<?php
    require '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $expenseId = filter_input(INPUT_POST, 'expenseId', FILTER_SANITIZE_SPECIAL_CHARS);
        $stmt = $connect->prepare("SELECT * FROM tableExpenses WHERE id = ?");
        $stmt->execute([$expenseId]);
        $expense = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($expense) {
            // Decrypt the description
            $expense['description'] = decrypt($expense['description']);
            
            echo json_encode($expense);
        } else {
            echo json_encode(['error' => 'Income not found']);
        }
    }
?>
