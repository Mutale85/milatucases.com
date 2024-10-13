<?php
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caseId = $_POST['caseId'];
    $clientId = $_POST['clientId'];
    
    $query = $connect->prepare("SELECT id, analysis, created_at FROM case_analyses WHERE caseId = ? AND clientId = ? ORDER BY created_at DESC");
    $query->execute([$caseId, $clientId]);
    
    if($query->rowCount() > 0){
        $alertStyles = ['warning', 'primary', 'secondary', 'success', 'danger', 'info', 'light', 'dark'];
        
        foreach($query->fetchAll(PDO::FETCH_ASSOC) as $row){
            $decryptedAnalysis = decrypt($row['analysis']);
            $analysis = json_decode($decryptedAnalysis, true);
            $analysisId = $row['id'];
            $createdAt = new DateTime($row['created_at']);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                // If JSON decoding failed, treat the entire decrypted content as a single analysis
                $analysis = [['title' => 'Analysis', 'content' => $decryptedAnalysis]];
            }
            
            if (!empty($analysis)) {
                // Randomly select a section from the analysis
                $randomSection = $analysis[array_rand($analysis)];
                $sectionTitle = isset($randomSection['title']) ? $randomSection['title'] : 'Analysis';
                $sectionContent = isset($randomSection['content']) ? $randomSection['content'] : $randomSection;
                
                // Get a random snippet from the section content
                $words = explode(' ', $sectionContent);
                $startIndex = rand(0, max(0, count($words) - 10));
                $shortAnalysis = implode(' ', array_slice($words, $startIndex, 5)) . '...';
                
                // Randomly select an alert style
                $alertStyle = $alertStyles[array_rand($alertStyles)];
                
                echo "<div class='alert alert-{$alertStyle} alert-dismissible fade show analysis-item cursor-pointer' role='alert' data-id='{$analysisId}'>
                        <strong>{$sectionTitle}:</strong> {$shortAnalysis}
                        <br><small class='text-muted'>Created: {$createdAt->format('Y-m-d H:i:s')}</small>
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        <div class='full-analysis' style='display:none;'>" . htmlspecialchars(json_encode($analysis)) . "</div>
                      </div>";
            } else {
                echo "<div class='alert alert-danger'>Error: Unable to parse analysis data for ID {$analysisId}</div>";
            }
        } 
    } else {
        echo "<div class='alert alert-info'>No analyses found for this case.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Invalid request method</div>";
}
?>