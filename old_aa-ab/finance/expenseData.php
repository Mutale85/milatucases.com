<?php


require '../../includes/db.php';

function expenseGraphs($connect) {
    $stmt = $connect->prepare("
        SELECT 
            DATE_FORMAT(date_added, '%b-%Y') as month,
            SUM(amount) as total 
        FROM 
            tableExpenses 
        WHERE 
            lawFirmId = ? 
        GROUP BY 
            DATE_FORMAT(date_added, '%Y-%m')
        ORDER BY 
            DATE_FORMAT(date_added, '%Y-%m')
    ");
    $stmt->execute([$_SESSION['parent_id']]);

    // Fetch the data
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Initialize an array with all months from Jan to Dec
    $months = [
        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
    ];
    $monthlyExpenses = array_fill_keys($months, 0);

    // Process the fetched data
    foreach ($expenses as $expense) {
        $month = substr($expense['month'], 0, 3);
        $monthlyExpenses[$month] = $expense['total'];
    }

    return [
        'monthlyExpenses' => $monthlyExpenses
    ];
}

// Usage example
header('Content-Type: application/json');
echo json_encode(expenseGraphs($connect));
?>

