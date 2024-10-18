<?php
    include '../../includes/db.php';

    if (isset($_POST['id'])) {
        $clientId = $_POST['id'];
        $sql = $connect->prepare("SELECT * FROM lawFirmClients WHERE id = ?");
        $sql->execute([$clientId]);
        $client = $sql->fetch(PDO::FETCH_ASSOC);

        if ($client) {
            $client['business_name'] = $client['business_name'];
            $client['allow_login']  = $client['allow_login'];
            $client['client_email'] = html_entity_decode(decrypt($client['client_email']));
            $client['client_names'] = html_entity_decode(decrypt($client['client_names']));
            $client['client_phone'] = html_entity_decode(decrypt($client['client_phone']));
            $client['client_tpin'] = html_entity_decode(decrypt($client['client_tpin']));
        }

        echo json_encode($client);
    }
?>
