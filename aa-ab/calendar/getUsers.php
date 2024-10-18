<?php

    require_once '../../includes/db.php';

    // Check if parentId is set
    if(isset($_POST['parentId'])) {
        $parentId = $_POST['parentId'];
        
        try {
            // Prepare the SQL statement
            $stmt = $connect->prepare("SELECT id, names, email, phonenumber FROM lawFirms WHERE parentId = ? ");
            $stmt->execute([$parentId]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($users);
            
        } catch(PDOException $e) {
            // Handle any errors
            echo json_encode(['error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'No parentId provided']);
    }
?>