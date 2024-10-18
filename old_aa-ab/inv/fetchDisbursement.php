<?php
    require '../../includes/db.php';

    if (isset($_POST['clientId'])) {
        $clientId = $_POST['clientId'];
        // Fetch disbursement data
        $query = $connect->prepare("SELECT total FROM disbursements WHERE clientId = ? AND status = 0 LIMIT 1");
        $query->execute([$clientId]);
        $disbursement = $query->fetch(PDO::FETCH_ASSOC);

        $response = ['success' => false];

        if ($disbursement) {
            $response = [
                'success' => true,
                'total' => $disbursement['total']
            ];
        }

        echo json_encode($response);
    } else {
        echo json_encode(['success' => false]);
    }
?>
