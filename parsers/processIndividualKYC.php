<?php
	include '../includes/db.php';
    include '../includes/conf.php';
    require '../vendor/autoload.php'; // Include the PHPMailer library

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $clientId = filter_input(INPUT_POST, 'clientId', FILTER_SANITIZE_SPECIAL_CHARS);
        $lawFirmId = filter_input(INPUT_POST, 'lawFirmId', FILTER_SANITIZE_SPECIAL_CHARS);

        $client_name = encrypt(filter_input(INPUT_POST, 'client-name', FILTER_SANITIZE_SPECIAL_CHARS));
        $client_tpin = encrypt(filter_input(INPUT_POST, 'client_tpin', FILTER_SANITIZE_SPECIAL_CHARS));
        $marital_status = encrypt(filter_input(INPUT_POST, 'marital-status', FILTER_SANITIZE_SPECIAL_CHARS));
        $date_of_birth = filter_input(INPUT_POST, 'date-of-birth', FILTER_SANITIZE_SPECIAL_CHARS);
        $sex = encrypt(filter_input(INPUT_POST, 'sex', FILTER_SANITIZE_SPECIAL_CHARS));
        $profession = encrypt(filter_input(INPUT_POST, 'profession', FILTER_SANITIZE_SPECIAL_CHARS));
        $occupation = encrypt(filter_input(INPUT_POST, 'occupation', FILTER_SANITIZE_SPECIAL_CHARS));
        $nationality = encrypt(filter_input(INPUT_POST, 'nationality', FILTER_SANITIZE_SPECIAL_CHARS));
        $identity_type = encrypt(filter_input(INPUT_POST, 'identity-type', FILTER_SANITIZE_SPECIAL_CHARS));
        $identification_number = encrypt(filter_input(INPUT_POST, 'identification-number', FILTER_SANITIZE_SPECIAL_CHARS));
        $date_of_issue = filter_input(INPUT_POST, 'date-of-issue', FILTER_SANITIZE_SPECIAL_CHARS);
        $place_of_issue = encrypt(filter_input(INPUT_POST, 'place-of-issue', FILTER_SANITIZE_SPECIAL_CHARS));
        $identification_issued_by = encrypt(filter_input(INPUT_POST, 'identification-issued-by', FILTER_SANITIZE_SPECIAL_CHARS));
        $tpn_no = encrypt(filter_input(INPUT_POST, 'TPN-no', FILTER_SANITIZE_SPECIAL_CHARS));
        $residential_address = encrypt(filter_input(INPUT_POST, 'residential-address', FILTER_SANITIZE_SPECIAL_CHARS));
        $postal_address = encrypt(filter_input(INPUT_POST, 'postal-address', FILTER_SANITIZE_SPECIAL_CHARS));
        $contact_details = encrypt(filter_input(INPUT_POST, 'contact-details', FILTER_SANITIZE_SPECIAL_CHARS));
        
        $politically_exposed_foreign_person = filter_input(INPUT_POST, 'politically-exposed-foreign-person', FILTER_SANITIZE_SPECIAL_CHARS);
        $potentially_exposed_to_money_laundering = filter_input(INPUT_POST, 'potentially-exposed-to-money-laundering', FILTER_SANITIZE_SPECIAL_CHARS);
        $potentially_exposed_to_any_terrorist_act = filter_input(INPUT_POST, 'potentially-exposed-to-any-terrorist-act', FILTER_SANITIZE_SPECIAL_CHARS);
        $criminal_activity = filter_input(INPUT_POST, 'criminalActivity', FILTER_SANITIZE_SPECIAL_CHARS);
        $terrorist_association = filter_input(INPUT_POST, 'terroristAssociation', FILTER_SANITIZE_SPECIAL_CHARS);
        $terrorist_dealings = filter_input(INPUT_POST, 'terroristDealings', FILTER_SANITIZE_SPECIAL_CHARS);
        $signature_date = filter_input(INPUT_POST, 'signature_date', FILTER_SANITIZE_SPECIAL_CHARS);
        $signature_names = filter_input(INPUT_POST, 'signature_names', FILTER_SANITIZE_SPECIAL_CHARS);

        try {
            //*/ Check if the client already exists in individualPart1
            $checkQuery1 = $connect->prepare("SELECT clientId FROM `individualPart1` WHERE lawFirmId = ? AND clientId = ?");
            $checkQuery1->execute([$lawFirmId, $clientId]);

            if ($checkQuery1->rowCount() > 0) {
                $updateQuery1 = $connect->prepare("UPDATE `individualPart1` SET client_name = ?, marital_status = ?, date_of_birth = ?, sex = ?, profession = ?, occupation = ?, nationality = ?, identity_type = ?, identification_number = ?, date_of_issue = ?, place_of_issue = ?, identification_issued_by = ?, tpn_no = ?, residential_address = ?, postal_address = ?, contact_details = ? WHERE lawFirmId = ? AND clientId = ?");
                $updateQuery1->execute([$client_name, $marital_status, $date_of_birth, $sex, $profession, $occupation, $nationality, $identity_type, $identification_number, $date_of_issue, $place_of_issue, $identification_issued_by, $tpn_no, $residential_address, $postal_address, $contact_details, $lawFirmId, $clientId]);
            } else {
                // Insert new record into individualPart1
                $insertQuery1 = $connect->prepare("INSERT INTO `individualPart1`(`client_name`, `lawFirmId`, `clientId`, `client_tpin`, `marital_status`, `date_of_birth`, `sex`, `profession`, `occupation`, `nationality`, `identity_type`, `identification_number`, `date_of_issue`, `place_of_issue`, `identification_issued_by`, `tpn_no`, `residential_address`, `postal_address`, `contact_details`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $insertQuery1->execute([$client_name, $lawFirmId, $clientId, $client_tpin, $marital_status, $date_of_birth, $sex, $profession, $occupation, $nationality, $identity_type, $identification_number, $date_of_issue, $place_of_issue, $identification_issued_by, $tpn_no, $residential_address, $postal_address, $contact_details]);
            }

            // Check if the client already exists in individualPart2
            $checkQuery2 = $connect->prepare("SELECT clientId FROM `individualPart2` WHERE lawFirmId = ? AND clientId = ?");
            $checkQuery2->execute([$lawFirmId, $clientId]);

            $upload_dir = '../uploads/signatures/';
            // Ensure the upload directory exists
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $signature_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['signature-data']));
            $signature_file = $upload_dir . uniqid() . '.png';
            file_put_contents($signature_file, $signature_data);

            if ($checkQuery2->rowCount() > 0) {
                $updateQuery2 = $connect->prepare("UPDATE `individualPart2` SET politically_exposed_foreign_person = ?, potentially_exposed_to_money_laundering = ?, potentially_exposed_to_any_terrorist_act = ?, criminal_activity = ?, terrorist_association = ?, terrorist_dealings = ?, signature_names = ?, signature_data = ?, signature_date = ?
                    WHERE lawFirmId = ? AND clientId = ?");
                $updateQuery2->execute([$politically_exposed_foreign_person, $potentially_exposed_to_money_laundering, $potentially_exposed_to_any_terrorist_act, $criminal_activity, $terrorist_association, $terrorist_dealings, $signature_names, $signature_file, $signature_date, $lawFirmId, $clientId]);
            } else {
                // Insert new record into individualPart2
                $insertQuery2 = $connect->prepare("INSERT INTO `individualPart2`(`lawFirmId`, `clientId`, `client_tpin`, `politically_exposed_foreign_person`, `potentially_exposed_to_money_laundering`, `potentially_exposed_to_any_terrorist_act`, `criminal_activity`, `terrorist_association`, `terrorist_dealings`, `signature_names`, `signature_data`, `signature_date`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $insertQuery2->execute([$lawFirmId, $clientId, $client_tpin, $politically_exposed_foreign_person, $potentially_exposed_to_money_laundering, $potentially_exposed_to_any_terrorist_act, $criminal_activity, $terrorist_association, $terrorist_dealings, $signature_names, $signature_file, $signature_date]);
            }
            //*/

            $lawFirmEmail = getFirmEmail($lawFirmId);
            $lawFirmPhoneNumber = getFirmPhoneNumber($lawFirmId);

            // We also send an SMS and email to the firm
            $sql = $connect->prepare("UPDATE `lawFirmClients` SET `kyc`= '2' WHERE `id` = ? AND `lawFirmId`= ? ");
            $sql->execute([$clientId, $lawFirmId]);
            $clientName = decrypt($client_name);

            $subject = "$clientName - Individual Client Form Submission";
            $body = " Client - {$clientName} - has submitted their KYC form. Please review the submission.";
            $smsMessage = " Client {$clientName} has submitted their KYC form. Please review the submission.";
            sendSMS(API, SENDER, $lawFirmPhoneNumber, $smsMessage);

            // Send email using PHPMailer
            $mail = new PHPMailer\PHPMailer\PHPMailer();

    		    try {
    		        //Server settings
    		        $mail->isSMTP();
    		        $mail->Host       = 'smtp.zoho.com'; 
    		        $mail->SMTPAuth   = true;
    		        $mail->Username   = 'support@milatucases.com';
    		        $mail->Password   = 'Javeria##2019';
    		        $mail->SMTPSecure = 'tls';
    		        $mail->Port       = 587;
    		        //Recipients
    		        $mail->setFrom('support@milatucases.com', 'Individual KYC Submitted');
    		        $mail->addAddress($lawFirmEmail);
    		        // Content
    		        $mail->isHTML(true);
    		        $mail->Subject = $subject;
    		        $mail->Body    = $body;
    		        $mail->send();
    		        echo 'Form submitted successfully and email has been sent.';
    		    } catch (Exception $e) {
    		        echo "Form submitted successfully but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    		    }

    		} catch (Exception $e) {
    	        // $connect->rollBack();
    	        echo "Failed to insert or update form data: " . $e->getMessage();
    	    }
	}
    
?>
