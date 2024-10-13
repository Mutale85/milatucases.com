<?php
	include '../../includes/db.php';
	include '../../includes/conf.php';
	require '../../vendor/autoload.php'; // Include the PHPMailer library

	if($_SERVER['REQUEST_METHOD'] == "POST"){
		$client_name = filter_input(INPUT_POST, 'client-name', FILTER_SANITIZE_SPECIAL_CHARS);
		$lawFirmId = filter_input(INPUT_POST, 'lawFirmId', FILTER_SANITIZE_SPECIAL_CHARS);
		$client_uid = filter_input(INPUT_POST, 'client_uid', FILTER_SANITIZE_SPECIAL_CHARS);
		$date_of_incorporation = filter_input(INPUT_POST, 'date-of-incorporation', FILTER_SANITIZE_SPECIAL_CHARS);
		$place_of_incorporation = filter_input(INPUT_POST, 'place-of-incorporation', FILTER_SANITIZE_SPECIAL_CHARS);
		$business_type = filter_input(INPUT_POST, 'business-type', FILTER_SANITIZE_SPECIAL_CHARS);
		$tax_identification_number = filter_input(INPUT_POST, 'tax-identification-number', FILTER_SANITIZE_SPECIAL_CHARS);
		$registered_office_address = filter_input(INPUT_POST, 'registered-office-address', FILTER_SANITIZE_SPECIAL_CHARS);
		$mailing_address = filter_input(INPUT_POST, 'mailing-address', FILTER_SANITIZE_SPECIAL_CHARS);
		$contact_person = filter_input(INPUT_POST, 'contact-person', FILTER_SANITIZE_SPECIAL_CHARS);
		$contact_number = filter_input(INPUT_POST, 'contact-number', FILTER_SANITIZE_SPECIAL_CHARS);
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
		$auditors = filter_input(INPUT_POST, 'auditors', FILTER_SANITIZE_SPECIAL_CHARS);
		$financial_year_end = filter_input(INPUT_POST, 'financial-year-end', FILTER_SANITIZE_SPECIAL_CHARS);

		$checkStmt = $connect->prepare("SELECT COUNT(*) FROM corporatePart1 WHERE tax_identification_number = ? AND lawFirmId = ? ");
	    $checkStmt->execute([$client_uid, $lawFirmId]);
	    $recordExists = $checkStmt->fetchColumn() > 0;

	    if ($recordExists) {
	        $stmt = $connect->prepare("UPDATE corporatePart1 SET 
	            client_name = ?, date_of_incorporation = ?, place_of_incorporation = ?, business_type = ?, registered_office_address = ?, mailing_address = ?, contact_person = ?, contact_number = ?, email = ?, auditors = ?, financial_year_end = ?
	            WHERE client_uid = ?");
	        $stmt->execute([$client_name, $date_of_incorporation, $place_of_incorporation, $business_type, $registered_office_address, $mailing_address, $contact_person, $contact_number, $email, $auditors, $financial_year_end, $client_uid]);
	    } else {
	        $stmt = $connect->prepare("INSERT INTO corporatePart1 
	            (lawFirmId, client_uid, client_name, date_of_incorporation, place_of_incorporation, business_type, tax_identification_number, registered_office_address, mailing_address, contact_person, contact_number, email, auditors, financial_year_end) 
	            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	        $stmt->execute([$lawFirmId, $client_uid, $client_name, $date_of_incorporation, $place_of_incorporation, $business_type, $tax_identification_number, $registered_office_address, $mailing_address, $contact_person, $contact_number, $email, $auditors, $financial_year_end]);
	    }

	    //============= PART 2 ===============

		$checkStmt = $connect->prepare("SELECT COUNT(*) FROM corporatePart2 WHERE business_id = ? AND lawFirmId = ?");
		$checkStmt->execute([$client_uid, $lawFirmId]);
		$recordExists = $checkStmt->fetchColumn() > 0;

		// Delete the existing record if it exists
		if ($recordExists) {
		    $stmt = $connect->prepare("DELETE FROM corporatePart2 WHERE business_id = ? AND lawFirmId = ?");
		    $stmt->execute([$client_uid, $lawFirmId]);
		}

		
		$totalEntries = count($_POST['d-full-name']);

		for ($key = 0; $key < $totalEntries; $key++) {
		    $fullName = filter_var($_POST['d-full-name'][$key], FILTER_SANITIZE_SPECIAL_CHARS);
		    $gender = filter_var($_POST['d-gender'][$key], FILTER_SANITIZE_SPECIAL_CHARS);
		    $maritalStatus = filter_var($_POST['d-marital-status'][$key], FILTER_SANITIZE_SPECIAL_CHARS);
		    $nationality = filter_var($_POST['d-nationality'][$key], FILTER_SANITIZE_SPECIAL_CHARS);
		    $occupation = filter_var($_POST['d-occupation'][$key], FILTER_SANITIZE_SPECIAL_CHARS);
		    $identityTypeAndNo = filter_var($_POST['d-identity-type'][$key], FILTER_SANITIZE_SPECIAL_CHARS);
		    $datePlaceOfIssue = filter_var($_POST['d-date-place-issue'][$key], FILTER_SANITIZE_SPECIAL_CHARS);
		    $residentialAddress = filter_var($_POST['d-residential-address'][$key], FILTER_SANITIZE_SPECIAL_CHARS);
		    $contactDetails = filter_var($_POST['d-contact-details'][$key], FILTER_SANITIZE_SPECIAL_CHARS);

		    $stmt = $connect->prepare("INSERT INTO corporatePart2 (
		        lawFirmId, business_id, full_name, gender, marital_status, nationality, occupation, identity_type_and_no, date_place_of_issue, residential_address, contact_details
		    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		    $stmt->execute([$lawFirmId, $client_uid, $fullName, $gender, $maritalStatus, $nationality, $occupation, $identityTypeAndNo, $datePlaceOfIssue, $residentialAddress, $contactDetails]);
		}

		// ========== Load Part 3 ===================

		$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
		$marital_status = filter_input(INPUT_POST, 'marital-status', FILTER_SANITIZE_SPECIAL_CHARS);
		$date_of_birth = filter_input(INPUT_POST, 'date-of-birth', FILTER_SANITIZE_SPECIAL_CHARS);
		$sex = filter_input(INPUT_POST, 'sex', FILTER_SANITIZE_SPECIAL_CHARS);
		$profession = filter_input(INPUT_POST, 'profession', FILTER_SANITIZE_SPECIAL_CHARS);
		$occupation = filter_input(INPUT_POST, 'occupation', FILTER_SANITIZE_SPECIAL_CHARS);
		$nationality = filter_input(INPUT_POST, 'nationality', FILTER_SANITIZE_SPECIAL_CHARS);
		$identity_type = filter_input(INPUT_POST, 'identity-type', FILTER_SANITIZE_SPECIAL_CHARS);
		$identification_number = filter_input(INPUT_POST, 'identification-number', FILTER_SANITIZE_SPECIAL_CHARS);
		$date_of_issue = filter_input(INPUT_POST, 'date-of-issue', FILTER_SANITIZE_SPECIAL_CHARS);
		$place_of_issue = filter_input(INPUT_POST, 'place-of-issue', FILTER_SANITIZE_SPECIAL_CHARS);
		$identification_issued_by = filter_input(INPUT_POST, 'identification-issued-by', FILTER_SANITIZE_SPECIAL_CHARS);
		$tpn_no = filter_input(INPUT_POST, 'TPN-no', FILTER_SANITIZE_SPECIAL_CHARS);
		$contact_details = filter_input(INPUT_POST, 'contact-details', FILTER_SANITIZE_SPECIAL_CHARS);

		// Check if the record already exists for the given lawFirmId and business_entity_id
	    $stmt = $connect->prepare("SELECT * FROM corporatePart3 WHERE lawFirmId = ? AND business_entity_id = ?");
	    $stmt->execute([$lawFirmId, $client_uid]);
	    $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);

	    if ($existingRecord) {
	        $stmt = $connect->prepare("UPDATE corporatePart3 SET name = ?, marital_status = ?, date_of_birth = ?, sex = ?, profession = ?, occupation = ?, nationality = ?, identity_type = ?, identification_number = ?, date_of_issue = ?, place_of_issue = ?, identification_issued_by = ?, TPN_no = ?, contact_details = ? WHERE lawFirmId = ? AND business_entity_id = ?");
	        $stmt->execute([$name,$marital_status,$date_of_birth,$sex,$profession,$occupation,$nationality,$identity_type,$identification_number,$date_of_issue,$place_of_issue,$identification_issued_by,$tpn_no,$contact_details,$lawFirmId,$client_uid
	        ]);
	    } else {
	        $stmt = $connect->prepare("INSERT INTO corporatePart3 (lawFirmId, business_entity_id, name, marital_status, date_of_birth, sex, profession, occupation, nationality, identity_type, identification_number, date_of_issue, place_of_issue, identification_issued_by, TPN_no, contact_details) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	        $stmt->execute([$lawFirmId,$client_uid,$name,$marital_status,$date_of_birth,$sex,$profession,$occupation,$nationality,$identity_type,$identification_number,$date_of_issue,$place_of_issue,$identification_issued_by,$tpn_no,$contact_details
	        ]);
	    }

	    // ======== insert part 4 ==================

		// $upload_dir = '../uploads/signatures/';

		// // Ensure the upload directory exists
		// if (!is_dir($upload_dir)) {
		//     mkdir($upload_dir, 0777, true);
		// }

		// Sanitize input fields
		$politically_exposed_foreign_person = filter_input(INPUT_POST, 'politically-exposed-foreign-person', FILTER_SANITIZE_SPECIAL_CHARS);
		$potentially_exposed_to_money_laundering = filter_input(INPUT_POST, 'potentially-exposed-to-money-laundering', FILTER_SANITIZE_SPECIAL_CHARS);
		$potentially_exposed_to_any_terrorist_act = filter_input(INPUT_POST, 'potentially-exposed-to-any-terrorist-act', FILTER_SANITIZE_SPECIAL_CHARS);
		$criminal_activity = filter_input(INPUT_POST, 'criminalActivity', FILTER_SANITIZE_SPECIAL_CHARS);
		$terrorist_association = filter_input(INPUT_POST, 'terroristAssociation', FILTER_SANITIZE_SPECIAL_CHARS);
		$terrorist_dealings = filter_input(INPUT_POST, 'terroristDealings', FILTER_SANITIZE_SPECIAL_CHARS);

		$representative_name = filter_input(INPUT_POST, 'representative_name', FILTER_SANITIZE_SPECIAL_CHARS);
		$signature_data = filter_input(INPUT_POST, 'signature-data', FILTER_SANITIZE_SPECIAL_CHARS);
		$date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_SPECIAL_CHARS);
		$compliance_officer_name = filter_input(INPUT_POST, 'compliance_officer_name', FILTER_SANITIZE_SPECIAL_CHARS);

		/*/ Decode the base64 signature data
			$signature_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signature_data));

			// Generate a unique file name
			$signature_file = $upload_dir . uniqid() . '.png';

			// Save the image to the file system
			file_put_contents($signature_file, $signature_data);
		*/
		// Check if the record exists
		$stmt = $connect->prepare("SELECT COUNT(*) FROM corporatePart4 WHERE lawFirmId = ? AND businessId = ?");
		$stmt->execute([$lawFirmId, $client_uid]);
		$exists = $stmt->fetchColumn();

		if ($exists) {
		    // Update existing record
		    $sql = "UPDATE corporatePart4 SET 
		            politically_exposed_foreign_person = ?, potentially_exposed_to_money_laundering = ?, 
		            potentially_exposed_to_any_terrorist_act = ?, criminal_activity = ?, 
		            terrorist_association = ?, terrorist_dealings = ?, representative_name = ?, 
		            date = ?, compliance_officer_name = ? 
		            WHERE lawFirmId = ? AND businessId = ?";
		    $stmt = $connect->prepare($sql);
		    $stmt->execute([
		        $politically_exposed_foreign_person, $potentially_exposed_to_money_laundering, 
		        $potentially_exposed_to_any_terrorist_act, $criminal_activity, 
		        $terrorist_association, $terrorist_dealings, $representative_name, 
		        $date, $compliance_officer_name, $lawFirmId, $client_uid
		    ]);
		} else {
		    // Insert new record
		    $sql = "INSERT INTO corporatePart4 (
		            lawFirmId, businessId, politically_exposed_foreign_person, potentially_exposed_to_money_laundering, 
		            potentially_exposed_to_any_terrorist_act, criminal_activity, terrorist_association, terrorist_dealings, 
		            representative_name, date, compliance_officer_name) 
		            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		    $stmt = $connect->prepare($sql);
		    $stmt->execute([
		        $lawFirmId, $client_uid, $politically_exposed_foreign_person, 
		        $potentially_exposed_to_money_laundering, $potentially_exposed_to_any_terrorist_act, 
		        $criminal_activity, $terrorist_association, $terrorist_dealings, 
		        $representative_name, $date, $compliance_officer_name
		    ]);
		}


		$lawFirmEmail = getFirmEmail($lawFirmId);
		$lawFirmPhoneNumber = getFirmPhoneNumber($lawFirmId);
		
		// We also send an SMS and email to the firm
		$sql = $connect->prepare("UPDATE `lawFirmCorporateClients` SET `kyc`= '2' WHERE `lawFirmId`= ? AND `business_tpin` = ?  ");
		$sql->execute([$lawFirmId, $client_uid]);
	    echo "KYC for corporate client posted sucessfully";
	}
?>
