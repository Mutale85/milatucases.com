<?php
    include '../../includes/db.php';

    if (isset($_POST['caseId'])) {
        $caseId = $_POST['caseId'];

        // Fetch case details
        $stmt = $connect->prepare("SELECT * FROM `cases` WHERE `id` = :caseId");
        $stmt->execute(['caseId' => $caseId]);
        $case = $stmt->fetch(PDO::FETCH_ASSOC);

        // Decrypt case title and description
        $case['caseTitle'] = decrypt($case['caseTitle']);
        $case['caseDescription'] = nl2br(html_entity_decode(decrypt($case['caseDescription'])));

        // Fetch case access
        $stmt = $connect->prepare("SELECT `lawyerId` FROM `case_access` WHERE `caseId` = :caseId");
        $stmt->execute(['caseId' => $caseId]);
        $case_access = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Combine all data into an array
        $data = [
            'case' => $case,
            'case_access' => $case_access
        ];

        // Return JSON response
        echo json_encode($data);
    }
?>
