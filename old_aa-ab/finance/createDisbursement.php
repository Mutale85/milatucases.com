<?php
    include '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $clientId = filter_input(INPUT_POST, 'clientId', FILTER_SANITIZE_SPECIAL_CHARS);
        $client_tpin = filter_input(INPUT_POST, 'client_tpin', FILTER_SANITIZE_SPECIAL_CHARS);
        $clientEmail = filter_input(INPUT_POST, 'clientEmail', FILTER_SANITIZE_SPECIAL_CHARS);
        $disbursement_date = filter_input(INPUT_POST, 'disbursement_date', FILTER_SANITIZE_SPECIAL_CHARS);
        $disbursementTotal = filter_input(INPUT_POST, 'disbursementTotal', FILTER_SANITIZE_SPECIAL_CHARS);
        $lawFirmId = filter_input(INPUT_POST, 'lawFirmId', FILTER_SANITIZE_SPECIAL_CHARS);
        $items = $_POST['items'];

        // Insert into invoices table
        $stmt = $connect->prepare("INSERT INTO disbursements (lawFirmId, clientId, client_tpin, clientEmail, disbursement_date, total) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$lawFirmId, $clientId, $client_tpin, $clientEmail, $disbursement_date, $disbursementTotal]);
        $disbursement_id = $connect->lastInsertId();

        // Insert each item into invoice_items table
        foreach ($items as $item) {
            $description = filter_var($item['description'], FILTER_SANITIZE_SPECIAL_CHARS);
            $quantity = filter_var($item['quantity'], FILTER_SANITIZE_SPECIAL_CHARS);
            $price = filter_var($item['price'], FILTER_SANITIZE_SPECIAL_CHARS);
            $item_total = filter_var($item['total'], FILTER_SANITIZE_SPECIAL_CHARS);
            $stmt = $connect->prepare("INSERT INTO disbursement_items (disbursement_id, description, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$disbursement_id, $description, $quantity, $price, $item_total]);
        }

        echo "Disbursement Successfully posted";
    }
?>