<?php
    include '../../includes/db.php';
    try {
        $stmt = $connect->prepare("SELECT id, caseTitle FROM cases WHERE lawFirmId = ?");
        $stmt->execute([$_SESSION['parent_id']]);
        $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Decrypt caseTitle for each case
        foreach ($cases as &$case) {
            $case['caseTitle'] = decrypt($case['caseTitle']);
        }

        echo json_encode($cases);
    } catch (PDOException $e) {
        echo json_encode([]);
    }
?>
