<?php
    require_once '../../includes/db.php';
    require_once '../../includes/conf.php';
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $caseId = $_POST['caseId'];
        $clientId = $_POST['clientId'];
        $analysisCount = getAnalysisCount($caseId);
        $lawFirmId = $_SESSION['parent_id'];
        $sub = checkSubscription($lawFirmId);
        if($sub == 'trial'){
             echo json_encode(['success' => true, 'message' => "AI analysys is for paid up users"]);
        }else{
        if ($analysisCount >= 5) {  // Limit to 5 analyses per 24 hours
            echo json_encode(['success' => false, 'message' => 'Analysis limit reached. Please try again later.']);
            exit;
        }
        // Fetch case details from the database
        $stmt = $connect->prepare("SELECT caseTitle, caseDescription FROM cases WHERE id = ?");
        $stmt->execute([$caseId]);
        $case = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($case) {
            // Decrypt sensitive fields
            $caseTitle = decrypt($case['caseTitle']);
            $caseDescription = decrypt($case['caseDescription']);
            $openAIResponse = makeOpenAIRequest($caseTitle, $caseDescription);
            if (isset($openAIResponse['error'])) {
                echo json_encode(['success' => false, 'message' => 'OpenAI API Error', 'error' => $openAIResponse['error']]);
            } else if (isset($openAIResponse['choices'][0]['message']['content'])) {
                $analysis = $openAIResponse['choices'][0]['message']['content'];
                
                // Store the analysis result
                storeAnalysisResult($caseId, $clientId, $analysis);
                echo json_encode(['success' => true, 'analysis' => $analysis]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Unexpected response format from OpenAI', 'response' => $openAIResponse]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Case not found']);
        }
      }
    }
    

?>
