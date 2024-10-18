<?php
    // Include database connection file
    include "../../includes/db.php";

    // Law firm ID (replace with actual lawFirmId)
    $lawFirmId = $_SESSION['parent_id'];

    // Query to retrieve incomes
    $sqlIncome = "SELECT `id`, `description`, `currency`, `amount`, `income_date`
                  FROM `tableIncome`
                  WHERE `lawFirmId` = :lawFirmId";

    // Query to retrieve expenses
    $sqlExpenses = "SELECT `id`, `description`, `currency`, `amount`, `date_added`
                   FROM `tableExpenses`
                   WHERE `lawFirmId` = :lawFirmId";

    // Prepare statements
    $stmtIncome = $connect->prepare($sqlIncome);
    $stmtExpenses = $connect->prepare($sqlExpenses);

    // Bind parameters
    $stmtIncome->bindParam(':lawFirmId', $lawFirmId, PDO::PARAM_INT);
    $stmtExpenses->bindParam(':lawFirmId', $lawFirmId, PDO::PARAM_INT);

    // Execute queries
    $stmtIncome->execute();
    $stmtExpenses->execute();

    // Fetch all results into arrays
    $incomes = $stmtIncome->fetchAll(PDO::FETCH_ASSOC);
    $expenses = $stmtExpenses->fetchAll(PDO::FETCH_ASSOC);

    // Initialize arrays to hold monthly totals for incomes and expenses separately
    $monthlyIncomes = array_fill(1, 12, 0); // Index 1 to 12 for months
    $monthlyExpenses = array_fill(1, 12, 0);

    // Process income data
    foreach ($incomes as $income) {
        $month = date('n', strtotime($income['income_date']));
        $monthlyIncomes[$month] += (float) $income['amount'];
    }

    // Process expenses data
    foreach ($expenses as $expense) {
        $month = date('n', strtotime($expense['date_added']));
        $monthlyExpenses[$month] += (float) $expense['amount'];
    }

    // Prepare data for JSON response
    $response = array(
        'months' => array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'),
        'monthlyIncomes' => array_values($monthlyIncomes),
        'monthlyExpenses' => array_values($monthlyExpenses),
    );

    // Output JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
?>
