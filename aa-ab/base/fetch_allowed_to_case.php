<?php
    include '../../includes/db.php';

    if (isset($_GET['caseNo'])) {
        $caseNo = $_GET['caseNo'];
        $lawFirmId = $_GET['lawFirmId'];
        $query = $connect->prepare("SELECT * FROM case_access WHERE id = ? AND lawFirmId = ? ");
        $query->execute([$caseNo, $lawFirmId]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

            
    
    }
?>
