<?php
    require '../../includes/db.php';
    require '../../includes/conf.php';
    require '../../vendor/autoload.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $clientId = filter_input(INPUT_POST, 'clientId', FILTER_SANITIZE_SPECIAL_CHARS);
        $client_tpin = filter_input(INPUT_POST, 'client_tpin', FILTER_SANITIZE_SPECIAL_CHARS);
        $caseNo = filter_input(INPUT_POST, 'caseNo', FILTER_SANITIZE_SPECIAL_CHARS);
        $caseId = decrypt($_POST['caseId']);
        $userId = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_SPECIAL_CHARS);
        $lawFirmId = filter_input(INPUT_POST, 'lawFirmId', FILTER_SANITIZE_SPECIAL_CHARS);
        $milestoneTitle = encrypt(filter_input(INPUT_POST, 'milestoneTitle', FILTER_SANITIZE_SPECIAL_CHARS));
        $milestoneDescription = encrypt(filter_input(INPUT_POST, 'milestoneDescription', FILTER_SANITIZE_SPECIAL_CHARS));
        $agreed = isset($_POST['agree']) ? 1 : 0;
        $milestoneId = filter_input(INPUT_POST, 'milestoneId', FILTER_SANITIZE_SPECIAL_CHARS);

        try {
            if (!empty($milestoneId)) {
                // Update existing milestone
                $sql = "UPDATE case_milestones SET 
                        clientId = ?, caseId = ?, caseNo = ?, userId = ?, lawFirmId = ?, milestoneTitle = ?, milestoneDescription = ?, agreed = ? 
                        WHERE id = ?";
                $stmt = $connect->prepare($sql);
                $stmt->execute([$clientId, $caseId, $caseNo, $userId, $lawFirmId, $milestoneTitle, $milestoneDescription, $agreed, $milestoneId]);
                echo 'Milestone updated successfully';
                // we should notify the people taske to to the case apart from the sender.
                fetchLawyersAssignedToCase($caseId, $userId, $caseNo);
            } else {
                // Insert new milestone
                $sql = "INSERT INTO case_milestones (clientId, client_tpin, caseId, caseNo, userId, lawFirmId, milestoneTitle, milestoneDescription, agreed) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $connect->prepare($sql);
                $stmt->execute([$clientId, $client_tpin, $caseId, $caseNo, $userId, $lawFirmId, $milestoneTitle, $milestoneDescription, $agreed]);
                echo 'Milestone saved successfully';
                fetchLawyersAssignedToCase($caseId, $userId, $caseNo);
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    function fetchLawyersAssignedToCase($caseId, $lawyerId, $caseNo){
        global $connect;
        $query = $connect->prepare("SELECT * FROM case_access WHERE caseId = ? AND lawyerId != ? ");
        $query->execute([$caseId, $lawyerId]);
        foreach($query->fetchAll() as $row){
            $lawyerId = $row['lawyerId'];
            $lawyerEmail = fetchLawFirmMemberEmail($lawyerId);

            $lawyerPhone = fetchLawFirmMemberPhone($lawyerId);
            $lawyerName = fetchLawFirmMemberNames($lawyerId);
            $subject = "Milestone Added to Case No ($caseNo)";
            $body = "Dear $lawyerName,<br><br>A milestone has been added to Case No ($caseNo). Please review the milestone details at your earliest convenience.<br><br>Best Regards,<br>LegalZM.com";
            $smsMessage = "Dear $lawyerName, a milestone has been added to Case No ($caseNo). Please review the details.";

            sendSMS(API, SENDER, $lawyerPhone, $smsMessage);
            $lawFirm = $_SESSION['lawFirmName'];

            // Send email using PHPMailer
            $mail = new PHPMailer\PHPMailer\PHPMailer();

            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.zoho.com'; 
                $mail->SMTPAuth   = true;
                $mail->Username   = 'support@milatucases.com';
                $mail->Password   = 'mdbm npox ftcj ougf';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;
                //Recipients
                $mail->setFrom('support@milatucases.com', "$lawFirm - Case Milestone ");
                $mail->addAddress($lawyerEmail);
                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $body;
                $mail->send();
            } catch (Exception $e) {
                echo " Mailer Error: {$mail->ErrorInfo}";
            }
        }
    }
?>
