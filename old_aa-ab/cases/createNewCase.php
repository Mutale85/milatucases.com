<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    include '../../includes/db.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $causeId = filter_input(INPUT_POST, 'causeId', FILTER_SANITIZE_SPECIAL_CHARS);
        $caseId = filter_input(INPUT_POST, 'caseId', FILTER_SANITIZE_SPECIAL_CHARS);
        $caseNo = filter_input(INPUT_POST, 'caseNo', FILTER_SANITIZE_SPECIAL_CHARS);
        $caseTitle = filter_input(INPUT_POST, 'caseTitle', FILTER_SANITIZE_SPECIAL_CHARS);
        $clientId = filter_input(INPUT_POST, 'clientId', FILTER_SANITIZE_SPECIAL_CHARS);
        $client_tpin = filter_input(INPUT_POST, 'client_tpin', FILTER_SANITIZE_SPECIAL_CHARS);
        $lawFirmId = filter_input(INPUT_POST, 'lawFirmId', FILTER_SANITIZE_SPECIAL_CHARS);
        $caseStatus = filter_input(INPUT_POST, 'caseStatus', FILTER_SANITIZE_SPECIAL_CHARS);
        $customStatus = filter_input(INPUT_POST, 'custom-status', FILTER_SANITIZE_SPECIAL_CHARS);
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

        if($caseStatus == 'custom'){
            $case_status = $customStatus;
        }else{
            $case_status = $caseStatus;
        }

        if($selectedLawyers === ""){
            exit("Please Select Lawyers for this case");
        }

        
        try {
            $connect->beginTransaction();

            if (!empty($caseId)) {
                // Update case
                $sql = $connect->prepare("UPDATE cases SET caseNo=?, causeId = ?, caseTitle=?, caseStatus=?, caseDescription=?, caseDate=?, feeMethod=?, fixedFee=?, currency=?, hourly_rate=?, created_at=?, other_case_status = ? WHERE id=?");
                $sql->execute([$caseNo, $causeId, $caseTitle, $caseStatus, $caseDescription, $caseDate, $feeMethod, $fixedFee, $currency, $hourlyRate, $createdAt, $customStatus, $caseId]);

                // Remove existing access controls
                $sql = $connect->prepare("DELETE FROM case_access WHERE caseId=? ");
                $sql->execute([$caseId]);

                // $clientId = fetchCLientIdCaseId($caseId);
                // $client_tpin = clientsTpinByCaseId($caseId);

                $insert = $connect->prepare("INSERT INTO `case_status`(`userId`, `lawFirmId`, `clientId`, `client_tpin`, `caseId`, `case_status`) VALUES (?, ?, ?, ?, ?, ?) ");
                $insert->execute([$userId, $lawFirmId, $clientId, $client_tpin, $caseId, $case_status]);
                echo "Case successfully updated";
            } else {
                $query = $connect->prepare("SELECT * FROM cases WHERE caseNo = ? AND lawFirmId = ?");
                $query->execute([$caseNo, $lawFirmId]);
                if($query->rowCount() > 0){
                    exit("Case no: $caseNo is arealdy in existence, you can append though");
                }
                // Insert new case
                $sql = $connect->prepare("INSERT INTO cases (userId, lawFirmId, clientId, client_tpin, caseNo, causeId, caseTitle, caseStatus, caseDescription, caseDate, feeMethod, fixedFee, currency, hourly_rate, other_case_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $sql->execute([$userId, $lawFirmId, $clientId, $client_tpin, $caseNo, $causeId, $caseTitle, $caseStatus, $caseDescription, $caseDate, $feeMethod, $fixedFee, $currency, $hourlyRate, $customStatus]);
                $caseId = $connect->lastInsertId();

                // We instert case status to track the work flow
                $insert = $connect->prepare("INSERT INTO `case_status`(`userId`, `lawFirmId`, `clientId`, `client_tpin`, `caseId`, `case_status`) VALUES (?, ?, ?, ?, ?, ?) ");
                $insert->execute([$userId, $lawFirmId, $clientId, $client_tpin, $caseId, $case_status]);

                echo "Case successfully saved.";
            }

            // Add new access controls
            
            if (isset($_POST['accessControl'])) {
                foreach ($_POST['accessControl'] as $lawyerId) {
                    // echo $lawyerId . "<br>";
                    $sql = $connect->prepare("INSERT INTO case_access (caseNo, caseId, userId, lawFirmId, lawyerId) VALUES (?, ?, ?, ?, ?)");
                    $sql->execute([$caseNo, $caseId, $userId, $lawFirmId, $lawyerId]);
                }
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
                            $sql = $connect->prepare("INSERT INTO caseDocuments (caseId, caseNo, documentName, date_added, userId, lawFirmId) VALUES (?, ?, ?, ?, ?, ?)");
                            $sql->execute([$caseId, $caseNo, $documentName, $createdAt, $userId, $lawFirmId]);
                        }
                    }
                }
            }

            $connect->commit();
            
        } catch (PDOException $e) {
            $connect->rollBack();
            echo "Error: " . $e->getMessage();
        }
        
    }
?>
