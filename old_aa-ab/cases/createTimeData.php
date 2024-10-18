<?php 
	include '../../includes/db.php';

	if($_SERVER['REQUEST_METHOD'] === "POST"){
		$userId = $_SESSION['user_id'];
		// $caseId = filter_input(INPUT_POST, 'caseDbId', FILTER_SANITIZE_SPECIAL_CHARS);
		$caseId = filter_input(INPUT_POST, 'caseId', FILTER_SANITIZE_SPECIAL_CHARS);
		$currency = filter_input(INPUT_POST, 'currency', FILTER_SANITIZE_SPECIAL_CHARS);
		$hourlyRate = filter_input(INPUT_POST, 'hourlyRate', FILTER_VALIDATE_FLOAT);
		$timeSpentSeconds = filter_input(INPUT_POST, 'timeSpent', FILTER_VALIDATE_INT);
		$taskDescription = htmlspecialchars(filter_input(INPUT_POST, 'taskDescription', FILTER_SANITIZE_SPECIAL_CHARS));
		$lawFirmId = filter_input(INPUT_POST, 'lawFirmId', FILTER_SANITIZE_SPECIAL_CHARS);
		$client_tpin = filter_input(INPUT_POST, 'clientId', FILTER_SANITIZE_SPECIAL_CHARS);
		$caseNo = getClientcaseNoByCaseId($caseId, $lawFirmId);
		$createdAt = date('Y-m-d H:i:s');
		if(!empty($hourlyRate)){
			$client_tpin = getClientTpinByCaseId($caseId, $lawFirmId);
			$clientId = getClientIdByCaseId($caseId, $lawFirmId);
			$hourlyRate = getClientHourlyRateByCaseId($caseId, $lawFirmId);
			$caseNo = getClientcaseNoByCaseId($caseId, $lawFirmId);
			$currency = getClientCurrencyByCaseId($caseId, $lawFirmId);
		}

		// Convert time spent from seconds to minutes
		$timeSpentMinutes = ceil($timeSpentSeconds / 60);

		// Calculate total amount and round it to the next highest value
		$totalAmount = ceil($hourlyRate * ($timeSpentMinutes / 60));

		$stmt = $connect->prepare("
	        INSERT INTO timer_logs (userId, lawFirmId, client_tpin, clientId, case_id, caseNo, currency, hourly_rate, time_spent, total_amount, task_description, created_at)
	        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
	    ");
	    
	    // Execute the statement with the array of values
	    $stmt->execute([$userId, $lawFirmId,$client_tpin, $clientId, $caseId,$caseNo,$currency,$hourlyRate,$timeSpentMinutes,$totalAmount,$taskDescription,$createdAt]);

	    echo "Timer / Fees data saved successfully.";
	}


	/*
		
	DELETE FROM `case_access` WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM caseDocuments WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM cases WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM case_milestones WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM case_status WHERE `lawFirmId` = 'GzTUMEJn';

	DELETE FROM company_info WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM corporatePart1 WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM corporatePart2 WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM corporatePart3 WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM corporatePart4 WHERE `lawFirmId` = 'GzTUMEJn';

	DELETE FROM disbursements WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM events WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM fee_notes WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM individualPart1 WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM individualPart2 WHERE `lawFirmId` = 'GzTUMEJn';

	DELETE FROM invoices WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM lawFirmClients WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM lawFirms WHERE `lawFirmId` = 'GzTUMEJn';

	DELETE FROM tableAudit WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM tableBudgets WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM tableExpenses WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM tableIncome WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM tablePettyCash WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM timer_logs WHERE `lawFirmId` = 'GzTUMEJn';
	DELETE FROM user_logins WHERE `lawFirmId` = 'GzTUMEJn';
	*/
?>
