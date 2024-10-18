<?php
    include '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $caseId = filter_input(INPUT_POST, 'caseId', FILTER_SANITIZE_SPECIAL_CHARS);
        $caseNo = filter_input(INPUT_POST, 'caseNo', FILTER_SANITIZE_SPECIAL_CHARS);
        $caseTitle = filter_input(INPUT_POST, 'caseTitle', FILTER_SANITIZE_SPECIAL_CHARS);
        $clientId = filter_input(INPUT_POST, 'clientId', FILTER_SANITIZE_SPECIAL_CHARS);
        $lawFirmId = filter_input(INPUT_POST, 'lawFirmId', FILTER_SANITIZE_SPECIAL_CHARS);
        $caseStatus = filter_input(INPUT_POST, 'caseStatus', FILTER_SANITIZE_SPECIAL_CHARS);
        $caseDescription = filter_input(INPUT_POST, 'caseDescription', FILTER_SANITIZE_SPECIAL_CHARS);
        $caseDate = filter_input(INPUT_POST, 'caseDate', FILTER_SANITIZE_SPECIAL_CHARS);
        $feeMethod = filter_input(INPUT_POST, 'feeMethod', FILTER_SANITIZE_SPECIAL_CHARS);
        $fixedFee = filter_input(INPUT_POST, 'fixedFee', FILTER_SANITIZE_SPECIAL_CHARS);
        $currency = filter_input(INPUT_POST, 'currency', FILTER_SANITIZE_SPECIAL_CHARS);
        $hourlyRate = filter_input(INPUT_POST, 'hourlyRate', FILTER_SANITIZE_SPECIAL_CHARS);
        $selectedLawyers = json_decode($_POST['selectedLawyers'], true);
        $createdAt = date('Y-m-d H:i:s');
        $userId = $_SESSION['user_id'];
        if ($fixedFee === "") {
            $fixedFee = 0.00;
        }

        if ($hourlyRate == "") {
            $hourlyRate = 0.00;
        }

        $caseTitle = encrypt($caseTitle);
        $caseDescription = encrypt($caseDescription);

        try {
            $connect->beginTransaction();

            if ($caseId) {
                // Update case
                $sql = $connect->prepare("UPDATE cases SET caseNo=?, caseTitle=?, clientId=?, lawFirmId=?, caseStatus=?, caseDescription=?, caseDate=?, feeMethod=?, fixedFee=?, currency=?, hourly_rate=?, created_at=? WHERE id=?");
                $sql->execute([$caseNo, $caseTitle, $clientId, $lawFirmId, $caseStatus, $caseDescription, $caseDate, $feeMethod, $fixedFee, $currency, $hourlyRate, $caseId]);

                // Remove existing access controls
                $sql = $connect->prepare("DELETE FROM case_access WHERE caseId=?");
                $sql->execute([$caseId]);
            } else {
                // Insert new case
                $sql = $connect->prepare("INSERT INTO cases (caseNo, caseTitle, clientId, lawFirmId, caseStatus, caseDescription, caseDate, feeMethod, fixedFee, currency, hourly_rate) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
                $sql->execute([$caseNo, $caseTitle, $clientId, $lawFirmId, $caseStatus, $caseDescription, $caseDate, $feeMethod, $fixedFee, $currency, $hourlyRate]);
                $caseId = $connect->lastInsertId();
                // We instert case status to track the work flow
                $insert = $connect->prepare("INSERT INTO `case_status`(`caseId`, `clientId`, `lawFirmId`, `case_status`) VALUES (?, ?, ?, ?) ");
                $insert->execute([$caseId, $clientId, $lawFirmId, $caseStatus]);
            }

            // Add new access controls
            foreach ($selectedLawyers as $lawyerId) {
                $sql = $connect->prepare("INSERT INTO case_access (caseNo, caseId, userId, lawFirmId) VALUES (?,?,?,?)");
                $sql->execute([$caseNo, $caseId, $lawyerId, $lawFirmId]);
            }

            // Handle document uploads
            if (isset($_FILES['caseDocuments'])) {
                $totalFiles = count($_FILES['caseDocuments']['name']);
                for ($i = 0; $i < $totalFiles; $i++) {
                    $documentName = $_FILES['caseDocuments']['name'][$i];
                    $documentTmpName = $_FILES['caseDocuments']['tmp_name'][$i];
                    $documentSize = $_FILES['caseDocuments']['size'][$i];
                    $documentError = $_FILES['caseDocuments']['error'][$i];
                    $documentType = $_FILES['caseDocuments']['type'][$i];

                    if ($documentError === 0) {
                        $documentDestination = '../caseDocuments/' . $documentName;

                        if (move_uploaded_file($documentTmpName, $documentDestination)) {
                            $sql = $connect->prepare("INSERT INTO caseDocuments (caseId, caseNo, documentName, date_added, userId, lawFirmId) VALUES (?, ?,?,?,?,?)");
                            $sql->execute([$caseId, $caseNo, $documentName, $createdAt, $userId, $lawFirmId]);
                        }
                    }
                }
            }


            $connect->commit();
            echo "Case successfully saved.";
        } catch (PDOException $e) {
            $connect->rollBack();
            echo "Error: " . $e->getMessage();
        }
    }
?>
