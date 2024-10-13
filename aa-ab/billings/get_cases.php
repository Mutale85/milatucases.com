<?php
// Assuming you have a config file with database connection details
require_once '../../includes/db.php';

// Check if clientId is set in the POST request
if (isset($_POST['clientId'])) {
    $clientId = $_POST['clientId'];
    
    try {
        // Prepare the SQL query
        $query = "SELECT id, caseNo, caseTitle, caseCategory, caseStatus 
                  FROM cases 
                  WHERE clientId = :clientId 
                  ORDER BY caseDate DESC";
        
        $stmt = $connect->prepare($query);
        $stmt->bindParam(':clientId', $clientId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Fetch all cases
        $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Generate HTML options
        if (count($cases) > 0) {
            echo '<option value="">Select Case</option>';
            foreach ($cases as $case) {
                $caseInfo = html_entity_decode($case['caseNo'] . ' - ' . decrypt($case['caseTitle']) );
                echo "<option value='" . $case['id'] . "'>" . $caseInfo . "</option>";
            }
        } else {
            echo '<option value="">No cases found for this client</option>';
        }
    } catch (PDOException $e) {
        // Log the error and return a generic error message
        error_log("Database error: " . $e->getMessage());
        echo '<option value="">Error fetching cases</option>';
    }
} else {
    echo '<option value="">Invalid request</option>';
}
?>