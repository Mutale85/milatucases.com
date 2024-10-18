<?php
    require_once '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $caseId = $_POST['caseId'];
        $clientId = $_POST['clientId'];
        
        $query = $connect->prepare("SELECT id, analysis, created_at FROM case_analyses WHERE caseId = ? AND clientId = ? ORDER BY created_at DESC LIMIT 1");
        $query->execute([$caseId, $clientId]);
        
        $response = ['success' => false, 'message' => '', 'analysis' => null, 'created_at' => null, 'analysis_id' => null];

        if($query->rowCount() > 0){
            $row = $query->fetch(PDO::FETCH_ASSOC);
            $decryptedAnalysis = decrypt($row['analysis']);
            $analysis = json_decode($decryptedAnalysis, true);
            $analysisId = $row['id'];
            $createdAt = new DateTime($row['created_at']);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                // If JSON decoding failed, treat the entire decrypted content as a single analysis
                $analysis = [['title' => 'Analysis', 'content' => $decryptedAnalysis]];
            }
            
            if (!empty($analysis)) {
                $response['success'] = true;
                $response['analysis'] = $analysis;
                $response['created_at'] = $createdAt->format('Y-m-d H:i:s');
                $response['analysis_id'] = $analysisId;
            } else {
                $response['message'] = "Error: Unable to parse analysis data for ID {$analysisId}";
            }
        } else {
            $response['message'] = "No analyses found for this case.";
        }
    } else {
        $response['message'] = "Invalid request method";
    }

    header('Content-Type: application/json');
    echo json_encode($response);
?>