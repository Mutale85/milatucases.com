<?php
	include '../../includes/db.php';

	if (isset($_POST['lawFirmId'])) {
	    $lawFirmId = filter_input(INPUT_POST, 'lawFirmId', FILTER_SANITIZE_SPECIAL_CHARS);
	    $sql = $connect->prepare("SELECT * FROM `lawFirmClients` WHERE `lawFirmId` = ? AND archived = '0' ");
	    $sql->execute([$lawFirmId]);
	    $clients = $sql->fetchAll(PDO::FETCH_ASSOC);

	    $response = [];
	    foreach ($clients as $client) {
	    	$client['id'] = $client['id'];
	        $client['client_tpin'] = html_entity_decode(decrypt($client['client_tpin']));
	        $client['client_email'] = html_entity_decode(decrypt($client['client_email']));
	        $client['client_names'] = html_entity_decode(decrypt($client['client_names']));
	        $response[] = $client;
	    }

	    echo json_encode(['success' => true, 'clients' => $response]);
	} else {
	    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
	}
?>
