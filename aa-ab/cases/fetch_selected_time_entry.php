<?php
    /*
    require_once '../../includes/db.php';

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $timeEntryId = $_POST['id'];
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role']; // Assuming the user's role is stored in the session

        // Check if the user is a superAdmin
        $isSuperAdmin = ($userRole === 'superAdmin');

        if ($isSuperAdmin) {
            // SuperAdmin can view any time entry
            $query = $connect->prepare("SELECT * FROM time_entries WHERE id = ?");
            $params = [$timeEntryId];
        } else {
            // Regular users can only view their own time entries
            $query = $connect->prepare("SELECT * FROM time_entries WHERE id = ? AND userId = ?");
            $params = [$timeEntryId, $userId];
        }

        $query->execute($params);

        if ($query->rowCount() > 0) {
            $timeEntry = $query->fetch(PDO::FETCH_ASSOC);
            
            // Decrypt sensitive data if necessary
            $timeEntry['description'] = decrypt($timeEntry['description']);
            
            // Add a flag to indicate if the viewer is the owner of the entry
            $timeEntry['isOwner'] = ($timeEntry['userId'] == $userId);
            
            echo json_encode(['success' => true, 'data' => $timeEntry]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Time entry not found or you do not have permission to view this entry']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
    */

    require_once '../../includes/db.php';

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $timeEntryId = $_POST['id'];
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];
        
        // Check if the user is a superAdmin
        $isSuperAdmin = ($userRole === 'superAdmin');
        
        try {
            if ($isSuperAdmin) {
                // SuperAdmin can view any time entry
                $query = $connect->prepare("SELECT * FROM time_entries WHERE id = ?");
                $params = [$timeEntryId];
            } else {
                // Regular users can only view their own time entries
                $query = $connect->prepare("SELECT * FROM time_entries WHERE id = ? AND userId = ?");
                $params = [$timeEntryId, $userId];
            }
            
            $query->execute($params);
            
            if ($query->rowCount() > 0) {
                $timeEntry = $query->fetch(PDO::FETCH_ASSOC);
                
                // Decrypt sensitive data
                $timeEntry['description'] = decrypt($timeEntry['description']);
                
                // Add a flag to indicate if the viewer is the owner of the entry
                $timeEntry['isOwner'] = ($timeEntry['userId'] == $userId);
                
                echo json_encode([
                    'success' => true, 
                    'data' => [
                        'caseId' => $timeEntry['caseId'],
                        'dateCreated' => $timeEntry['dateCreated'],
                        'timeCreated' => $timeEntry['timeCreated'],
                        'hours' => $timeEntry['hours'],
                        'minutes' => $timeEntry['minutes'],
                        'currency' => $timeEntry['currency'],
                        'hourlyRate' => $timeEntry['hourlyRate'],
                        'cost' => $timeEntry['cost'],
                        'description' => $timeEntry['description'],
                        'billableStatus' => $timeEntry['billableStatus'],
                        'id' => $timeEntry['id'],
                        'isOwner' => $timeEntry['isOwner']
                    ]
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Time entry not found or you do not have permission to view this entry'
                ]);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid request: Missing or empty time entry ID'
        ]);
    }

?>