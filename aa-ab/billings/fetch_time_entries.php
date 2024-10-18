<?php
    require_once '../../includes/db.php';

    if (isset($_POST['clientId'], $_POST['caseId'], $_POST['dateFrom'], $_POST['dateTo'])) {
        $clientId = $_POST['clientId'];
        $caseId = $_POST['caseId'];
        $dateFrom = $_POST['dateFrom'];
        $dateTo = $_POST['dateTo'];

        try {
            $query = "SELECT *
                      FROM time_entries
                      WHERE clientId = :clientId
                      AND caseId = :caseId
                      AND dateCreated BETWEEN :dateFrom AND :dateTo
                      AND billableStatus = 'billable' AND status = '0'
                      ORDER BY dateCreated ASC";

            $stmt = $connect->prepare($query);
            $stmt->bindParam(':clientId', $clientId, PDO::PARAM_INT);
            $stmt->bindParam(':caseId', $caseId, PDO::PARAM_INT);
            $stmt->bindParam(':dateFrom', $dateFrom);
            $stmt->bindParam(':dateTo', $dateTo);
            $stmt->execute();

            $timeEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($timeEntries) > 0) {
                // Decrypt description for each time entry
                foreach ($timeEntries as &$entry) {
                    $entry['description'] = decrypt($entry['description']);
                }
                echo json_encode(['success' => true, 'timeEntries' => $timeEntries]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No time entries found']);
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error fetching time entries']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
?>