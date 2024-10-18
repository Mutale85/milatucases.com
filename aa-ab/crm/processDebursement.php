<?php 
	include '../../includes/db.php';
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
	    try {
	        $connect->beginTransaction();

	        // Insert into disbursed_funds table
	        $stmt = $connect->prepare("
	            INSERT INTO disbursed_funds 
	            (clientId, lawFirmId, userId, caseId, currency, amount, date_disbursed, description, posted_to_expense) 
	            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
	        ");
	        $stmt->execute([
	            $_POST['clientId'],
	            $_POST['lawFirmId'],
	            $_POST['userId'],
	            $_POST['caseId'], // This is the caseId in your form
	            $_POST['currency'],
	            $_POST['amount'],
	            $_POST['date_disbursed'],
	            encrypt($_POST['description']),
	            isset($_POST['postToExpense']) ? 1 : 0
	        ]);

	        // If checkbox is checked, also insert into tableExpenses
	        if (isset($_POST['postToExpense']) && $_POST['postToExpense'] == 'on') {
	            $description = $_POST['description'];
	            $description = encrypt($description);

	            $stmt = $connect->prepare("INSERT INTO tableExpenses (lawFirmId, description, currency, amount, userId, date_added) VALUES (?, ?, ?, ?, ?, ?)");
	            $stmt->execute([
	                $_POST['lawFirmId'],
	                $description,
	                $_POST['currency'],
	                $_POST['amount'],
	                $_POST['userId'],
	                $_POST['date_disbursed']
	            ]);
	        }

	        $connect->commit();
	        echo json_encode([
	            'status' => 'success',
	            'message' => 'Funds disbursed successfully' . (isset($_POST['postToExpense']) && $_POST['postToExpense'] == 'on' ? ' and posted to expenses' : ''),
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