<?php 
    include '../../includes/db.php';

    if (isset($_POST['id'])) {
        $lawFirmUserId = $_POST['id'];
        $parentId = $_SESSION['parent_id']; // Assuming you store this in the session

        try {
            // Fetch member data
            $stmt = $connect->prepare("SELECT id, firmId, names, firmName, email, phonenumber, parentId, joinDate, activate, userRole, job, title FROM lawFirms WHERE id = :id AND parentId = :parent_id");
            $stmt->execute(['id' => $lawFirmUserId, 'parent_id' => $parentId]);
            $member = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($member) {
                // We don't need to fetch separate permissions anymore
                // Just return the member data
                echo json_encode(['member' => $member]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Member not found']);
            }
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'No member ID provided']);
    }
?>