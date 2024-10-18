<?php

require '../../includes/db.php';

function incomeGraphs($connect) {
    // Prepare and execute the query
    $stmt = $connect->prepare("
        SELECT 
            DATE_FORMAT(income_date, '%b-%Y') as month,
            SUM(amount) as total 
        FROM 
            tableIncome 
        WHERE 
            lawFirmId = ? 
        GROUP BY 
            DATE_FORMAT(income_date, '%Y-%m')
        ORDER BY 
            DATE_FORMAT(income_date, '%Y-%m')
    ");
    $stmt->execute([$_SESSION['parent_id']]);

    // Fetch the data
    $incomes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize an array with all months from Jan to Dec
    $months = [
        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
    ];
    $monthlyIncomes = array_fill_keys($months, 0);

    // Process the fetched data
    foreach ($incomes as $income) {
        $month = substr($income['month'], 0, 3);
        $monthlyIncomes[$month] = $income['total'];
    }

    // Return the data in the expected structure
    return [
        'monthlyIncomes' => $monthlyIncomes
    ];
}

    // Usage example
    header('Content-Type: application/json');
    echo json_encode(incomeGraphs($connect));
?>
