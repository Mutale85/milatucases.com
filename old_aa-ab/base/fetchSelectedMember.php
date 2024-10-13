<?php
/*
	include '../../includes/db.php'; 
	if (isset($_POST['id'])) {
	    $lawFirmUserId = $_POST['id'];
	    $parentId = $_SESSION['parent_id']; // Assuming you store this in the session

	    try {
	        $stmt = $connect->prepare("SELECT * FROM lawFirms WHERE id = :id AND parentId = :parent_id");
	        $stmt->execute(['id' => $lawFirmUserId, 'parent_id' => $parentId]);
	        $member = $stmt->fetch(PDO::FETCH_ASSOC);

	        if ($member) {
	            // Return the member data as JSON
	            echo json_encode($member);
	        } else {
	            http_response_code(404);
	            echo json_encode(['error' => 'Member not found']);
	        }
	    } catch (PDOException $e) {
	        http_response_code(500);
	        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
	    }
	}
*/



include '../../includes/db.php';

if (isset($_POST['id'])) {
    $lawFirmUserId = $_POST['id'];
    $parentId = $_SESSION['parent_id']; // Assuming you store this in the session

    try {
        // Fetch member data
        $stmt = $connect->prepare("SELECT * FROM lawFirms WHERE id = :id AND parentId = :parent_id");
        $stmt->execute(['id' => $lawFirmUserId, 'parent_id' => $parentId]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch user permissions (if member is found)
        if ($member) {
            $stmt = $connect->prepare("SELECT * FROM permissions WHERE userId = :userId");
            $stmt->execute(['userId' => $lawFirmUserId]);
            $permissions = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Member not found']);
            exit; // Exit script if member not found
        }

        // Return member and permission data as JSON
        echo json_encode(['member' => $member, 'permissions' => $permissions]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
