<?php
    require '../../includes/db.php';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $date = date("Y-m-d", strtotime($_POST['date']));
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
        $amount = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_SPECIAL_CHARS);
        $transactionType = filter_input(INPUT_POST, 'transaction_type', FILTER_SANITIZE_SPECIAL_CHARS);
        $paymentMode = filter_input(INPUT_POST, 'payment_mode', FILTER_SANITIZE_SPECIAL_CHARS);
        $currency = filter_input(INPUT_POST, 'currency', FILTER_SANITIZE_SPECIAL_CHARS);
        $description = encrypt($description);

        if ($transactionType == 'Cash In') {
            $balance = getBalance();
            $total_cash =  $balance + $amount;
            
            $credit = $amount;
            $debit = 0.00;

            $sqlTransact = $connect->prepare("INSERT INTO `tablePettyCash`(`lawFirmId`, `userId`, `date`, `description`, `currency`, `amount`, `transaction_type`, `debit`, `credit`, `balance`, `payment_mode`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ");
            $sqlTransact->execute([$_SESSION['parent_id'], $_SESSION['user_id'], $date, $description, $currency, $amount, $transactionType, $debit, $credit, $total_cash, $paymentMode]);
            
            echo "$currency $amount Posted In";

        } elseif ($transactionType == 'Cash Out') {
            $balance = getBalance();
            $total_cash =  $balance - $amount;
            $credit = 0.00;
            $debit = $amount;
            $sql = $connect->prepare("INSERT INTO `tablePettyCash`(`lawFirmId`, `userId`, `date`, `description`, `currency`, `amount`, `transaction_type`, `debit`, `credit`, `balance`, `payment_mode`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ");
            $sql->execute([$_SESSION['parent_id'], $_SESSION['user_id'], $date, $description, $currency, $amount, $transactionType, $debit, $credit, $total_cash, $paymentMode]);
            
            echo " $currency $amount Posted out";

        }

    }

