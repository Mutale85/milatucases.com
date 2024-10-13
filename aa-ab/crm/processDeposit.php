<?php 
	include '../../includes/db.php';

	if($_SERVER['REQUEST_METHOD'] == "POST"){
	    try {
	        $connect->beginTransaction();

	        // Insert into deposited_funds table
	        $stmt = $connect->prepare("
	            INSERT INTO deposited_funds 
	            (clientId, lawFirmId, userId, caseId, currency, amount, date_deposited, description, posted_to_income) 
	            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
	        ");
	        $stmt->execute([
	            $_POST['clientId'],
	            $_POST['lawFirmId'],
	            $_POST['userId'],
	            $_POST['case_id'],
	            $_POST['currency'],
	            $_POST['amount'],
	            $_POST['date_deposited'],
	            encrypt($_POST['description']),
	            isset($_POST['postToExpense']) ? 1 : 0
	        ]);

	        // If checkbox is checked, also insert into tableIncome
	        if(isset($_POST['postToIncome']) && $_POST['postToIncome'] == 'on') {
	            $currency = $_POST['currency'];
	            $income_date = $_POST['date_deposited'];
	            $description = encrypt($_POST['description']);

	            $stmt = $connect->prepare("INSERT INTO tableIncome (lawFirmId, description, currency, amount, userId, income_date) VALUES (?, ?, ?, ?, ?, ?)");
	            $stmt->execute([$_POST['lawFirmId'], $description, $currency, $_POST['amount'], $_POST['userId'], $income_date]);
	        }

	        $connect->commit();
	        echo json_encode([
	            'status' => 'success',
	            'message' => 'Funds deposited successfully' . (isset($_POST['postToIncome']) && $_POST['postToIncome'] == 'on' ? ' and posted to income' : ''),
	            'amount' => $_POST['amount'],
	            'currency' => $_POST['currency']
	        ]);
	    } catch (Exception $e) {
	        $connect->rollBack();
	        echo json_encode([
	            'status' => 'error',
	            'message' => 'An error occurred: ' . $e->getMessage()
	        ]);
	    }
	}
?>