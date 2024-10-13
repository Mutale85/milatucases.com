<?php
    require '../../includes/db.php';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $incomeId = filter_input(INPUT_POST, 'incomeId', FILTER_SANITIZE_SPECIAL_CHARS);
        $stmt = $connect->prepare("SELECT * FROM tableIncome WHERE id = ?");
        $stmt->execute([$incomeId]);
        $income = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($income) {
            // Decrypt the description
            $income['description'] = decrypt($income['description']);
            
            echo json_encode($income);
        } else {
            echo json_encode(['error' => 'Income not found']);
        }
    }
?>
