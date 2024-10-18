<?php
    include '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $clientId = filter_input(INPUT_POST, 'client_id', FILTER_SANITIZE_SPECIAL_CHARS);
        $lawFirmId = filter_input(INPUT_POST, 'lawFirmId', FILTER_SANITIZE_SPECIAL_CHARS);
        $client_names = filter_input(INPUT_POST, 'client_names', FILTER_SANITIZE_SPECIAL_CHARS);
        $client_phone = filter_input(INPUT_POST, 'client_phone', FILTER_SANITIZE_SPECIAL_CHARS);
        $client_email = filter_input(INPUT_POST, 'client_email', FILTER_SANITIZE_SPECIAL_CHARS);
        $client_tpin = filter_input(INPUT_POST, 'client_tpin', FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($clientId)) {
            // Insert new client
            $sql = $connect->prepare("INSERT INTO lawFirmIndividualClients (lawFirmId, client_names, client_phone, client_email, client_tpin) VALUES (?, ?, ?, ?, ?)");
            $sql->execute([$lawFirmId, $client_names, $client_phone, $client_email, $client_tpin]);
            echo "Client information saved successfully.";
            // we will send an email to client 
        } else {
            // Update existing client
            $sql = $connect->prepare("UPDATE lawFirmIndividualClients SET client_names = ?, client_phone = ?, client_email = ?, client_tpin = ? WHERE id = ?");
            $sql->execute([$client_names, $client_phone, $client_email, $client_tpin, $clientId]);
            echo "Client information updated successfully.";
        }
    }
?>
