<?php 
	include "../../includes/db.php";
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		$invoiceId = $_POST['invoiceId'];
		$invoiceNo = $_POST['invoiceNo']; 
		$postPayment = $_POST['postPayment'];
		$amountPaid = $_POST['amountPaid'];
		$remainingBalance = $_POST['remainingBalance'];
		
		if($remainingBalance <= 0){
			// exit("check the amount you wish to post");
			$update = $connect->prepare("UPDATE invoices SET status = '1', amountPaid = ?, remainingBalance = ?  WHERE id = ? ");
			$update->execute([$amountPaid, $remainingBalance, $invoiceId]);

			if($postPayment == 'true'){
				$description = " Payment for Invoice Number: $invoiceNo ";
		        $currency = userMainCurrency();
		        $income_date = date("Y-m-d");
		        $description = encrypt($description);
				$stmt = $connect->prepare("INSERT INTO tableIncome (lawFirmId, description, currency, amount, userId, income_date) VALUES (?, ?, ?, ?, ?, ?)");
	            $stmt->execute([$_SESSION['parent_id'], $description, $currency, $amountPaid, $_SESSION['user_id'], $income_date]);
	            echo "full invoice payment recorded and posted in the income table successfully!";

			}else{
				echo "full invoice payment recorded";
			}
		}else{
			$update = $connect->prepare("UPDATE invoices SET amountPaid = ?, remainingBalance = ?  WHERE id = ? ");
			$update->execute([$amountPaid, $remainingBalance, $invoiceId]);

			if($postPayment == 'true'){
				$description = " Payment for Invoice Number: $invoiceNo ";
		        $currency = "";
		        // $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
		        $income_date = date("Y-m-d");
		        $description = encrypt($description);
				$stmt = $connect->prepare("INSERT INTO tableIncome (lawFirmId, description, currency, amount, userId, income_date) VALUES (?, ?, ?, ?, ?, ?)");
	            $stmt->execute([$_SESSION['parent_id'], $description, $currency, $amountPaid, $_SESSION['user_id'], $income_date]);
	            echo "full invoice payment recorded and posted in the income table successfully!";

			}else{
				echo "full invoice payment recorded";
			}
		}
	}
?>