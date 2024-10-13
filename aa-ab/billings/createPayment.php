<?php
include '../../includes/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoiceAmount = filter_input(INPUT_POST, 'invoiceAmount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $invoiceId = filter_input(INPUT_POST, 'invoiceId', FILTER_SANITIZE_NUMBER_INT);
    $lawFirmId = filter_input(INPUT_POST, 'lawFirmId', FILTER_SANITIZE_NUMBER_INT);
    $clientId = filter_input(INPUT_POST, 'clientId', FILTER_SANITIZE_NUMBER_INT);
    $paymentDate = filter_input(INPUT_POST, 'payment-date', FILTER_SANITIZE_STRING);
    $paymentMethod = filter_input(INPUT_POST, 'payment-method', FILTER_SANITIZE_STRING);
    $paymentNote = filter_input(INPUT_POST, 'payment-note', FILTER_SANITIZE_STRING);
    if (!$invoiceAmount || !$invoiceId || !$lawFirmId || !$clientId || !$paymentDate || !$paymentMethod) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }
    try {
        $connect->beginTransaction();
        $currentBalance = fetchInvoiceBalance($invoiceId);
        
               
        if ($invoiceAmount > $currentBalance) {
            throw new Exception("Payment amount of: $invoiceAmount exceeds current balance of: $currentBalance");
        }
        
        $newBalance = $currentBalance - $invoiceAmount;
        $status = ($newBalance == 0) ? '1' : '2';
        
        $stmt = $connect->prepare("INSERT INTO invoice_payments (invoiceId, lawFirmId, clientId, userId, paid, balance, date_paid, paymentMethod, paymentNote) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $invoiceId,
            $lawFirmId,
            $clientId,
            $_SESSION['user_id'] ?? null,
            $invoiceAmount,
            $newBalance,
            $paymentDate,
            $paymentMethod,
            $paymentNote
        ]);
        
        $query = $connect->prepare("SELECT amountPaid, invoice_number FROM invoices WHERE id = ?");
        $query->execute([$invoiceId]);
        $row = $query->fetch();
        $amountPaid = $row['amountPaid'];
        $invoiceNo = $row['invoice_number'];
        $totalPaid = $amountPaid + $invoiceAmount;
        
        $update = $connect->prepare("UPDATE invoices SET amountPaid = ?, remainingBalance = ?, status = ? WHERE id = ?");
        $update->execute([$totalPaid, $newBalance, $status, $invoiceId]);
        
        $description = " Payment for Invoice Number: $invoiceNo ";
        $currency = "ZMW";
        $income_date = date("Y-m-d");
        $description = encrypt($description);
        
        $stmt = $connect->prepare("INSERT INTO tableIncome (lawFirmId, description, currency, amount, userId, income_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['parent_id'], $description, $currency, $invoiceAmount, $_SESSION['user_id'], $income_date]);
        
        $connect->commit();
        echo json_encode(['status' => 'success', 'message' => "Payment processed successfully"]);
    } catch (Exception $e) {
        $connect->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>