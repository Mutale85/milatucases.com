<?php

    include '../../includes/db.php';

    if (isset($_POST['id'])) {

        $clientId = $_POST['id'];

        $sql = $connect->prepare("SELECT * FROM lawFirmClients WHERE id = ?");
        $sql->execute([$clientId]);
        $client = $sql->fetch(PDO::FETCH_ASSOC);

        if ($client) {
            // Decrypt sensitive fields
            $client['client_email'] = html_entity_decode(decrypt($client['client_email']));
            $client['client_names'] = html_entity_decode(decrypt($client['client_names']));
            $client['client_phone'] = html_entity_decode(decrypt($client['client_phone']));
            $client['client_tpin'] = html_entity_decode(decrypt($client['client_tpin']));
            if($client['client_type'] == 'Corporate' AND $client['incorporation_number'] != "" ){
                $client['incorporation_number'] = html_entity_decode(decrypt($client['incorporation_number']));
            }else if($client['client_type'] == 'Individual' AND $client['nrc_passport_number'] != "" ){
                $client['nrc_passport_number'] = html_entity_decode(decrypt($client['nrc_passport_number']));
            }else{

            }
            if($client['address'] == ""){
                $client['address'] = $client['address'];
            }else{
                $client['address'] = html_entity_decode(decrypt($client['address']));
            }
            
            
            // Determine client type
            if (isset($client['business_name']) && !empty($client['business_name'])) {
                $client['client_type'] = 'Corporate';
                $client['business_name'] = $client['business_name'];
                $client['allow_login'] = $client['allow_login'];
            } else {
                $client['client_type'] = 'Individual';
            }
        }

        echo json_encode($client);
    }
?>
