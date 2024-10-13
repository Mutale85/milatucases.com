<?php
    include '../../includes/db.php';
    
    if (isset($_GET['caseNo'])) {
        $caseNo = $_GET['caseNo'];
        $query = $connect->prepare("SELECT caseDocuments FROM cases WHERE caseNo = ? AND lawFirmId = ?");
        $query->execute([$caseNo, $_SESSION['parent_id']]);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $files = explode(',', $result['caseDocuments']);

            // Fetch authorized users
            $query = $connect->prepare("SELECT u.id, u.names as name FROM case_access ca JOIN lawFirms u ON ca.userId = u.id WHERE ca.caseNo = ?");
            $query->execute([$caseNo]);
            $authorizedUsers = $query->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'files' => $files, 'authorizedUsers' => $authorizedUsers]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No files found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    }

?>
