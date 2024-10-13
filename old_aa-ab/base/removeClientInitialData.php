<?php
    include '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $clientId = isset($_GET['clientId']) ? $_GET['clientId'] : null;
        $clientId = decrypt($_GET['clientId']);

        $sql = $connect->prepare("UPDATE lawFirmClients SET archived = 1 WHERE id = ? ");
        $sql->execute([$clientId]);

        echo "Client removed successfully.";
        
    }
?>
