<?php
    include '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $clientId = filter_input(INPUT_POST,'client_id', FILTER_SANITIZE_SPECIAL_CHARS);
        $lawFirmId = filter_input(INPUT_POST,'lawFirmId', FILTER_SANITIZE_SPECIAL_CHARS);
        $business_entity_name = filter_input(INPUT_POST,'business_entity_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $representative_name = filter_input(INPUT_POST,'representative_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $representative_email = filter_input(INPUT_POST,'representative_email', FILTER_SANITIZE_SPECIAL_CHARS);
        $business_tpin = filter_input(INPUT_POST,'business_tpin', FILTER_SANITIZE_SPECIAL_CHARS);
        $representative_phone = filter_input(INPUT_POST,'representative_phone', FILTER_SANITIZE_SPECIAL_CHARS);
        

        if (empty($clientId)) {
            // Insert new client
            $sql = $connect->prepare("INSERT INTO lawFirmCorporateClients (lawFirmId, business_entity_name, representative_name, representative_email, business_tpin, representative_phone, userId) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $sql->execute([$lawFirmId, $business_entity_name, $representative_name, $representative_email, $business_tpin, $representative_phone, $_SESSION['user_id']]);
            echo "Client information saved successfully.";
            // we will send an email to client 
        } else {
            // Update existing client
            $sql = $connect->prepare("UPDATE lawFirmCorporateClients SET business_entity_name = ?, representative_name = ?, representative_email = ?, business_tpin = ?, representative_phone = ?, userId = ? WHERE id = ?");
            $sql->execute([$business_entity_name, $representative_name, $representative_email, $business_tpin, $representative_phone, $_SESSION['user_id'], $clientId]);
            echo "Client information updated successfully.";
        }
    }
?>
