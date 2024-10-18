<?php
    require '../../includes/db.php';

    function titheGraphs($connect) {
        $stmt = $connect->prepare("
            SELECT 
                DATE_FORMAT(donationDate, '%b') AS month, 
                SUM(amount) as total 
            FROM church_tithes 
            WHERE churchId = ? 
            GROUP BY month
            ORDER BY MONTH(donationDate)
        ");
        $stmt->execute([$_SESSION['parent_id']]);

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $monthlyTithes = [];
        foreach ($data as $row) {
            $monthlyTithes[$row['month']] = $row['total'];
        }

        return ['monthlyTithes' => $monthlyTithes];
    }

    $data = titheGraphs($connect);
    echo json_encode($data);
?>
