<?php 
	include "../../includes/db.php";
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		$invoiceId = $_POST['invoiceId'];
		$invoiceNo = $_POST['invoiceNo']; 
		$postPayment = $_POST['postPayment'];

		$update = $connect->prepare("UPDATE invoices SET status = '1' WHERE id = ? ");
		$update->execute([$invoiceId]);

		if($postPayment == 'true'){
			$description = " Payment for Invoice Number: $invoiceNo ";
	        $currency = "ZMW";
	        $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
	        $income_date = date("Y-m-d");
	        $description = encrypt($description);
			$stmt = $connect->prepare("INSERT INTO tableIncome (lawFirmId, description, currency, amount, userId, income_date) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['parent_id'], $description, $currency, $amount, $_SESSION['user_id'], $income_date]);
            echo "Invoice payment and recorded in the income successfully!";

		}else{
			echo "Invoice payment updated";
		}
	}
?>