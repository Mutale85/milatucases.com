<?php 
    include '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        // Assuming you have already started the session somewhere before this script
        $userId = $_SESSION['user_id'];
        $lawFirmId = $_SESSION['parent_id'];
        $createdAt = date('Y-m-d H:i:s');
        $updatedAt = $createdAt;

        // Sanitize and validate input data
        $clientType = filter_input(INPUT_POST, 'client_type', FILTER_SANITIZE_SPECIAL_CHARS);
        $clientId = filter_input(INPUT_POST, 'client_id', FILTER_SANITIZE_SPECIAL_CHARS);
        $lawFirmId = filter_input(INPUT_POST, 'lawFirmId', FILTER_SANITIZE_SPECIAL_CHARS);

        if ($clientType == 'Corporate') {
            $business_name = filter_input(INPUT_POST, 'business_entity_name', FILTER_SANITIZE_SPECIAL_CHARS);
            $client_names = filter_input(INPUT_POST, 'representative_name', FILTER_SANITIZE_SPECIAL_CHARS);
            $client_email = filter_input(INPUT_POST, 'representative_email', FILTER_VALIDATE_EMAIL);
            $client_tpin = filter_input(INPUT_POST, 'business_tpin', FILTER_SANITIZE_SPECIAL_CHARS);
            $client_phone = filter_input(INPUT_POST, 'representative_phone', FILTER_SANITIZE_SPECIAL_CHARS);
            $allowLogin = filter_input(INPUT_POST, 'allow_login', FILTER_VALIDATE_BOOLEAN);
            if($allowLogin == 'on'){
               $pass = passwordGenerate();
                $password = password_hash($pass, PASSWORD_DEFAULT);
                $allowLogin = 1;
            }else{
                $password = '';
                $allowLogin = 0;
            }

        } else {
            $business_name = '';
            $client_names = filter_input(INPUT_POST, 'client_names', FILTER_SANITIZE_SPECIAL_CHARS);
            $client_phone = filter_input(INPUT_POST, 'client_phone', FILTER_SANITIZE_SPECIAL_CHARS);
            $client_email = filter_input(INPUT_POST, 'client_email', FILTER_VALIDATE_EMAIL);
            $client_tpin = filter_input(INPUT_POST, 'client_tpin', FILTER_SANITIZE_SPECIAL_CHARS);
            $allowLogin = 0; // Set default value for allowLogin if individual client
            $password = ""; // Set a default password or hash it if needed
        }
        
        $client_names = encrypt($client_names);
        $client_email = encrypt($client_email);
        $client_phone = encrypt($client_phone);
        $client_tpin = encrypt($client_tpin);
        $clientId = filter_input(INPUT_POST, 'clientId', FILTER_SANITIZE_NUMBER_INT);
        /*
        if(!empty($clientId)){

        }else{

            // Prepare the SQL statement
            $sql = $connect->prepare("INSERT INTO lawFirmClients (lawFirmId, userId, client_type, business_name, allow_login, password, client_names,client_email, client_phone, client_tpin, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt = $sql->execute([$lawFirmId, $userId, $clientType, $business_name, $allowLogin, $password, $client_names, $client_email, $client_phone, $client_tpin, $createdAt, $updatedAt]);

            if ($stmt) {
                echo " $clientType Client added successfully. ";
            } else {
                echo "Error adding client.";
            }
        }
        */

        if (!empty($clientId)) {
            // Prepare the SQL statement for updating existing client
            $sql = $connect->prepare("UPDATE lawFirmClients SET
                lawFirmId = ?,
                userId = ?,
                client_type = ?,
                business_name = ?,
                allow_login = ?,
                password = ?,
                client_names = ?,
                client_email = ?,
                client_phone = ?,
                client_tpin = ?,
                updated_at = ?
                WHERE id = ?");
        
            $stmt = $sql->execute([
                $lawFirmId, 
                $userId, 
                $clientType, 
                $business_name, 
                $allowLogin, 
                $password, 
                $client_names, 
                $client_email, 
                $client_phone, 
                $client_tpin, 
                $updatedAt, 
                $clientId // Assuming `id` is the primary key for the client
            ]);
        
            if ($stmt) {
                echo "Client updated successfully.";
            } else {
                echo "Error updating client.";
            }
        } else {
            // Prepare the SQL statement for inserting a new client
            $sql = $connect->prepare("INSERT INTO lawFirmClients (
                lawFirmId, 
                userId, 
                client_type, 
                business_name, 
                allow_login, 
                password, 
                client_names, 
                client_email, 
                client_phone, 
                client_tpin, 
                created_at, 
                updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
            $stmt = $sql->execute([
                $lawFirmId, 
                $userId, 
                $clientType, 
                $business_name, 
                $allowLogin, 
                $password, 
                $client_names, 
                $client_email, 
                $client_phone, 
                $client_tpin, 
                $createdAt, 
                $updatedAt
            ]);
        
            if ($stmt) {
                echo "$clientType Client added successfully.";
            } else {
                echo "Error adding client.";
            }
        }
        
    }
?>
