<?php
include '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $deleteData);
    $clientId = $deleteData['id'];

    $sql = $connect->prepare("DELETE FROM lawFirmCorporateClients WHERE id = ? ");
    $sql->execute([$clientId]);

    echo "Client removed successfully.";
}
?>
