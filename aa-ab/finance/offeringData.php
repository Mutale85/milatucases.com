<?php
    require '../../includes/db.php';

    function offeringGraphs($connect) {
        $stmt = $connect->prepare("
            SELECT 
                DATE_FORMAT(donationDate, '%b') AS month, 
                SUM(amount) as total 
            FROM church_offering 
            WHERE churchId = ? 
            GROUP BY month
            ORDER BY MONTH(donationDate)
        ");
        $stmt->execute([$_SESSION['parent_id']]);

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $monthlyOffering = [];
        foreach ($data as $row) {
            $monthlyOffering[$row['month']] = $row['total'];
        }

        return ['monthlyOffering' => $monthlyOffering];
    }

    $data = offeringGraphs($connect);
    echo json_encode($data);
?>
