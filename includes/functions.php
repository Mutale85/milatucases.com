<?php 
    function Clean($string){
        return htmlspecialchars($string);
        return trim($string);
    }
    
	function checkSubscription($lawFirmId){
        global $connect;
        $stmt = $connect->prepare("SELECT tier FROM subscriptions WHERE lawFirmId = ?");
        $stmt->execute([$lawFirmId]);
        $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
        return $subscription['tier']; 
    }

    /*Encryptions fetchandDisplayEVent */

    function encryptData($data) {
        $key = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        $encrypted = base64_encode($encrypted . '::' . $iv);
        return $encrypted;
    }

    function decryptData($data) {
        list($encryptedData, $iv) = explode('::', base64_decode($data), 2);
        $key = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        // Ensure the IV is exactly 16 bytes long
        if (strlen($iv) < 16) {
            $iv = str_pad($iv, 16, "\0");
        }
        $decrypted = openssl_decrypt($encryptedData, 'aes-256-cbc', $key, 0, $iv);
        return $decrypted;
    }
   
    define('ENCRYPTION_KEY', 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789');
    function encrypt($data) {
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    function decrypt($data) {
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        
        // Ensure the IV is exactly 16 bytes
        $iv = str_pad($iv, 16, "\0");
        
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);
    }

    /*Encrypt for user ID*/

        
    function getUserIpAddr(){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    function time_ago_check($time){
        date_default_timezone_set("Africa/Lusaka");
        $time_ago   = strtotime($time);
        $current_time = time();
        $time_difference = $current_time - $time_ago;
        $seconds = $time_difference;
        //lets make tround thes into actual time.
        $minutes    = round($seconds / 60);
        $hours      = round($seconds / 3600);
        $days       = round($seconds / 86400);
        $weeks      = round($seconds / 604800); // 7*24*60*60;  
        $months     = round($seconds / 2629440); //((365+365+365+365+366)/5/12)*24*60*60  
        $years      = round($seconds / 31553280); //(365+365+365+365+366)/5 * 24 * 60 * 60

        if ($seconds <= 60) {
            return "$seconds Seconds Ago";
        }else if ($minutes <= 60) {

            if ($minutes == 1) {
                return "1 minute Ago";
            }else{
                return "$minutes minutes ago";
            }
            
        }else if ($hours <= 24) {
            if ($hours == 1) {
                return "1 hour ago";
            }else{
                return "$hours hrs ago";
            }
        }else if ($days <= 7) {
            if ($days == 1) {
                return "1 day ago";
            }else{
                return "$days days ago";
            }
        }else if ($weeks < 7) {
            if ($weeks == 1) {
            
                return "1 week ago";
            }else{
                return "$weeks Weeks ago";
            }
        }else if ($months <= 12) {
            if ($months == 1) {
                return "1 month ago";
            }else{
                return "$months Months ago";
            }
        }else {
            if ($years == 1) {
                return "One year ago";
            }else{
                return "$years years ago";
            }
        }
    }

    function passwordGenerate() {
        $alphabet = "abcdefghjkmnpqrstuwxyzABCDEFGHJKMNPQRSTUWXYZ23456789";
        $password = array(); 
        $alphabet_Length = strlen($alphabet) - 1;
        for ($i = 0; $i < 9; $i++) {
            $new = rand(0, $alphabet_Length);
            $password[] = $alphabet[$new];
        }
        return implode($password); //turn the array into a string
    }

    function generateRandomPassword($length = 8) {
        $characters = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $password .= $characters[$index];
        }
        return $password;
    }

    function get_gravatar( $email, $s = 80, $d = 'mp', $r = 'g', $img = false, $atts = array() ) {
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5( strtolower( trim( $email ) ) );
        $url .= "?s=$s&d=$d&r=$r";
        if ( $img ) {
            $url = '<img src="' . $url . '"';
            foreach ( $atts as $key => $val )
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    }


    function sendIntSMS($recipient, $content) {
        $curl = curl_init();
        $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJhdXRoLWJhY2tlbmQ6YXBwIiwic3ViIjoiNzBlYjU3OTctNWE1Ni00OTlmLWI3M2UtMzdhYWVlZmMxYzNhIn0.QS9DVHgu72nHEcOkYOGpKfx6oHqgxTxjDTDz-smq3dA"; // Replace it with your API Token
        $originator = "MilatuCases"; // Replace it with your Sender ID

        $message_obj = array(
            "channel" => "sms",
            "msg_type" => "text",
            "recipients" => array($recipient), // Single recipient as an array
            "content" => $content,
            "data_coding" => "auto"
        );

        $globals_obj = array(
            "originator" => $originator,
            "report_url" => "https://the_url_to_recieve_delivery_report.com",
        );

        $payload = json_encode(array(
            "messages" => array($message_obj),
            "message_globals" => $globals_obj
        ));

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.d7networks.com/messages/v1/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        
        return $response;
    }


    function getGravatarImage($email, $size = 80, $default = 'mp', $rating = 'g') {
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$size&d=$default&r=$rating";
        return $url;
    }
   

    function fetchCompanyName(){
        global $connect;
        $company = "";
        $sql = $connect->prepare("SELECT * FROM lawFirms WHERE firmId = ? ");
        $sql->execute([$_SESSION['parent_id']]);
        if($sql->rowCount() > 0){
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $company = $row['firmName'];
        }else{
            $company = 'MilatucCases.com';
        }
        return $company;
    }

    function fetchUserName($userId){
        global $connect;
        $userName = "";
        $sql = $connect->prepare("SELECT names FROM lawFirms WHERE id = ? ");
        $sql->execute([$userId]);
        if($sql->rowCount() > 0){
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $userName = $row['names'];
        }else{
            $userName = 'MilatucCases.com';
        }
        return $userName;
    }

    

    function sendSMS($api_key, $sender_id, $contacts, $message) {
        $response = "";
      	$lawFirmId = $_SESSION['parent_id'];
      	$sub = checkSubscription($lawFirmId);
        if($sub == 'trial'){
           
           $response = "SMS is restricted to paid up clients"; 
            
        }else{
            $url = 'https://bulksms.zamtel.co.zm/api/v2.1/action/send/api_key/' . $api_key . '/contacts/' . $contacts . '/senderId/' . $sender_id . '/message/' . urlencode($message);

            $ch = curl_init($url);

            // Set cURL options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification

            // Execute cURL session dash
            $response = curl_exec($ch);

            // Check if any error occurred
            if (curl_errno($ch)) {
                echo 'cURL Error: ' . curl_error($ch);
            }

            // Close cURL session
            curl_close($ch);

            
        }
        return $response;
    }

    function getFirmEmail($firmId) {
        global $connect;
        $stmt = $connect->prepare("SELECT email FROM lawFirms WHERE firmId = ?");
        $stmt->execute([$firmId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['email'] : null;
    }

    // Function to fetch phone number by firmId company_info
    function getFirmPhoneNumber($firmId) {
        global $connect;
        $stmt = $connect->prepare("SELECT phonenumber FROM lawFirms WHERE firmId = ?");
        $stmt->execute([$firmId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['phonenumber'] : null;
    }

    function fetchLawFirmUserEmail($userId, $lawFirmId) {
        global $connect;
        $stmt = $connect->prepare("SELECT email FROM lawFirms WHERE id = ? AND firmId = ?");
        $stmt->execute([$userId, $lawFirmId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['email'] : null;
    }

    function fetchLawFirmUserName($userId, $lawFirmId) {
        global $connect;
        $stmt = $connect->prepare("SELECT names FROM lawFirms WHERE id = ? AND firmId = ?");
        $stmt->execute([$userId, $lawFirmId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['names'] : null;
    }

    // Function to fetch phone number by firmId company_info
    function fetchLawFirmUserPhone($userId, $lawFirmId) {
        global $connect;
        $stmt = $connect->prepare("SELECT phonenumber FROM lawFirms WHERE id = ? AND firmId = ?");
        $stmt->execute([$userId, $lawFirmId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['phonenumber'] : null;
    }

    function lawFirmClientNameById($clientId, $lawFirmId) {
        global $connect;
        $output = "";
        $stmt = $connect->prepare("SELECT * FROM lawFirmClients WHERE id = ? AND lawFirmId = ? ");
        $stmt->execute([$clientId, $lawFirmId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row['client_type'] == "Corporate"){
            $output = $row['business_name'];
        }else{
            $output = decrypt($row['client_names']);
        }
        return $output;
    }


    function getClientTpinByCaseId($caseId, $lawFirmId) {
        global $connect;
        $stmt = $connect->prepare("SELECT client_tpin FROM cases WHERE id = ? AND lawFirmId = ?  ");
        $stmt->execute([$caseId, $lawFirmId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['client_tpin'] : null;
    }

    function getClientIdByCaseId($caseId, $lawFirmId) {
        global $connect;
        $stmt = $connect->prepare("SELECT clientId FROM cases WHERE id = ? AND lawFirmId = ?  ");
        $stmt->execute([$caseId, $lawFirmId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['clientId'] : null;
    }

    function getClientHourlyRateByCaseId($caseId, $lawFirmId) {
        global $connect;
        $stmt = $connect->prepare("SELECT hourly_rate FROM cases WHERE id = ? AND lawFirmId = ?  ");
        $stmt->execute([$caseId, $lawFirmId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['hourly_rate'] : null;
    }

    function getClientcaseNoByCaseId($caseId, $lawFirmId) {
        global $connect;
        $stmt = $connect->prepare("SELECT caseNo FROM cases WHERE id = ? AND lawFirmId = ?  ");
        $stmt->execute([$caseId, $lawFirmId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['caseNo'] : null;
    }

    function getClientCurrencyByCaseId($caseId, $lawFirmId) {
        global $connect;
        $stmt = $connect->prepare("SELECT currency FROM cases WHERE id = ? AND lawFirmId = ?  ");
        $stmt->execute([$caseId, $lawFirmId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['currency'] : null;
    }

     
    function getClientNameByTPIN($lawFirmId, $client_tpin) {
        global $connect;
        $stmt = $connect->prepare("SELECT * FROM `lawFirmClients` WHERE `lawFirmId` = ? AND `client_tpin` = ? ");
        $stmt->execute([$lawFirmId, $client_tpin]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            if ($row['client_type'] == 'Corporate') {
                return $row['business_name'];
            } else {
                return decrypt($row['client_name']);
            }
        } else {
            return null;
        }
    }
    

    function getClientNameById($clientId, $lawFirmId) {
        global $connect;

        try {
            $stmt = $connect->prepare("SELECT * FROM `lawFirmClients` WHERE id = ? AND `lawFirmId` = ? ");
            $stmt->execute([$clientId, $lawFirmId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                if ($row['client_type'] == 'Corporate') {
                    return $row['business_name'];
                } else {
                    return decrypt($row['client_names']);
                }
            } else {
                return null;
            }

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    function getClientTpinById($clientId, $lawFirmId) {
        global $connect;

        try {
            $stmt = $connect->prepare("SELECT * FROM `lawFirmClients` WHERE id = ? AND `lawFirmId` = ? ");
            $stmt->execute([$clientId, $lawFirmId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                
                return decrypt($row['client_tpin']);
                
            } else {
                return null;
            }

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }
    


    function fetchLawFirmClients() {
        global $connect;
        $sql = $connect->prepare("SELECT * FROM lawFirmClients WHERE lawFirmId = ? AND archived = 0 ");
        $sql->execute([$_SESSION['parent_id']]);
        $clients = $sql->fetchAll(PDO::FETCH_ASSOC);

        foreach ($clients as $client): 
            $clientType = $client['client_type'];
            $clientTpin = decrypt($client['client_tpin']);
            $kyc = $client['kyc'];
            $clientId = $client['id'];
            $names = decrypt($client['client_names']);
            $email = decrypt($client['client_email']);
            $phone = decrypt($client['client_phone']);
            $lawFirmId = $client['lawFirmId'];
            $bName = $client['business_name'];
            
            // Determine KYC status message
            if ($kyc == '0') {
                $action = '<small><span class="badge bg-label-danger me-1">Unsent</span></small>';
            } elseif ($kyc == '1') {
                $action = '<small><span class="badge bg-label-success me-1">Sent</span></small>';
            } elseif ($kyc == '2') {
                $action = '<small><a href="'.($clientType === 'Corporate' ? 'cc/kyccorporate?cc=' : 'cc/kycindividual?cc=').base64_encode($clientId).'"><span class="badge bg-label-primary me-1">Received HERE </span></a></small>';
            }

            ?>
            <tr>
                <td>
                    <?php if ($clientType === 'Corporate'): ?>
                        <a href="crm/?clientId=<?php echo base64_encode($clientId)?>"><?php echo $clientType; ?></a>
                    <?php else: ?>
                        <a href="crm/?clientId=<?php echo base64_encode($clientId)?>"><?php echo $clientType; ?></a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($clientType === 'Corporate'): ?>
                        <a href="crm/?clientId=<?php echo base64_encode($clientId)?>"><?php echo "$bName <small>($names)</small> " ?></a>
                    <?php else: ?>
                        <a href="crm/?clientId=<?php echo base64_encode($clientId)?>"><?php echo $names; ?></a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo $action; ?>
                </td>
                <td>
                    <a href="<?php echo $clientType === 'Corporate' ? 'cc/corporate-manual-kyc' : 'cc/individual-manual-kyc'; ?>?clientId=<?php echo base64_encode($clientId); ?>&email=<?php echo base64_encode($email); ?>&names=<?php echo base64_encode($names); ?>&firm=<?php echo base64_encode($lawFirmId); ?>&tpin=<?php echo base64_encode($clientTpin); ?>&busiName=<?php echo ($clientType === 'Corporate' ? $bName : ''); ?>"> Add KYC </a>
                </td>
                <?php if ($_SESSION['user_role'] === 'superAdmin' || $_SESSION['userJob'] == 'Admin Officer' || $_SESSION['userJob'] == 'Secretary'): ?>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-primary btn-sm editClient" href="#" data-id="<?php echo encrypt($clientId); ?>"><i class="bi bi-pen"></i> Edit</button>
                        <button class="btn btn-danger btn-sm removeClient" href="#" data-id="<?php echo encrypt($clientId); ?>"><i class="bi bi-trash2"></i> Remove</button>
                    </div>
                </td>
                <?php else: ?>
                    <td>---</td>
                <?php endif; ?>
                <td>
                    <button class="btn btn-dark btn-sm sendKYC" 
                        data-id="<?php echo encrypt($clientId); ?>" 
                        data-email="<?php echo encrypt($email); ?>" 
                        data-names="<?php echo $names; ?>" 
                        data-firm="<?php echo encrypt($lawFirmId); ?>" 
                        data-tpin="<?php echo encrypt($Tpin); ?>" 
                        data-bname="<?php echo $clientType === 'Corporate' ? $bName : ''; ?>" 
                        data-type="<?php echo $clientType; ?>"> 
                        <i class="bi bi-send"></i> Send KYC
                    </button>
                </td>
            </tr>
            <?php endforeach;
    }

    function fetchLawFirmCorporateClients() {
        global $connect;
        $sql = $connect->prepare("SELECT * FROM lawFirmClients WHERE lawFirmId = ? AND client_type = 'Corporate' AND archived = 0");
        $sql->execute([$_SESSION['parent_id']]);
        $clients = $sql->fetchAll(PDO::FETCH_ASSOC);

        foreach ($clients as $client): 
            $clientTpin = decrypt($client['client_tpin']);
            $kyc = $client['kyc'];
            $clientId = $client['id'];
            $names = decrypt($client['client_names']);
            $email = decrypt($client['client_email']);
            $lawFirmId = $client['lawFirmId'];
            $bName = $client['business_name'];
            
            // Determine KYC status message
            if ($kyc == '0') {
                $action = '<small><span class="badge bg-label-danger me-1">Unsent</span></small>';
            } elseif ($kyc == '1') {
                $action = '<small><span class="badge bg-label-success me-1">Sent</span></small>';
            } elseif ($kyc == '2') {
                $action = '<small><a href="cc/kyccorporate?cc=' . base64_encode($clientId) . '"><span class="badge bg-label-primary me-1">Received HERE </span></a></small>';
            }
            ?>
            <tr>
                <td>
                    <a href="crm/?clientId=<?php echo base64_encode($clientId)?>"><?php echo "$bName - <small>($names)</small> " ?></a>
                </td>
                <td>
                    <?php echo $action; ?>
                </td>
                <td>
                    <a href="cc/corporate-manual-kyc?clientId=<?php echo base64_encode($clientId); ?>&email=<?php echo base64_encode($email); ?>&names=<?php echo base64_encode($names); ?>&firm=<?php echo base64_encode($lawFirmId); ?>&tpin=<?php echo base64_encode($clientTpin); ?>&busiName=<?php echo $bName; ?>"> Add KYC </a>
                </td>
                <?php if ($_SESSION['user_role'] === 'superAdmin' || $_SESSION['userJob'] == 'Admin Officer' || $_SESSION['userJob'] == 'Secretary'): ?>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-primary btn-sm editClient" href="#" data-id="<?php echo encrypt($clientId); ?>"><i class="bi bi-pen"></i> Edit</button>
                        <button class="btn btn-danger btn-sm removeClient" href="#" data-id="<?php echo $clientId; ?>"><i class="bi bi-trash2"></i> Remove</button>
                    </div>
                </td>
                <?php else: ?>
                    <td>---</td>
                <?php endif; ?>
                <td>
                    <button class="btn btn-dark btn-sm sendKYC" 
                        data-id="<?php echo encrypt($clientId); ?>" 
                        data-email="<?php echo encrypt($email); ?>" 
                        data-names="<?php echo $names; ?>" 
                        data-firm="<?php echo encrypt($lawFirmId); ?>" 
                        data-tpin="<?php echo encrypt($clientTpin); ?>" 
                        data-bname="<?php echo $bName; ?>" 
                        data-type="Corporate"> 
                        <i class="bi bi-send"></i> Send KYC
                    </button>
                </td>
            </tr>
            <?php 
        endforeach;
    }

    function fetchLawFirmIndividualClients() {
        global $connect;
        $sql = $connect->prepare("SELECT * FROM lawFirmClients WHERE lawFirmId = ? AND client_type = 'Individual' AND archived = 0");
        $sql->execute([$_SESSION['parent_id']]);
        $clients = $sql->fetchAll(PDO::FETCH_ASSOC);

        foreach ($clients as $client): 
            $clientTpin = decrypt($client['client_tpin']);
            $kyc = $client['kyc'];
            $clientId = $client['id'];
            $names = decrypt($client['client_names']);
            $email = decrypt($client['client_email']);
            $lawFirmId = $client['lawFirmId'];
            
            // Determine KYC status message
            if ($kyc == '0') {
                $action = '<small><span class="badge bg-label-danger me-1">Unsent</span></small>';
            } elseif ($kyc == '1') {
                $action = '<small><span class="badge bg-label-success me-1">Sent</span></small>';
            } elseif ($kyc == '2') {
                $action = '<small><a href="cc/kycindividual?cc=' . base64_encode($clientId) . '"><span class="badge bg-label-primary me-1">Received HERE </span></a></small>';
            }
            ?>
            <tr>
                <td>
                    <a href="crm/?clientId=<?php echo base64_encode($clientId)?>"><?php echo $names; ?></a>
                </td>
                <td>
                    <?php echo $action; ?>
                </td>
                <td>
                    <a href="cc/individual-manual-kyc?clientId=<?php echo base64_encode($clientId); ?>&email=<?php echo base64_encode($email); ?>&names=<?php echo base64_encode($names); ?>&firm=<?php echo base64_encode($lawFirmId); ?>&tpin=<?php echo base64_encode($clientTpin); ?>"> Add KYC </a>
                </td>
                <?php if ($_SESSION['user_role'] === 'superAdmin' || $_SESSION['userJob'] == 'Admin Officer' || $_SESSION['userJob'] == 'Secretary'): ?>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-primary btn-sm editClient" href="#" data-id="<?php echo encrypt($clientId); ?>"><i class="bi bi-pen"></i> Edit</button>
                        <button class="btn btn-danger btn-sm removeClient" href="#" data-id="<?php echo encrypt($clientId); ?>"><i class="bi bi-trash2"></i> Remove</button>
                    </div>
                </td>
                <?php else: ?>
                    <td>---</td>
                <?php endif; ?>
                <td>
                    <button class="btn btn-dark btn-sm sendKYC" 
                        data-id="<?php echo encrypt($clientId); ?>" 
                        data-email="<?php echo encrypt($email); ?>" 
                        data-names="<?php echo $names; ?>" 
                        data-firm="<?php echo encrypt($lawFirmId); ?>" 
                        data-tpin="<?php echo encrypt($clientTpin); ?>" 
                        data-type="Individual"> 
                        <i class="bi bi-send"></i> Send KYC
                    </button>
                </td>
            </tr>
            <?php 
        endforeach;
    }

    // ====================================== end of clients data =================================

    function fetchCaseStatus($caseId) {
        global $connect;

        try {
            // Check in the case_status table first
            $query = $connect->prepare("SELECT case_status FROM case_status WHERE caseId = ? AND lawFirmId = ?");
            $query->execute([$caseId, $_SESSION['parent_id']]);
            $result = $query->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return $result['case_status'];
            } else {
                // If not found, check in the cases table
                $query = $connect->prepare("SELECT caseStatus FROM cases WHERE id = ? AND lawFirmId = ?");
                $query->execute([$caseId, $lawFirmId]);
                $result = $query->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    return $result['caseStatus'];
                } else {
                    return 'Case status not found';
                }
            }
        } catch (PDOException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }


    /*======== CASE DETAILS =============*/

  
    function getCaseDetails($caseId, $lawFirmId) {
        global $connect;
        $query = $connect->prepare("SELECT * FROM `cases` WHERE id = ? AND lawFirmId = ?");
        $query->execute([$caseId, $lawFirmId]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }


    function displayFeeMethod($feeMethod, $fixedFee, $hourlyRate) {
        if ($feeMethod != 'Hourly Rate') {
            return "
                <tr>
                    <th>" . ucwords($feeMethod) . "</th>
                    <td align='right'>{$fixedFee}</td>
                </tr>
            ";
        } else {
            return "
                <tr>
                    <th>HOURLY RATE</th>
                    <td align='right'>{$hourlyRate}</td>
                </tr>
            ";
        }
    }

    function displayActionButtons($clientId, $caseNo, $caseId) {
        return "";
    }

    function displayCaseDetails($row, $caseId) {
        $feeMethod = displayFeeMethod($row['feeMethod'], $row['fixedFee'], $row['hourly_rate']);
        $actionButtons = displayActionButtons($row['clientId'], $row['caseNo'], $caseId);
        $caseDescription = nl2br(html_entity_decode(decrypt($row['caseDescription'])));  // Use nl2br to convert newlines to <br> tags

        return "
            <div class='card-header border-bottom'>
                <div class='text-left mb-3'>
                    {$actionButtons}
                </div>
            </div>
            <div class='card-body' id='reading-matter'>
                <div class='table table-responsive mb-3'>
                    <table class='table table-borderless'>                        
                        <tr>
                            <td>Matter Status</td>
                            <td align='right'>" . htmlspecialchars($row['caseStatus']) . "</td>
                        </tr>
                        <tr>
                            <td>Matter Date</td>
                            <td align='right'>" . date("D d M, Y", strtotime($row['caseDate'])) . "</td>
                        </tr>
                        <tr>
                            <td>Currency</td>
                            <td align='right'>" . htmlspecialchars($row['currency']) . "</td>
                        </tr>
                        {$feeMethod}
                        <tr>
                            <td>Reponsible</td>
                            <td align='right'><small>" . fetchCaseLawyers($row['caseNo']) . "</small></td>
                        </tr>
                    </table>
                </div>
                <div class='case-description mb-3 border-bottom pt-4'>
                    <h5 class='text-primary'><strong>Matter Description</strong></h5>
                    <p>{$caseDescription}</p>
                </div>
            </div>
        ";
    }

    function displayCaseDetailsById($caseId, $lawFirmId) {
        $row = getCaseDetails($caseId, $lawFirmId);

        if ($row && userHasAccessToCase($_SESSION['user_id'], $caseId, $_SESSION['parent_id'])) {
            echo displayCaseDetails($row, $caseId);
        } else {
            echo "<p class='mt-2'>You have no access to this case or the case doesn't exist.</p>";
        }
    }

    function displayFolders($caseId, $lawFirmId) {
        global $connect;
        $query = $connect->prepare("SELECT * FROM caseFolders WHERE caseId = ? AND lawFirmId = ? AND archived = '0' ");
        $query->execute([$caseId, $lawFirmId]);
        $folders = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($folders as $folder) {
            $files = countDocumentinCaseFolder($caseId, $folder['id']);
            $folderName = $folder['folder_name'];
            $folderId = $folder['id'];
            echo "<tr>";
            echo "<td>" . $folder['basename'] . " (".$files.") </td>";
            echo "<td>" . fetchUserName($folder['uploaded_by']) . "</td>";
            echo "<td>" . time_ago_check($folder['created_at']) . "</td>";
            echo "<td>";
             echo "<a href='cases/folder?folderName=$folderName&folderId=".base64_encode($folderId)."&caseId=".base64_encode($caseId)."' class='edit-folder' data-id='" . $folderId . "'>Open</a> | ";
            echo "<a href='#' class='edit-folder' data-id='" . $folderId . "'>Edit</a> | ";
            echo "<a href='#' class='delete-folder' data-id='" . $folderId . "'>Delete</a> | ";
            echo "<a href='#' class='archive-folder' data-id='" . $folderId . "'>Archive</a>";
            echo "</td>";
            echo "</tr>";
        }
    }

    function fetchCaseFolders($caseId, $lawFirmId) {
        global $connect;
        $query = $connect->prepare("SELECT * FROM caseFolders WHERE caseId = ? AND lawFirmId = ? AND archived = '0' ");
        $query->execute([$caseId, $lawFirmId]);
        $folders = $query->fetchAll(PDO::FETCH_ASSOC);
        return $folders;
    }

    function fetchCaseFolderNameById($folderId) {
        global $connect;
        $query = $connect->prepare("SELECT * FROM caseFolders WHERE id = ? AND archived = '0' ");
        $query->execute([$folderId]);
        $folder = $query->fetch(PDO::FETCH_ASSOC);
        return $folder['folder_name'];
    }

    function getCaseFolderNameById($folderId) {
        global $connect;
        $query = $connect->prepare("SELECT * FROM caseFolders WHERE id = ? AND archived = '0' ");
        $query->execute([$folderId]);
        $folder = $query->fetch(PDO::FETCH_ASSOC);
        return $folder['basename'];
    }
  

    function fetchCaseDocuments($caseId, $caseNo) {
        global $connect;
        $query = $connect->prepare("SELECT `id`, `documentName` FROM `caseDocuments` WHERE `caseId` = ? AND `caseNo` = ?");
        $query->execute([$caseId, $caseNo]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }


    function displayCaseDocuments($caseId, $caseNo) {
        $folders = fetchCaseFolders($caseId, $_SESSION['parent_id']);
        // Add upload more documents form
        echo "<form id='uploadDocumentsForm' enctype='multipart/form-data'>";
            echo "<div class='mb-3'>";
            echo "<label for='uploadFiles' class='form-label'>Upload More Documents</label>";
            echo "<input class='form-control' type='file' id='uploadFiles' name='documents[]' multiple>";
            echo "</div>";

        // Add the folder checkboxes
        if (!empty($folders)) {
            echo "<div class='mb-3'>";
            echo "<label class='form-label'>Select Folder To Add Documents To:</label>";
            foreach ($folders as $folder) {
                $folderId = $folder['id'];
                $folderName = $folder['basename'];
                echo "<div class='form-check'>";
                echo "<input class='form-check-input' type='radio' value='{$folderId}' id='folder_{$folderId}' name='folder'>";
                echo "<label class='form-check-label' for='folder_{$folderId}'>{$folderName}</label>";
                echo "</div>";
            }
            echo "</div>";
        }

        echo "<input type='hidden' name='newcaseId' value='{$caseId}'>";
        echo "<input type='hidden' name='newcaseNo' value='{$caseNo}'>";
        echo "<button type='submit' class='btn btn-primary' id='uploadMoreBtn'>Upload</button>";
        echo "</form>";
    }

    function fetchCasePostedDocuments($caseId, $lawFirmId){
        global $connect;
        $query = $connect->prepare("SELECT * FROM `caseDocuments` WHERE `caseId` = ? AND `lawFirmId` = ? ");
        $query->execute([$caseId, $lawFirmId]);
        $documents = $query->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($documents)) {
            foreach ($documents as $document) {
                $docId = htmlspecialchars($document['id']);
                $docName = htmlspecialchars($document['documentName']);
                $docUrl = $document['documentName'];
                $folderId = $document['folderId'];
                if($folderId === null){
                    $folderName = 'caseDocuments';
                    $location = "$folderName/$docUrl";
                ?>
                   <tr>
                        <td><input type='checkbox' class='fileCheckbox' value='<?php echo $docId?>'></td>
                        <td><?php echo $docName?></td>
                        <td><?php echo htmlspecialchars(fetchUserName($document['userId'])) ?></td>
                        <td><?php echo htmlspecialchars(time_ago_check($document['date_added'])) ?></td>
                        <td>
                            <div class='dropdown'>
                                <button class='btn btn-secondary btn-sm dropdown-toggle' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
                                    <i class='bi bi-three-dots-vertical'></i>
                                </button>
                                <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                                    <li><a class='dropdown-item' href='<?php echo $folderName?>/<?php echo $docUrl ?>' target='_blank'><i class='bi bi-circle'></i> Open</a></li>
                                    <li><button class='dropdown-item remove-file' id="<?php echo htmlspecialchars($docId) ?>" data-id="<?php echo $docId?>"><i class='bi bi-trash2'></i> Delete</button></li>
                                    <?php if (!in_array(pathinfo($docUrl, PATHINFO_EXTENSION), ['doc', 'docx'])): ?>
                                        <li><button class='dropdown-item previewFile' data-file='<?= htmlspecialchars($location) ?>'><i class='bi bi-view-list'></i> Preview</button></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </td>                
                   </tr>
            <?php
                }else{
                    $folderName = fetchCaseFolderNameById($folderId);
                    $folderName = "cases/$folderName";
                }
            }
        }
    }

    // milatucases@milatucase.iam.gserviceaccount.com
    // 575658105346-jvcljbinqcchm3fcm70ho98unt066alr.apps.googleusercontent.com

    function countDocumentinCaseFolder($caseId, $folderId){
        global $connect;
        $query = $connect->prepare("SELECT * FROM caseDocuments WHERE caseId = ? AND folderId = ?");
        $query->execute([$caseId, $folderId]);
        $count = $query->rowCount();
        return $count;
    }

    function countDocumentsinCase($caseId, $lawFirmId){
        global $connect;
        $query = $connect->prepare("SELECT * FROM caseDocuments WHERE caseId = ? AND lawFirmId = ?");
        $query->execute([$caseId, $lawFirmId]);
        $count = $query->rowCount();
        return $count;
    }


    function fetchCaseFolderFiles($folderId){
        global $connect;
        $folderName = fetchCaseFolderNameById($folderId);
        $stmt = $connect->prepare("SELECT * FROM caseDocuments WHERE folderId = ? AND lawFirmId = ? ");
        $stmt->execute([$folderId, $_SESSION['parent_id']]);
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($files as $file): 
            $documentName = $file['documentName'];
            $location = "$folderName/$documentName";
            $documentId = $file['id'];
        ?>
            <tr>
                <td><i class='bi bi-file'></i> <?php echo htmlspecialchars($documentName) ?></td>
                <td><?php echo htmlspecialchars(fetchUserName($file['userId'])) ?></td>
                <td><?php echo htmlspecialchars(time_ago_check($file['date_added'])) ?></td>
                <td>
                    <div class='dropdown'>
                        <button class='btn btn-secondary btn-sm dropdown-toggle' type='button' id='dropdownMenuButton' data-bs-toggle='dropdown' aria-expanded='false'>
                            <i class='bi bi-three-dots-vertical'></i>
                        </button>
                        <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton'>
                            <li><a class='dropdown-item' href='cases/<?php echo $folderName?>/<?php echo $documentName ?>' target='_blank'><i class='bi bi-circle'></i> Open</a></li>
                            <li><button class='dropdown-item deleteCaseFolderFile' id="<?php echo htmlspecialchars($documentName) ?>" data-id="<?php echo $documentId?>"><i class='bi bi-trash2'></i> Delete</button></li>
                            <?php if (!in_array(pathinfo($documentName, PATHINFO_EXTENSION), ['doc', 'docx'])): ?>
                                <li><button class='dropdown-item previewFile' data-file='<?= htmlspecialchars($location) ?>'><i class='bi bi-view-list'></i> Preview</button></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach;
    }
    
  /*============================ END OF CASES ==============================================================================*/ 

    function fetchLawFirmMembers($firmId) {
        global $connect;
        $stmt = $connect->prepare("SELECT * FROM `lawFirms` WHERE `firmId` = ? OR `parentId` = ?");
        $stmt->execute([$firmId, $firmId]);
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $members;
    }

    function fetchLawFirmMemberNames($userId) {
        global $connect;
        $stmt = $connect->prepare("SELECT names FROM `lawFirms` WHERE `id` = ? AND `parentId` = ?");
        $stmt->execute([$userId, $_SESSION['parent_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row){
            return $row['names'];
        }else{
            return null;
        }
    }

    function fetchLawFirmMemberEmail($userId) {
        global $connect;
        $stmt = $connect->prepare("SELECT email FROM `lawFirms` WHERE `id` = ? AND `parentId` = ?");
        $stmt->execute([$userId, $_SESSION['parent_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row){
            return $row['email'];
        }else{
            return null;
        }
    }

    function fetchLawFirmMemberPhone($userId) {
        global $connect;
        $stmt = $connect->prepare("SELECT phonenumber FROM `lawFirms` WHERE `id` = ? AND `parentId` = ?");
        $stmt->execute([$userId, $_SESSION['parent_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row){
            return $row['phonenumber'];
        }else{
            return null;
        }
    }


    function fetchCaseId($caseNo) {
        global $connect;
        $query = $connect->prepare("SELECT id FROM cases WHERE caseNo = ? AND lawFirmId = ? ");
        $query->execute([$caseNo, $_SESSION['parent_id']]);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if($row){
            return $row['id'];
        }else{
            return null;
        }
    }

    function fetchCaseNoById($caseId) {
        global $connect;
        $query = $connect->prepare("SELECT caseNo FROM cases WHERE id = ? AND lawFirmId = ? ");
        $query->execute([$caseId, $_SESSION['parent_id']]);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if($row){
            return $row['caseNo'];
        }else{
            return null;
        }
    }


    function fetchCaseLawyers($caseNo) {
        global $connect;
        $output = [];
        
        $query = $connect->prepare("SELECT lawyerId FROM case_access WHERE caseNo = ? AND lawFirmId = ?");
        $query->execute([$caseNo, $_SESSION['parent_id']]);
        
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $output[] = fetchLawFirmMemberNames($row['lawyerId']);
        }
        
        return implode(', ', $output);
    }

    function nameToAcronym($name) {
        $words = explode(' ', $name);
        $acronym = '';
        
        foreach ($words as $word) {
            $acronym .= strtoupper(substr($word, 0, 1));
        }
        
        return $acronym;
    }


    function fetchCaseLayersAsAcronyms($caseId) {
        global $connect;
        $output = [];
        
        $query = $connect->prepare("SELECT lawyerId FROM case_access WHERE caseId = ? AND lawFirmId = ?");
        $query->execute([$caseId, $_SESSION['parent_id']]);
        
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $fullName = fetchLawFirmMemberNames($row['lawyerId']);
            $acronym = nameToAcronym($fullName);
            $output[] = "<a href='javascript:;' title=\"$fullName\">$acronym</a>";
        }
        
        return implode(', ', $output);
    }

    function fetchCaseLayersAsFullNames($caseId) {
        global $connect;
        $output = [];
        
        $query = $connect->prepare("SELECT lawyerId FROM case_access WHERE caseId = ? AND lawFirmId = ?");
        $query->execute([$caseId, $_SESSION['parent_id']]);
        
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $fullName = fetchLawFirmMemberNames($row['lawyerId']);
            $acronym = $fullName;
            $output[] = "<a href='javascript:;' title=\"$fullName\">$acronym</a><br>";
        }
        
        return implode(' ', $output);
    }


    function userHasAccessToCase($lawyerId, $caseId, $lawFirmId) {
        global $connect;
        
        $query = $connect->prepare("
            SELECT COUNT(*) 
            FROM case_access 
            WHERE lawyerId = ? AND caseId = ? AND lawFirmId = ?
        ");
        $query->execute([$lawyerId, $caseId, $lawFirmId]);
        
        return $query->fetchColumn() > 0;
    }

    function countFeeNotesAdded($caseId, $client_tpin) {
        global $connect;
        $lawFirmId = $_SESSION['parent_id']; // assuming the law firm ID is stored in the session
        $sql = $connect->prepare("SELECT COUNT(*) FROM `timer_logs` WHERE case_id = ? AND client_tpin = ? AND lawFirmId = ?");
        $sql->execute([$caseId, $client_tpin, $lawFirmId]);
        $count = $sql->fetchColumn();
        return $count;
    }

    function clientCases($clientId) {
        global $connect;
        $lawFirmId = $_SESSION['parent_id'];
        $sql = $connect->prepare("SELECT COUNT(*) FROM `cases` WHERE clientId = ? AND lawFirmId = ?");
        $sql->execute([$clientId, $lawFirmId]);
        $count = $sql->fetchColumn();
        return $count;
    }

    function clientsTpinByCaseId($caseId) {
        global $connect;
        $lawFirmId = $_SESSION['parent_id'];
        $sql = $connect->prepare("SELECT `client_tpin` FROM `cases` WHERE id = ? AND lawFirmId = ?");
        $sql->execute([$caseId, $lawFirmId]);
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        if($row){
            return $row['client_tpin'];
        }
    }

    function clientIdByCaseId($caseId) {
        global $connect;
        $lawFirmId = $_SESSION['parent_id'];
        $sql = $connect->prepare("SELECT `clientId` FROM `cases` WHERE id = ? AND lawFirmId = ?");
        $sql->execute([$caseId, $lawFirmId]);
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        if($row){
            $clientName =  getClientNameById($row['clientId'], $lawFirmId);
            return $clientName;
        }
    }

    function fetchClientsTpinById($clientId, $lawFirmId) {
        global $connect;
        $sql = $connect->prepare("SELECT `client_tpin` FROM `lawFirmClients` WHERE id = ? AND lawFirmId = ?");
        $sql->execute([$clientId, $lawFirmId]);
        $row = $sql->fetch(PDO::FETCH_ASSOC);
        if($row){
            return decrypt($row['client_tpin']);
        }
    }


    function fetchCLientIdCaseId($caseId) {
        global $connect;
        $lawFirmId = $_SESSION['parent_id'];
        $sql = $connect->prepare("SELECT clientId FROM `cases` WHERE id = ? AND lawFirmId = ?");
        $sql->execute([$caseId, $lawFirmId]);

        $row = $sql->fetch(PDO::FETCH_ASSOC);

        if($row){
            return $row['clientId'];
        }
    }


    /*===================== Law Firm Information  ======================*/
    function fetchCompanyInfoByLawFirmId($lawFirmId) {
        global $connect;
        try {
            $stmt = $connect->prepare("SELECT * FROM company_info WHERE lawFirmId = ?");
            $stmt->execute([$lawFirmId]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                echo '<div class="card-header bg-light">';
                echo '<h3 class="text-center">' . htmlspecialchars($data['company_name']) . '</h3>';
                echo '<p class="text-center mb-0">';
                echo nl2br(htmlspecialchars_decode($data['address'])) . '<br>'; // Decode HTML entities for line breaks
                echo 'Postal Code: ' . htmlspecialchars($data['postal_code']) . '<br>';
                echo 'Tel: ' . htmlspecialchars($data['telephone']) . '<br>';
                echo 'Email: ' . htmlspecialchars($data['email']);
                echo '</p>';
                if (!empty($data['logo'])) {
                    echo '<div class="text-center mt-3">';
                    echo '<img src="settings/' . htmlspecialchars($data['logo']) . '" alt="Company Logo" class="img-fluid" width="100">';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<div class="card-header bg-light">';
                echo '<p class="text-center mb-0">No data found.</p>';
                echo '</div>';
            }
        } catch (PDOException $e) {
            echo '<div class="card-header bg-light">';
            echo '<p class="text-center mb-0">Database error: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
        }
    }


    function fetchyLawFirmNameByID($lawFirmId) {
        global $connect;
        
        $query = $connect->prepare("SELECT * FROM lawFirms WHERE firmId = ?");
        $query->execute([$lawFirmId]);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $output = "";
        if($query->rowCount() > 0){
            $output = htmlspecialchars($row['firmName']);
        }else{
            $output = "Law Firm";
        }         
        return $output;
    }

    /*
    function fetchClientInfoById($clientId) {
        global $connect;

        try {
            // Prepare the query to fetch client information by TPIN
            $stmt = $connect->prepare("
                SELECT 
                    *
                FROM lawFirmClients
                WHERE id = ? AND lawFirmId = ?
            ");
            $stmt->execute([$clientId, $_SESSION['parent_id']]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                // Determine the client type and display relevant information
                if ($data['client_type'] === 'corporate') {
                    // Corporate client
                    echo '<p><strong>Attention: ' . html_entity_decode($data['business_name']) . '</strong></p>';
                } else {
                    // Individual client
                    echo '<p><strong>Attention: ' . html_entity_decode(decrypt($data['client_names'])) . '</strong></p>';
                }
                
                echo '<p><strong>Email: ' . html_entity_decode(decrypt($data['client_email'])) . '</strong></p>';
                echo '<p><strong>Phone: ' . html_entity_decode(decrypt($data['client_phone'])) . '</strong></p>';
                
            } else {
                echo '<p>No data found for the provided TPIN.</p>';
            }
        } catch (PDOException $e) {
            echo '<p>Database error: ' . html_entity_decode($e->getMessage()) . '</p>';
        }
    }
    */

    function fetchClientInfoById($clientId) {
        global $connect, $lawFirmId;
        
        try {
            $stmt = $connect->prepare("SELECT * FROM lawFirmClients WHERE id = ? AND lawFirmId = ?");
            $stmt->execute([$clientId, $lawFirmId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                if ($row['client_type'] === 'Corporate') {
                    $output = [
                        'name' => html_entity_decode($row['business_name']),
                        'email' => html_entity_decode(decrypt($row['client_email'])),
                        'Phone' => html_entity_decode(decrypt($row['client_phone'])),
                        'address' => html_entity_decode(decrypt($row['address'])),
                        'client_tpin' => html_entity_decode(decrypt($row['client_tpin']))
                    ];
                } else {
                    $output = [
                        'name' => html_entity_decode(decrypt($row['client_names'])),
                        'email' => html_entity_decode(decrypt($row['client_email'])),
                        'Phone' => html_entity_decode(decrypt($row['client_phone'])),
                        'address' => html_entity_decode(decrypt($row['address'])),
                        'client_tpin' => html_entity_decode(decrypt($row['client_tpin']))
                    ];
                }
                return $output;
            } else {
                return ['error' => 'No data found for the provided Id.'];
            }
        } catch (PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    function displayClientInfo($clientInfo) {
        if (isset($clientInfo['error'])) {
            return "<div class='alert alert-danger'>{$clientInfo['error']}</div>";
        }

        $html = "<div class='card mb-3 shadow-none'>";
        $html .= "<div class='card-header'>";
        $html .= "<h5 class='card-title mb-0'>" . htmlspecialchars($clientInfo['name']) . "</h5>";
        $html .= "</div>";
        $html .= "<div class='card-body'>";
        $html .= "<ul class='list-group list-group-flush'>";
        $html .= "<li class='list-group-item'><strong>Tpin:</strong> " . htmlspecialchars($clientInfo['client_tpin']) . "</li>";
        $html .= "<li class='list-group-item'><strong>Email:</strong> " . htmlspecialchars($clientInfo['email']) . "</li>";
        $html .= "<li class='list-group-item'><strong>Phone:</strong> " . htmlspecialchars($clientInfo['Phone']) . "</li>";
        $html .= "<li class='list-group-item'><strong>Address:</strong> " . htmlspecialchars($clientInfo['address']) . "</li>";
        $html .= "</ul>";
        $html .= "</div>";
        $html .= "</div>";

        return $html;

    }



    function fetchClientEmailByTPIN($clientId, $lawFirmId) {
        global $connect;
        $stmt = $connect->prepare("SELECT * FROM lawFirmClients WHERE id = ? AND lawFirmId = ? ");
        $stmt->execute([$clientId, $lawFirmId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $email = "";
        if ($data) {
            $email = decrypt($data['client_email']);
        } else {
                     
            $email = '';
        }

        return $email;
    }

    function fetchAndDisplayEvent($lawFirmId){
        global $connect;
        $query = $connect->prepare("SELECT `event_id`, `title`,`description`, `start_date`, DATE_FORMAT(`start_time`, '%H:%i') AS `start_time`, `end_date`, DATE_FORMAT(`end_time`, '%H:%i') AS `end_time`, `color` FROM `events` WHERE lawFirmId = ? ORDER BY start_date ");
        $query->execute([$lawFirmId]);
        foreach ($query->fetchAll() as $event) : ?>
            <tr>
                <td><?php echo $event['title']; ?></td>
                <td><?php echo $event['description']; ?></td>
                <td><?php echo date("j M, Y", strtotime($event['start_date'])) ?>: <?php echo $event['start_time'] ?></td>
                <td><?php echo date("j M, Y", strtotime($event['end_date'])); ?>: <?php echo $event['end_time'] ?></td>
                <!-- You can add more columns here -->
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-sm editEvent" data-id="<?php echo $event['event_id'] ?>">Edit</button>
                        <button type="button" class="btn btn-danger btn-sm removeEvent" data-id="<?php echo $event['event_id'] ?>">Delete</button>
                    </div>
                </td>
            </tr>
        <?php endforeach;
    }

    function fetchAndDisplayPersonalEvent($userId, $lawFirmId){
        global $connect;
        $query = $connect->prepare("SELECT `event_id`, `title`,`description`, `start_date`, DATE_FORMAT(`start_time`, '%H:%i') AS `start_time`, `end_date`, DATE_FORMAT(`end_time`, '%H:%i') AS `end_time`, `color` FROM `events_personal` WHERE created_by = ? AND lawFirmId = ? ORDER BY start_date ");
        $query->execute([$userId, $lawFirmId]);
        foreach ($query->fetchAll() as $event) : ?>
            <tr>
                <td><?php echo $event['title']; ?></td>
                <td><?php echo $event['description']; ?></td>
                <td><?php echo date("j M, Y", strtotime($event['start_date'])) ?>: <?php echo $event['start_time'] ?></td>
                <td><?php echo date("j M, Y", strtotime($event['end_date'])); ?>: <?php echo $event['end_time'] ?></td>
                <!-- You can add more columns here -->
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-sm editEvent" data-id="<?php echo $event['event_id'] ?>">Edit</button>
                        <button type="button" class="btn btn-danger btn-sm removeEvent" data-id="<?php echo $event['event_id'] ?>">Delete</button>
                    </div>
                </td>
            </tr>
        <?php endforeach;
    }


    function fetchEventDetails(){
        global $connect;
        $query = $connect->prepare("SELECT `event_id`, `title`,`description`, `start_date`, DATE_FORMAT(`start_time`, '%H:%i') AS `start_time`, `end_date`, DATE_FORMAT(`end_time`, '%H:%i') AS `end_time`, `color`,`status`FROM `events` ORDER BY start_date ");
        $query->execute();
        foreach ($query->fetchAll() as $row) {
            ?>
            <tr>
                <td><?php echo $row['title'] ?></td>
                <td><?php echo $row['description'] ?></td>
                <td><?php echo date("D, d M Y: H:i", strtotime($row['start_date'] . ' ' . $row['start_time'])) ?></td>
                <td><?php echo date("D, d M Y: H:i", strtotime($row['end_date'] . ' ' . $row['end_time'])) ?></td>
                <td><a href="#" class="btn btn-primary btn-sm viewAttendees" data-eventid="<?php echo $row['event_id'] ?>">Attendees</a></td>
                <?php if($_SESSION['user_role'] === 'superAdmin' || $_SESSION['user_role'] === 'Secretary'):?>
                <td>
                    <?php if ($row['status'] == 0): ?>
                        <label><input type="checkbox" class="statusCheckbox" data-table="events" data-eventid="<?php echo $row['event_id'] ?>"> </label>
                    <?php else: ?>
                        ✅
                    <?php endif;?>
                </td>
                <td>
                    <a href="" type="button" class="btn btn-primary checkListModal btn-sm" data-id="<?php echo $row['event_id'] ?>">
                        Checklist
                    </a>
                </td>
            <?php endif;?>
            </tr>
            <?php
        }
    }


    function getBootstrapClassName($color) {
        switch ($color) {
            case 'red':
                return ' text-danger border-none';
            case 'blue':
                return ' text-primary border-none';
            case 'green':
                return ' text-success border-none';
            case 'yellow':
                return ' text-warning border-none';
            default:
                return ''; // Default class name (if needed)
        }
    }


    /*============================Finances Folders and Processing Functions==========================*/

    function fetchLawFirmDisbursements($lawFirmId){
        global $connect; // Assuming $connect is your PDO connection object

        $stmt = $connect->prepare("SELECT * FROM disbursements WHERE lawFirmId = ?");
        $stmt->execute([$lawFirmId]);
        $disbursement = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($disbursement) {
            $output = '';
            foreach ($disbursement as $row) {
                $created_at = date("D d, M, Y", strtotime($row['created_at']));
                $output .= '<tr>';
                $output .= '<td>' . getClientNameById($row['clientId'], $lawFirmId) . '</td>';
                $output .= '<td>' . $created_at . '</td>'; // Assuming date_added is the date in this context
                $output .= '<td>' . number_format($row['total'], 2) . '</td>';
                if($_SESSION['user_role'] === 'superAdmin' || $_SESSION['userJob'] === 'Financial Officer'){
                    $output .= '<td>';
                    $output .= '<div class="btn-group">
                                    <a href="finance/disbursements?details='.$row['id'].'" class="btn btn-primary btn-sm">Details</a>
                                    <button type="button" class="btn btn-dark btn-sm share-btn" data-client-id="'.$row['clientId'].'" data-id="'.$row['id'].'">
                                        <i class="bi bi-share-alt"></i> Share
                                    </button>';
                    $output .= '</div>';
                    $output .= '</td>';
                }
                $output .= '</tr>';
            }
            return $output;
        } else {
            return '';
        }
    }


    function fetchLawFirmExpenses($lawFirmId){
        global $connect; // Assuming $connect is your PDO connection object

        $stmt = $connect->prepare("SELECT * FROM tableExpenses WHERE lawFirmId = ?");
        $stmt->execute([$lawFirmId]);
        $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($expenses) {
            $output = '';
            foreach ($expenses as $expense) {
                $output .= '<tr>';
                $output .= '<td>' . decrypt($expense['description']) . '</td>';
                $output .= '<td>' . $expense['date_added'] . '</td>'; // Assuming date_added is the date in this context
                $output .= '<td>' . number_format($expense['amount'], 2) . '</td>';
                if($_SESSION['user_role'] === 'superAdmin' || $_SESSION['userJob'] === 'Financial Officer'){
                    $output .= '<td>';
                    $output .= '<div class="btn-group"><button class="btn btn-primary btn-sm editExpense" data-id="' . $expense['id'] . '">Edit</button>';
                    $output .= '<button class="btn btn-danger btn-sm deleteExpense" data-id="' . $expense['id'] . '">Delete</button></div>';
                    $output .= '</td>';
                }
                $output .= '</tr>';
            }
            return $output;
        } else {
            return '';
        }
    }


    function insertAuditTrail($lawFirmId, $user_id, $action) {
        global $connect;
        
        $date_added = date('Y-m-d H:i:s');

        $stmt = $connect->prepare("INSERT INTO tableAudit (lawFirmId, userId, action, date_added) VALUES (?, ?, ?, ?)");
        $stmt->execute([$lawFirmId, $user_id, $action, $date_added]);
    }

    function fetchLegalBudgetCategoryById($budget_id){
        global $connect;
        $stmt = $connect->prepare("SELECT category FROM tableBudgets WHERE id = ?");
        $stmt->execute([$budget_id]);
        $cat = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($cat) {
            return $cat['category'];
        } else {
            return 'Unknown category';
        }
    }


    function fetchLawFirmIncome($lawFirmId) {
        global $connect;
        $stmt = $connect->prepare("SELECT * FROM tableIncome WHERE lawFirmId = ?");
        $stmt->execute([$lawFirmId]);
        $incomes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($incomes) {
            $output = '';
            foreach ($incomes as $income) {
                $output .= '<tr>';
                $output .= '<td>' . decrypt($income['description']) . '</td>';
                $output .= '<td>' . htmlspecialchars(date("D d M, Y", strtotime($income['income_date']))) . '</td>';
                $output .= '<td>' . number_format($income['amount'], 2) . '</td>';
                if($_SESSION['user_role'] === 'superAdmin' || $_SESSION['userJob'] === 'Financial Officer'){
                    $output .= '<td>';
                    $output .= '<div class="btn-group"><button class="btn btn-primary btn-sm editIncome" data-id="' . htmlspecialchars($income['id']) . '">Edit</button>';
                    $output .= '<button class="btn btn-danger btn-sm deleteIncome" data-id="' . htmlspecialchars($income['id']) . '">Delete</button></div>';
                    $output .= '</td>';
                }
                $output .= '</tr>';
            }
            return $output;
        } else {
            return '';
        }
    }


    function getUserCurrency() {
        
        $ip = getUserIpAddr();
        $geolocation = json_decode(file_get_contents("http://ip-api.com/json/{$ip}"), true);
        
        if ($geolocation['status'] == 'success') {
            $countryCode = $geolocation['countryCode'];

            // Step 3: Use country code to get currency
            $currencyData = json_decode(file_get_contents("https://restcountries.com/v3.1/alpha/{$countryCode}"), true);
            
            if (!empty($currencyData)) {
                $currencies = array_keys($currencyData[0]['currencies']);
                $localCurrency = $currencies[0];

                // Optional Step 4: Get exchange rate (example uses USD as base)
                $exchangeRate = json_decode(file_get_contents("https://open.er-api.com/v6/latest/USD"), true);
                
                if (isset($exchangeRate['rates'][$localCurrency])) {
                    return [
                        'currency' => $localCurrency,
                        'rate' => $exchangeRate['rates'][$localCurrency]
                    ];
                } else {
                    return ['currency' => $localCurrency];
                }
            }
        }

        // Default to USD if unable to determine
        return ['currency' => 'ZMW'];
    }

    function pettyCash($lawFirmId){
        global $connect;
        $query = $connect->prepare("SELECT * FROM tablePettyCash WHERE lawFirmId = ?");
        $query->execute([$lawFirmId]);
        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($data as $row ) {
            if($row['debit'] == 0.00){
                $debit = '';
            }else{
                $debit =  number_format($row['debit'], 2);
            }
            if($row['credit'] == 0.00){
                $credit = '';
            }else{
                $credit =  number_format($row['credit'], 2);
            }

            echo '<tr>';
            echo '<td>' . date("j F, Y", strtotime($row['date'])). '</td>';
            echo '<td>' . decrypt($row['description']) . '</td>';
            echo '<td>' . $debit . '</td>';
            echo '<td>' . $credit. '</td>';
            echo '<td>ZMW ' . number_format($row['balance'], 2) . '</td>';
            echo '</tr>';
        }
    }


    function getBalance(){
        global $connect;
        $query = $connect->prepare("SELECT balance FROM tablePettyCash WHERE lawFirmId = ? ORDER BY id DESC LIMIT 1 ");
        $query->execute([$_SESSION['parent_id']]);
        if($query->rowCount() > 0 ){
            $row = $query->fetch(PDO::FETCH_ASSOC);
            $balance =  $row['balance'];
            if($balance == 0.00){
                exit;
            }
        }else{
            $balance = 0.00;
        }
        return $balance;
    }

    function fetchClients($lawFirmId) {
        global $connect;
        try {
            // Fetch individual clients
            $stmt1 = $connect->prepare("SELECT `id`, `client_names` AS `name`, `client_tpin` AS `tpin`, `date_created` FROM `lawFirmIndividualClients` WHERE `lawFirmId` = :lawFirmId");
            $stmt1->execute(['lawFirmId' => $lawFirmId]);
            $individualClients = $stmt1->fetchAll(PDO::FETCH_ASSOC);

            // Add type information
            foreach ($individualClients as &$client) {
                $client['type'] = 'Individual';
            }

            // Fetch corporate clients
            $stmt2 = $connect->prepare("SELECT `id`, `business_entity_name` AS `name`, `business_tpin` AS `tpin`, `date_created` FROM `lawFirmCorporateClients` WHERE `lawFirmId` = :lawFirmId");
            $stmt2->execute(['lawFirmId' => $lawFirmId]);
            $corporateClients = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            // Add type information
            foreach ($corporateClients as &$client) {
                $client['type'] = 'Corporate';
            }

            // Combine both results
            $clients = array_merge($individualClients, $corporateClients);

            foreach ($clients as $client) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($client['name']) . ' <small>(' . htmlspecialchars($client['type']) . ')</small></td>';
                echo '<td>' . htmlspecialchars($client['tpin']) . '</td>';
                echo '<td>' . date("D d M, Y", strtotime($client['date_created'])) . '</td>';
                echo '<td><button class="btn btn-primary btn-sm addCase" data-bs-toggle="modal" data-bs-target="#addCaseModal" data-client-id="' . $client['tpin'] . '"><i class="bi bi-briefcase"></i> Add Case</button></td>';
                echo '<td>'.clientCases($client['tpin']).'</td>';
                echo '</tr>';
            }


        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }


    /* documents section*/
    function fetchcaseIdsByLawFirmId($lawFirmId) {
        global $connect;
        $sql = "SELECT DISTINCT `caseId` 
                FROM `caseDocuments` 
                WHERE `lawFirmId` = :lawFirmId";
        $stmt = $connect->prepare($sql);
        $stmt->bindParam(':lawFirmId', $lawFirmId, PDO::PARAM_INT);
        $stmt->execute();
        $caseId = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $caseId;
    } 


    function countDocumentsByCaseAndLawFirm($caseId){
        global $connect;
        $sql = $connect->prepare("SELECT COUNT(id) as documentCount FROM caseDocuments WHERE caseId = ? ");
        $sql->execute([$caseId]);
        if($sql->rowCount() > 0){

            $row = $sql->fetch();
            return $row['documentCount'];
        }else{
            return null;
        }
    }

    function fetchCaseIdByLawFirmClientIdFromCases($lawFirmId, $clientId) {
        global $connect;
        $sql = "SELECT DISTINCT `id` 
                FROM `cases` 
                WHERE `lawFirmId` = ? AND `clientId` = ?";
        $stmt = $connect->prepare($sql);
        
        // Execute with an array of parameters
        $stmt->execute([$lawFirmId, $clientId]);
        
        // Fetch all results as an associative array
        $caseIds = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        
        return $caseIds;
    }


    /*functions for invoicing*/
    function fetchAndDisplayCompanyInfo() {
        global $connect;
        $sql = "SELECT * FROM `company_info` WHERE lawFirmId = ? ";
        $stmt = $connect->prepare($sql);
        $stmt->execute([$_SESSION['parent_id']]);
        if($stmt->rowCount() > 0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC); 
            $address = html_entity_decode($row['address']);
            $formatted_address = str_replace(["\r\n", "\n", "\r"], '<br>', $address);

            echo '<div class="col-md-6 mt-3">';
            echo '<h4>' . htmlspecialchars($row['company_name']) . '</h4>';
            echo '<p>' . $formatted_address . '</p>';
            echo '<p>' . htmlspecialchars($row['postal_code']) . '</p>';
            echo '</div>';
        }else{
            echo '<p class="mb-3"><a href="settings/firm">Add Firms Header</a></p>';
        }
    }


    function fetchLogoByLawFirmId($lawFirmId) {
        global $connect;
        $sql = $connect->prepare("SELECT `logo` FROM `company_info` WHERE `lawFirmId` = ?");
        $sql->execute([$lawFirmId]);
        $row = $sql->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $output = '<img src="settings/'.$row['logo'].'" alt="Company Logo" width="140">';
        } else {
            $output = '<a href="settings/firm"><img src="https://placehold.co/600x400?text=Add Your+Logo Here" alt="Company Logo" width="140"></a>';
        }
        return $output;
    }

    function fetchLogoByLawFirmIdForPfd($lawFirmId) {
        global $connect;
        $sql = "SELECT `logo` FROM `company_info` WHERE `lawFirmId` = :lawFirmId";
        $stmt = $connect->prepare($sql);
        $stmt->bindParam(':lawFirmId', $lawFirmId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $output = 'settings/' . $row['logo'];
        } else {
            $output = 'https://placehold.co/600x400?text=Your+Logo Here';
        }
        return $output;
    }



    //====================== INVOICE and Fee notes =========
    function fetchClientContacts($lawFirmId) {
        global $connect;
        try {
            // Fetch clients from the unified table
            $stmt = $connect->prepare("
                SELECT 
                    `id`, 
                    `client_type` AS `type`, 
                    CASE 
                        WHEN `client_type` = 'Individual' THEN `client_names` 
                        ELSE `business_name` 
                    END AS `name`, 
                    `client_tpin` AS `tpin`, 
                    `client_email` AS `email`, 
                    `created_at` AS `date_created`
                FROM `lawFirmClients`
                WHERE `lawFirmId` = :lawFirmId AND archived = '0'
            ");
            $stmt->execute(['lawFirmId' => $lawFirmId]);
            $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $clients;

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    function fetchFeeNotesByLawFirmId($lawFirmId) {
        global $connect;
        $sql = "SELECT DISTINCT `case_id` 
                FROM `fee_notes` 
                WHERE `lawFirmId` = :lawFirmId";
        $stmt = $connect->prepare($sql);
        $stmt->bindParam(':lawFirmId', $lawFirmId, PDO::PARAM_INT);
        $stmt->execute();
        $caseId = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $caseId;
    }

    function removeFirstWord($string) {
        // Use a regular expression to match the first word and any subsequent whitespace
        $modifiedString = preg_replace("/^\w+\s+/", "", $string);
        return $modifiedString;
    }

    function TotalFeeNoteAmountByCaseId($caseId) {
        global $connect;
        $query = $connect->prepare("SELECT SUM(total_amount) AS total_sum FROM timer_logs WHERE case_id = ? AND lawFirmId = ?");
        $query->execute([$caseId, $_SESSION['parent_id']]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['total_sum'] ? (float)$result['total_sum'] : 0.0;
    }

     function TotalFeeNoteAmountByLawfirmId($lawFirmId) {
        global $connect;
        $query = $connect->prepare("SELECT SUM(total_amount) AS total_sum FROM timer_logs WHERE lawFirmId = ?");
        $query->execute([$_SESSION['parent_id']]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['total_sum'] ? (float)$result['total_sum'] : 0.0;
    }

    function totalInvoiceAmountByCaseId($caseId) {
        global $connect;
        $query = $connect->prepare("SELECT SUM(total) AS total_sum FROM invoices WHERE id = ? AND lawFirmId = ?");
        $query->execute([$caseId, $_SESSION['parent_id']]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['total_sum'] ? (float)$result['total_sum'] : 0.0;
    }

    function totalInvoicesByLawfirmId($lawFirmId) {
        global $connect;
        $query = $connect->prepare("SELECT SUM(total) AS total_sum FROM invoices WHERE lawFirmId = ?");
        $query->execute([$lawFirmId]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['total_sum'] ? (float)$result['total_sum'] : 0.0;
    }

    // ============== DASHBOARD front page functions ==============

    
    function getTimeLogsData($lawFirmId) {
        global $connect;
        $stmt = $connect->prepare("SELECT time_spent, created_at FROM timer_logs WHERE lawFirmId = ?");
        $stmt->execute([$lawFirmId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $monthlyData = array_fill(1, 12, 0);

        foreach ($results as $row) {
            $month = (int)date('m', strtotime($row['created_at'])); // Extract month as an integer
            $monthlyData[$month] += $row['time_spent']; // Accumulate time spent
        }

        // Convert minutes to hours and round off
        foreach ($monthlyData as $month => $minutes) {
            $monthlyData[$month] = round($minutes / 60, 2); // Round to the nearest highest up to 3 figures
        }

        return $monthlyData;
    }

   /*
    function getTotalBillableTime($lawFirmId) {
        global $connect;
        $stmt = $connect->prepare("SELECT SUM(TIME_TO_SEC(elapsed_time)) as total_seconds FROM task_billing WHERE lawFirmId = :lawFirmId");
        $stmt->execute(['lawFirmId' => $lawFirmId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $totalSeconds = $result['total_seconds'];
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);

        return [
            'hours' => $hours,
            'minutes' => $minutes
        ];
    }
    */

    function getTotalBillableTime($lawFirmId) {
        global $connect;
        $stmt = $connect->prepare("
            SELECT SUM(hours * 3600 + minutes * 60) as total_seconds 
            FROM time_entries 
            WHERE lawFirmId = :lawFirmId AND billableStatus = 'billable'
        ");
        $stmt->execute(['lawFirmId' => $lawFirmId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalSeconds = $result['total_seconds'] ?? 0;
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        return [
            'hours' => $hours,
            'minutes' => $minutes
        ];
    }

    function fetchTotalCases($lawFirmId) {
        global $connect;
        $query = $connect->prepare("SELECT * FROM cases WHERE lawFirmId = ?");
        $query->execute([$lawFirmId]);
        $result = $query->rowCount();
        return $result;
    }


    function getRecentCases($lawFirmId) {
        global $connect;
    
        $currentDate = date('Y-m-d');
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
    
        $stmt = $connect->prepare("
            SELECT 
                COUNT(CASE WHEN caseStatus <> 'closed' AND created_at BETWEEN :thirtyDaysAgo AND :currentDate THEN 1 END) AS new_cases_30_days,
                COUNT(CASE WHEN caseStatus = 'closed' AND closed_at BETWEEN :thirtyDaysAgo AND :currentDate THEN 1 END) AS closed_cases_30_days
            FROM cases
            WHERE lawFirmId = :lawFirmId
        ");
    
        $stmt->execute([
            'lawFirmId' => $lawFirmId,
            'thirtyDaysAgo' => $thirtyDaysAgo,
            'currentDate' => $currentDate
        ]);
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        $html = <<<HTML
            <table class="table table-borderless">
                <tr>
                    <th>New Cases Last 30 Days</th>
                    <td align="right">{$result['new_cases_30_days']}</td>
                </tr>
                <tr>
                    <th>Closed Cases Last 30 Days</th>
                    <td align="right">{$result['closed_cases_30_days']}</td>
                </tr>
            </table>
        HTML;
    
        return $html;
    }
    function fetchTotalCorporateClient($lawFirmId) {
        global $connect;
        $query = $connect->prepare("SELECT * FROM lawFirmClients WHERE lawFirmId = ? AND client_type = 'Corporate' AND archived = 0 ");
        $query->execute([$lawFirmId]);
        $result = $query->rowCount();
        return $result;
    }

    function fetchTotalIndividualClients($lawFirmId) {
        global $connect;
        $query = $connect->prepare("SELECT * FROM lawFirmClients WHERE lawFirmId = ? AND client_type = 'Individual' AND archived = 0 ");
        $query->execute([$lawFirmId]);
        $result = $query->rowCount();
        return $result;
    }

    function getRecentClients($lawFirmId) {
        global $connect;
    
        $currentDate = date('Y-m-d');
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
    
        $stmt = $connect->prepare("
            SELECT 
                COUNT(CASE WHEN date_created BETWEEN :thirtyDaysAgo AND :currentDate THEN 1 END) AS recent_corporate_clients
            FROM lawFirmCorporateClients
            WHERE lawFirmId = :lawFirmId
        ");
    
        $stmt->execute([
            'lawFirmId' => $lawFirmId,
            'thirtyDaysAgo' => $thirtyDaysAgo,
            'currentDate' => $currentDate
        ]);
    
        $corporateResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
        $stmt = $connect->prepare("
            SELECT 
                COUNT(CASE WHEN date_created BETWEEN :thirtyDaysAgo AND :currentDate THEN 1 END) AS recent_individual_clients
            FROM lawFirmIndividualClients
            WHERE lawFirmId = :lawFirmId
        ");
    
        $stmt->execute([
            'lawFirmId' => $lawFirmId,
            'thirtyDaysAgo' => $thirtyDaysAgo,
            'currentDate' => $currentDate
        ]);
    
        $individualResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
        $totalClients = $corporateResult['recent_corporate_clients'] + $individualResult['recent_individual_clients'];
    
        $html = <<<HTML
            <table class="table">
                <tr>
                    <th>Recent Corporate Clients</th>
                    <td align="right">{$corporateResult['recent_corporate_clients']}</td>
                </tr>
                <tr>
                    <th>Recent Individual Clients</th>
                    <td align="right">{$individualResult['recent_individual_clients']}</td>
                </tr>
                <tr>
                    <th>Total Recent Clients</th>
                    <td align="right">{$totalClients}</td>
                </tr>
            </table>
        HTML;
    
        return $html;
    }

    function countUsersByFirmId($lawFirmId) {
        global $connect;
        $query = $connect->prepare("SELECT * FROM lawFirms WHERE firmId = ?");
        $query->execute([$lawFirmId]);
        $result = $query->rowCount();
        return $result;
    }
    
    function fetchInvoicePDF($invoiceId, $lawFirmId, $clientId){
        global $connect;
        $query = $connect->prepare("SELECT * FROM lawFirmInvoices WHERE invoice_id = ? AND lawFirmId = ? AND clientId = ? ");
        $query->execute([$invoiceId, $lawFirmId, $clientId]);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        if($row){
           return $row['pdfFilePath']; 
       }else{
        return null;
       }
    }

    function countUpcomingAppointments($lawFirmId) {
        global $connect;
        $currentDateTime = date('Y-m-d H:i:s');
        $startOfToday = date('Y-m-d 00:00:00'); // Start of today

        // Prepare the SQL query
        $sql = "SELECT COUNT(*) AS appointment_count 
                FROM events 
                WHERE lawFirmId = :lawFirmId 
                  AND CONCAT(start_date, ' ', start_time) BETWEEN :startOfToday AND :currentDateTime";

        try {
            // Prepare the statement
            $stmt = $connect->prepare($sql);
            
            // Bind the parameters
            $stmt->bindParam(':lawFirmId', $lawFirmId, PDO::PARAM_INT);
            $stmt->bindParam(':startOfToday', $startOfToday, PDO::PARAM_STR);
            $stmt->bindParam(':currentDateTime', $currentDateTime, PDO::PARAM_STR);

            // Execute the query
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['appointment_count'] ?? 0;
        } catch (PDOException $e) {
            // Handle the exception (log it, rethrow it, etc.)
            error_log($e->getMessage());
            return 0;
        }
    }
    // fetchCaseNumber fetchandDisplayEVent

    function countUpcomingEvents($lawFirmId) {
        global $connect;
        $query = $connect->prepare("SELECT * FROM events WHERE lawFirmId = ? AND CONCAT(start_date, ' ', start_time) > NOW()");
        $query->execute([$lawFirmId]);
        $result = $query->rowCount();
        return $result;
        
    }

    function fetchCaseNumber($caseId, $lawFirmId) {
        global $connect;
        $query = $connect->prepare("SELECT * FROM cases WHERE id = ? AND lawFirmId = ? ");
        $query->execute([$caseId, $lawFirmId]);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $result = $row['caseNo'];
        return $result;
    }

    function fetchCaseTitle($caseId, $clientId) {
        global $connect;
        $query = $connect->prepare("SELECT `caseTitle` FROM cases WHERE id = ? AND clientId = ? ");
        $query->execute([$caseId, $clientId]);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $result = decrypt($row['caseTitle']);
        return $result;
    }

    function fetchCaseTitleById($caseId) {
        global $connect;
        $query = $connect->prepare("SELECT `caseTitle` FROM cases WHERE id = ? ");
        $query->execute([$caseId]);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $result = html_entity_decode(decrypt($row['caseTitle']));
        return $result;
    }


    // ================== CRM function caseNo ===================

    function fetchTotalCasesById($lawFirmId, $clientId) {
        global $connect;
        $query = $connect->prepare("SELECT * FROM cases WHERE lawFirmId = ? AND clientId = ? ");
        $query->execute([$lawFirmId, $clientId]);
        $result = $query->rowCount();
        return $result;
    }


    function fetchTotalInvoicesById($lawFirmId, $clientId) {
        global $connect;
        $query = $connect->prepare("SELECT * FROM invoices WHERE lawFirmId = ? AND clientId = ? ");
        $query->execute([$lawFirmId, $clientId]);
        $result = $query->rowCount();
        return $result;
    }

    function fetchLawFirmClientKYC($clientId, $lawFirmId) {
        global $connect;
        $sql = $connect->prepare("SELECT * FROM lawFirmClients WHERE id = ? AND lawFirmId = ? ");
        $sql->execute([$_SESSION['parent_id']]);
        $client = $sql->fetch(PDO::FETCH_ASSOC);
        
        if ($client){ 
            $Tpin   = decrypt($client['client_tpin']);
            $kyc    = $client['kyc'];
            if ($kyc == '0') {
                $action = '<small>KYC not yet sent</small>';
            } elseif ($kyc == '1') {
                $action = '<small>Sent the e-form to the client, awaiting them to send back filled and a signed one</small>';
            } elseif ($kyc == '2') {
                $action = '<small>Client returned the forms and can be viewed <br>  <a href="cc/kyccorporate?cc='.$Tpin.'">HERE</a> </small>';
            }
        }
    }
    function fetchClientInfoByIdForCRM($clientId) {
        global $connect;

        try {
            // Prepare the query to fetch client information by TPIN
            $stmt = $connect->prepare("
                SELECT 
                    *
                FROM lawFirmClients
                WHERE id = ? AND lawFirmId = ?
            ");
            $stmt->execute([$clientId, $_SESSION['parent_id']]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $name = $identity = "";
                if ($data['client_type'] === 'Corporate') {
                    $name = '<p><strong>Representative:</strong> ' . html_entity_decode(decrypt($data['client_names'])) . '</p>';
                    if($data['incorporation_number'] != ""){
                        $identity = '<p><strong>Incorporation No:</strong> ' . html_entity_decode(decrypt($data['incorporation_number'])) . '</p>';
                    }else{
                        $identity = ''; 
                    }
                } else if ($data['client_type'] === 'Individual') {
                    //$name = '<p><strong> ' . html_entity_decode(decrypt($data['client_names'])) . '</strong></p>';
                    if($data['nrc_passport_number'] != ""){
                        $identity = '<p><strong>NRC / Password No:</strong> ' . html_entity_decode(decrypt($data['nrc_passport_number'])) . '</p>';
                    }else{
                        $identity = ''; 
                    }
                }
                if($data['address'] == ""){
                    $address = "Add Clients Address";
                }else{
                    $address = nl2br(decrypt($data['address']));
                }
                echo $name;
                echo $identity;
                echo '<p><strong>Address:</strong> ' . $address . '</p>';
                echo '<p><strong>Email:</strong> ' . html_entity_decode(decrypt($data['client_email'])) . '</p>';
                echo '<p><strong>Phone:</strong> ' . html_entity_decode(decrypt($data['client_phone'])) . '</p>';
                echo '<p><strong>TPIN:</strong> ' . html_entity_decode(decrypt($data['client_tpin'])) . '</p>';
                
            } else {
                echo '<p>No data found for the provided Client.</p>';
            }
        } catch (PDOException $e) {
            echo '<p>Database error: ' . html_entity_decode($e->getMessage()) . '</p>';
        }
    }

    function resizeImage($filePath, $width, $height) {
        list($originalWidth, $originalHeight, $imageType) = getimagesize($filePath);
        
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($filePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($filePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($filePath);
                break;
            default:
                throw new Exception('Unsupported image type');
        }
        
        $destinationImage = imagecreatetruecolor($width, $height);

        // Preserve transparency for PNG and GIF
        if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
            imagecolortransparent($destinationImage, imagecolorallocatealpha($destinationImage, 0, 0, 0, 127));
            imagealphablending($destinationImage, false);
            imagesavealpha($destinationImage, true);
        }

        // Resize the image
        imagecopyresampled($destinationImage, $sourceImage, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

        // Save the resized image
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($destinationImage, $filePath);
                break;
            case IMAGETYPE_PNG:
                imagepng($destinationImage, $filePath);
                break;
            case IMAGETYPE_GIF:
                imagegif($destinationImage, $filePath);
                break;
        }

        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($destinationImage);
    }

    function calculateTimeDifference($startTime, $endTime) {
        // Convert the time strings to DateTime objects
        $start = new DateTime($startTime);
        $end = new DateTime($endTime);

        // Calculate the difference
        $interval = $start->diff($end);

        // Get the difference in seconds
        $seconds = $interval->days * 24 * 60 * 60;
        $seconds += $interval->h * 60 * 60;
        $seconds += $interval->i * 60;
        $seconds += $interval->s;

        // Calculate hours, minutes, and remaining seconds
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        return [
            'total_seconds' => $seconds,
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $remainingSeconds,
            'formatted' => sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds)
        ];
    }

    // Function to format file size
    function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    function getFolderName($folderId) {
        global $connect;
        $lawFirmId = $_SESSION['parent_id'];
        $stmt = $connect->prepare("SELECT folder_name FROM lawFirmFolders WHERE id = ? AND lawFirmId = ? ");
        $stmt->execute([$folderId, $lawFirmId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row){
            return $row['folder_name'];
        }
    }

    function countFilesInFolder($folderId, $lawFirmId) {
        global $connect;
        $lawFirmId = $_SESSION['parent_id'];
        $sql = $connect->prepare("SELECT * FROM `lawFirmFiles` WHERE folder_id = ? AND lawFirmId = ? ");
        $sql->execute([$folderId, $lawFirmId]);
        $count = $sql->rowCount();
        return $count;
    }

    function fetchClientCRMMatters($clientId, $lawFirmId) {
        global $connect;
        try {
            // Fetch clients from the unified table
            $stmt = $connect->prepare("
                SELECT 
                    *     
                FROM `cases`
                WHERE `clientId` = ? AND `lawFirmId` = ? AND caseStatus != 'Closed'
            ");
            $stmt->execute([$clientId, $lawFirmId]);
            $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $clients;

        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    /*End of CRM Functions*/ 

    /*Dashboard for Finance Officers*/

    function fetchTotalInvoices($lawFirmId) {
        global $connect;
        $query = $connect->prepare("SELECT * FROM invoices WHERE lawFirmId = ?");
        $query->execute([$lawFirmId]);
        $result = $query->rowCount();
        return $result;
    }

    function fetchTotalIncome($lawFirmId) {
        global $connect;
        $query = $connect->prepare("SELECT SUM(amount) as total_income, currency FROM `tableIncome` WHERE lawFirmId = ? ");
        $query->execute([$lawFirmId]);
        $row = $query->fetch();
        if($row){
            $output = $row['currency'].' '.$row['total_income'];
            return $output;
        }
        return 0.00;
    }

    function fetchTotalExpenses($lawFirmId) {
        global $connect;
        $query = $connect->prepare("SELECT SUM(amount) as total_income, currency FROM `tableExpenses` WHERE lawFirmId = ? ");
        $query->execute([$lawFirmId]);
        $row = $query->fetch();
        if($row){
            $output = $row['currency'].' '.$row['total_income'];
            return $output;
        }
        return 0.00;
    }


    function fetchFinancesRecentActivities($lawFirmId) {
        global $connect;
        $activities = [];
        $currentDate = date('Y-m-d');
        $fiveDaysAgo = date('Y-m-d', strtotime('-5 days'));

        // Income
        $stmt = $connect->prepare("SELECT 'Income' as type, id, description, currency, amount, income_date as activity_date FROM tableIncome WHERE lawFirmId = :lawFirmId AND income_date BETWEEN :fiveDaysAgo AND :currentDate");
        $stmt->execute(['lawFirmId' => $lawFirmId, 'fiveDaysAgo' => $fiveDaysAgo, 'currentDate' => $currentDate]);
        $incomeResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($incomeResults as &$incomeItem) {
            $incomeItem['description'] = decrypt($incomeItem['description']);
        }
        $activities = array_merge($activities, $incomeResults);

        // Expenses
        $stmt = $connect->prepare("SELECT 'Expense' as type, id, description, currency, amount, date_added as activity_date FROM tableExpenses WHERE lawFirmId = :lawFirmId AND date_added BETWEEN :fiveDaysAgo AND :currentDate");
        $stmt->execute(['lawFirmId' => $lawFirmId, 'fiveDaysAgo' => $fiveDaysAgo, 'currentDate' => $currentDate]);
        $expenseResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($expenseResults as &$expenseItem) {
            $expenseItem['description'] = decrypt($expenseItem['description']);
        }
        $activities = array_merge($activities, $expenseResults);

        // Events
        $stmt = $connect->prepare("SELECT 'Event' as type, event_id as id, title as description, start_date as activity_date FROM events WHERE lawFirmId = :lawFirmId AND start_date BETWEEN :fiveDaysAgo AND :currentDate");
        $stmt->execute(['lawFirmId' => $lawFirmId, 'fiveDaysAgo' => $fiveDaysAgo, 'currentDate' => $currentDate]);
        $activities = array_merge($activities, $stmt->fetchAll(PDO::FETCH_ASSOC));

        // Invoices
        $stmt = $connect->prepare("SELECT 'Invoice' as type, id, invoice_number as description, total as amount, date as activity_date FROM invoices WHERE lawFirmId = :lawFirmId AND date BETWEEN :fiveDaysAgo AND :currentDate");
        $stmt->execute(['lawFirmId' => $lawFirmId, 'fiveDaysAgo' => $fiveDaysAgo, 'currentDate' => $currentDate]);
        $activities = array_merge($activities, $stmt->fetchAll(PDO::FETCH_ASSOC));

        // Sort activities by date
        usort($activities, function($a, $b) {
            return strtotime($b['activity_date']) - strtotime($a['activity_date']);
        });

        return $activities;
    }
    
    function fetchLawFirmData($lawFirmId) {
        global $connect;
        $data = [
            'cases' => [],
            'clients' => [],
            'files' => [],
            'folders' => [],
            'events' => []
        ];

        $currentDate = date('Y-m-d');
        $fiveDaysAgo = date('Y-m-d', strtotime('-5 days'));

        // Cases
        $stmt = $connect->prepare("SELECT * FROM `cases` WHERE lawFirmId = :lawFirmId AND caseDate BETWEEN :fiveDaysAgo AND :currentDate");
        $stmt->execute(['lawFirmId' => $lawFirmId, 'fiveDaysAgo' => $fiveDaysAgo, 'currentDate' => $currentDate]);
        $caseResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($caseResults as &$caseItem) {
            $caseItem['caseTitle'] = decrypt($caseItem['caseTitle']);
        }
        $data['cases'] = $caseResults;

        $stmt = $connect->prepare("SELECT * FROM `lawFirmClients` WHERE lawFirmId = :lawFirmId AND archived = '0' AND created_at BETWEEN :fiveDaysAgo AND :currentDate");
        $stmt->execute(['lawFirmId' => $lawFirmId, 'fiveDaysAgo' => $fiveDaysAgo, 'currentDate' => $currentDate]);
        $clientResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data['clients'] = $clientResults;

        // Files
        $stmt = $connect->prepare("SELECT * FROM `lawFirmFiles` WHERE lawFirmId = :lawFirmId AND uploaded_at BETWEEN :fiveDaysAgo AND :currentDate");
        $stmt->execute(['lawFirmId' => $lawFirmId, 'fiveDaysAgo' => $fiveDaysAgo, 'currentDate' => $currentDate]);
        $data['files'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Folders
        $stmt = $connect->prepare("SELECT * FROM `lawFirmFolders` WHERE lawFirmId = :lawFirmId AND created_at BETWEEN :fiveDaysAgo AND :currentDate");
        $stmt->execute(['lawFirmId' => $lawFirmId, 'fiveDaysAgo' => $fiveDaysAgo, 'currentDate' => $currentDate]);
        $data['folders'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Events
        $stmt = $connect->prepare("SELECT * FROM `events` WHERE lawFirmId = :lawFirmId AND start_date BETWEEN :fiveDaysAgo AND :currentDate");
        $stmt->execute(['lawFirmId' => $lawFirmId, 'fiveDaysAgo' => $fiveDaysAgo, 'currentDate' => $currentDate]);
        $eventResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($eventResults as &$eventItem) {
            $eventItem['title'] = $eventItem['title'];
        }
        $data['events'] = $eventResults;

        return $data;
    }


    /*
        company header
    */

    function displayCompanyData($lawFirmId) {
        global $connect;
        try {
            $stmt = $connect->prepare("SELECT * FROM company_info WHERE lawFirmId = ?");
            $stmt->execute([$lawFirmId]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                ?>
                <div class="card">
                    <div class="card-header row">
                        <div class="col-md-4">
                            <img id="companyLogo" src="settings/<?php echo $data['logo']; ?>" alt="Company Logo" style="display:block; max-width:170px;">
                        </div>
                        <div class="col-md-8" align="right">
                            <div id="companyData">
                                <h4><?php echo $data['company_name']; ?></h4>
                                <span id="companyTpin"><?php echo $data['tpin']; ?></span><br>
                                <span id="address"><?php echo $data['address']; ?></span><br>
                                <span id="postalCode"><?php echo $data['postal_code']; ?></span><br>
                                <span id="telephone"><?php echo $data['telephone']; ?></span><br>
                                <span id="email"><?php echo $data['email']; ?></span><br>
                                <span id="website"><?php echo $data['website']; ?></span><br>
                                <span id="linkedin"><?php echo $data['linkedin']; ?></span><br>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                echo "No data found.";
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }

    // fetchClientInfo


    function getSubscriptionData($lawFirmId) {
        global $connect;
        $sql = "SELECT * 
                FROM `subscriptions` 
                WHERE `lawFirmId` = ?";
        $stmt = $connect->prepare($sql);
        $stmt->execute([$lawFirmId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function calculateRemainingTime($endDate) {
        $endDateTime = new DateTime($endDate);
        $currentDateTime = new DateTime();
        if ($currentDateTime >= $endDateTime) {
            return "expired";
        }

        $interval = $currentDateTime->diff($endDateTime);
        return $interval->format('%a days %h hours %i minutes');
    }

    function lockProfileIfExpired($remainingTime) {
        if ($remainingTime === "expired") {
            // Start session if not already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['profile_locked'] = true;
        } else {
            $_SESSION['profile_locked'] = false;
        }
    }

    // Create member profile data displayFolders

    function fetchMembersCases($userId) {
        global $connect;
        $query = $connect->prepare("SELECT id, caseTitle FROM cases WHERE userId = :userId ");
        $query->execute([
            ':userId' => $userId,
        ]);
        $cases = $query->fetchAll(PDO::FETCH_ASSOC);
        return $cases;
    }
    
    function countMilestones($caseId, $userId) {
        global $connect;
        
        $query = "SELECT COUNT(*) as milestoneCount 
                  FROM case_milestones 
                  WHERE caseId = :caseId AND userId = :userId";
        
        $stmt = $connect->prepare($query);
        $stmt->execute([
            ':caseId' => $caseId,
            ':userId' => $userId
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['milestoneCount'];
    }
    
    function countTimeEntries($caseId, $userId) {
        global $connect;
        $query = $connect->prepare("SELECT id 
                  FROM time_entries 
                  WHERE caseId = :caseId AND userId = :userId");
        $query->execute([
            ':caseId' => $caseId,
            ':userId' => $userId
        ]);
        
        $result = $query->rowCount();
        
        return $result;
    }
    
    function countTotalMilestones($userId) {
        global $connect;
        $query = "SELECT COUNT(*) as milestoneCount 
                  FROM case_milestones 
                  WHERE userId = :userId";
        
        $stmt = $connect->prepare($query);
        $stmt->execute([
            ':userId' => $userId
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['milestoneCount'];
    }
    
    function countTotalTimeEntries($userId) {
        global $connect;
        
        $query = "SELECT COUNT(*) as entryCount 
                  FROM time_entries 
                  WHERE userId = :userId";
        
        $stmt = $connect->prepare($query);
        $stmt->execute([
            ':userId' => $userId
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['entryCount'];
    }


    /*Time entries*/
    function fetchTimeEntries($lawFirmId){
        global $connect;

        $isSuperAdmin = ($_SESSION['user_role'] == 'superAdmin');
        $userId = $_SESSION['user_id'];
        if($isSuperAdmin){
            $stmt = $connect->prepare("SELECT * FROM `time_entries` WHERE lawFirmId = ?");
            $stmt->execute([$lawFirmId]);
        } else {
            $stmt = $connect->prepare("SELECT * FROM `time_entries` WHERE lawFirmId = ? AND userId = ?");
            $stmt->execute([$lawFirmId, $userId]);
        }
        
        $timeEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($timeEntries)) {
            // No time entries found
            echo '<tr><td colspan="9" class="text-center">No time entries found.</td></tr>';
        } else {
            foreach($timeEntries as $row){
                $staffName = fetchLawFirmMemberNames($row['userId']);
                $matterNo = ($row['caseId'] == 'Non-Matter-Related') ? "Non-Matter-Related" : fetchCaseNumber($row['caseId'], $lawFirmId);
                $clientNames = ($row['caseId'] == 'Non-Matter-Related') ? "InHouse" : clientIdByCaseId($row['caseId']);
                $billableStatus = $row['billableStatus'];
                $hours = ($row['hours'] > 1) ? $row['hours']." hrs " : $row['hours']." hr ";
                $bill_status = ($billableStatus == 'nonBillable') ? 'none' : 'billable';
                $clientId = ($row['clientId'] != "") ? $row['clientId'] : "";
                
                if($row['billableStatus'] == 'billable' AND $row['status'] == '1' ){
                    $status = '<small><span class="badge bg-success">Billed</span></small>';
                } elseif($row['billableStatus'] == 'billable' AND $row['status'] == '0'){
                    $status = '<small><span class="badge bg-danger">Uninvoiced</span></small>'; 
                } else {
                    $status = '';
                }           
            ?>
                <tr>
                    <td><?php echo $clientNames; ?></td>
                    <td><?php echo $row['dateCreated']; ?></td>
                    <td><?php echo decrypt($row['description']); ?></td>
                    <td><?php echo $matterNo ?></td>
                    <td><?php echo $hours . '' . $row['minutes'] . ' Mins '; ?></td>
                    <td><?php echo $row['hourlyRate']; ?></td>
                    <td><?php echo $row['cost']; ?></td>
                    <td><?php echo $staffName ?></td>
                    <td><?php echo $status?></td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item editTimerLog" href="#" data-id="<?php echo $row['id']; ?>">Edit</a>
                                <?php if($row['status'] == '1'):?>
                                    <a class="dropdown-item deleteTimerLog" href="#" id="<?php echo $row['id']; ?>" disabled>Delete</a>
                                <?php else:?>
                                    <a class="dropdown-item deleteTimerLog" href="#" id="<?php echo $row['id']; ?>" >Delete</a>
                                <?php endif;?>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php
            }
        }
    } 

    /*New Invoic*/ 
    function fetchCreatedInvoice($lawFirmId){
        global $connect;
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];
        $userJob = $_SESSION['userJob'];

        $isSuperAdmin = ($userRole === 'superAdmin');
        $isFinancialOfficer = ($userJob === 'Financial Officer');

        if ($isSuperAdmin || $isFinancialOfficer) {
            $query = $connect->prepare("SELECT * FROM invoices WHERE lawFirmId = ?");
            $query->execute([$lawFirmId]);
        } else {
            $query = $connect->prepare("SELECT * FROM invoices WHERE lawFirmId = ? AND createdBy = ?");
            $query->execute([$lawFirmId, $userId]);
        }

        $count = $query->rowCount();
        if($count > 0){
            foreach ($query->fetchAll() as $row){
                $clientId = $row['clientId'];
                $lawFirmId = $row['lawFirmId']; 
                $invoiceId = $row['id']; 
                // $filePath = $row['pdfFilePath'];
                // $link = preg_replace("#[^0-9.A-Za-z-]#", " ", $filePath);
                // $link = removeFirstWord($link);
                $balance = $row['total'] - $row['amountPaid'];
                if($row['status'] == '0'){
                    $status = '<button data-invoice="'.$invoiceId.'" data-number="'.$row['invoice_number'].'" id="'.$balance.'" class="btn btn-outline-danger btn-sm ">Unpaid</button>';
                }elseif($row['status'] == '2'){
                    $status = '<button class="btn btn-warning btn-sm">Partly Paid</button>';
                }else{
                    $status = '<button class="btn btn-success btn-sm">Fully Paid</button>';
                }

                if($row['pdfFilePath'] != ""){
                    $filePath = '<a href="billings/'.$row['pdfFilePath'].'" target="_blank" class="dropdown-item"><i class="bi bi-file-pdf"></i> View PDF</a>'; 
                }else{
                    $filePath = "";
                }
        ?>
            <tr>
                <td><?php echo $row['invoice_number']; ?></td>
                <td><a href="crm/?clientId=<?php echo base64_encode($clientId) ?>"> <?php echo getClientNameById($clientId, $lawFirmId); ?></a></td>
                <td><?php echo date("D d M, Y", strtotime($row['created_at'])); ?></td>
                <td><?php echo date("Y-m-d", strtotime($row['due_date'])); ?></td>
                <td><?php echo number_format($row['total'], 2); ?></td>
                <td><?php echo number_format($row['amountPaid'], 2); ?></td>
                <td><?php echo number_format($balance, 2); ?></td>
                <td><?php echo $status?></td>
                
                <td>
                    <a href="billings/invoice-preview?invoiceId=<?php echo base64_encode($invoiceId)?>" class="dropdown-item text-primary"><i class="bx bx-show"></i> Preview</a>
                    <!-- <?php echo $filePath?> -->
                </td>
            </tr>
        <?php 
            }
        } else {
            // echo '<tr><td colspan="9" class="text-center">No invoices found.</td></tr>';
        }
    }

    function formatCurrency($amount) {
        return number_format($amount, 2);
    }


    // Function to count invoiced clients
    function CountInvoicedClients($lawFirmId) {
        global $connect;
        $query = $connect->prepare("SELECT COUNT(DISTINCT clientId) as invoiced FROM invoices WHERE lawFirmId = ?");
        $query->execute([$lawFirmId]);
        $row = $query->fetch();
        return $row ? $row['invoiced'] : 0;
    }

    // Function to count total invoices
    function CountTotalInvoices($lawFirmId) {
        global $connect;
        $query = $connect->prepare("SELECT COUNT(id) as total FROM invoices WHERE lawFirmId = ?");
        $query->execute([$lawFirmId]);
        $row = $query->fetch();
        return $row ? $row['total'] : 0;
    }

    // Function to calculate total paid amount
    function CalculateTotalPaidAmount($lawFirmId) {
        global $connect;
        $query = $connect->prepare("SELECT SUM(amountPaid) as paid FROM invoices WHERE lawFirmId = ?");
        $query->execute([$lawFirmId]);
        $row = $query->fetch();
        return $row ? number_format($row['paid'], 2) : '0.00';
    }

    // Function to calculate total unpaid amount
    function CalculateTotalUnpaidAmount($lawFirmId) {
        global $connect;
        $query = $connect->prepare("SELECT SUM(total) as total, SUM(amountPaid) as paid FROM invoices WHERE lawFirmId = ? AND status = '0'");
        $query->execute([$lawFirmId]);
        $row = $query->fetch();
        $total = $row['total'];
        $paid  = $row['paid'];
        $unpaid = $total - $paid;
        return $row ? number_format($unpaid, 2) : '0.00';
    }

    function fetchInvoiceBalance($invoiceId) {
        global $connect;
        $stmt = $connect->prepare("SELECT total, amountPaid, remainingBalance FROM invoices WHERE id = :invoiceId");
        $stmt->execute([':invoiceId' => $invoiceId]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($invoice) {
            return $invoice['remainingBalance'];
        }
        
        return false;
    }

    function fetchInvoiceClientI($invoiceId) {
        global $connect;
        $stmt = $connect->prepare("SELECT clientId FROM invoices WHERE id = :invoiceId");
        $stmt->execute([':invoiceId' => $invoiceId]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($invoice) {
            return $invoice['clientId'];
        }
        
        return false;
    }


    function getGeoData() {
        $api_key = '41574dba141438223442dee975e0606d0cd00731';
        $url = "https://api.getgeoapi.com/v2/ip/check?api_key={$api_key}";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code == 200 && $response) {
            return json_decode($response, true);
        }
        return null;
    }

    function userMainCurrency() {
        $geo_data = getGeoData();
        if ($geo_data && isset($geo_data['currency']['code'])) {
            return $geo_data['currency']['code'];
        }
        return 'ZMW'; // Default to Zambian Kwacha if unable to get currency
    }

    function userMainCountry() {
        $geo_data = getGeoData();
        if ($geo_data && isset($geo_data['country']['name'])) {
            return $geo_data['country']['name'];
        }
        return 'Zambia'; // Default to Zambia if unable to get country
    }

    function fetchDeposits($clientId){
        global $connect;
        $stmt = $connect->prepare("
        SELECT * FROM deposited_funds WHERE clientId = ?
        ORDER BY date_deposited DESC");
        $stmt->execute([$clientId]);
        $deposits = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($stmt->rowCount() > 0){
            foreach ($deposits as $deposit): 
                $caseTitle = fetchCaseTitleById($deposit['caseId']);
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($deposit['date_deposited']); ?></td>
                    <td><?php echo htmlspecialchars($caseTitle); ?></td>
                    <td><?php echo htmlspecialchars($deposit['currency']); ?> <?php echo number_format($deposit['amount'], 2); ?></td>
                    <td><?php echo html_entity_decode(decrypt($deposit['description'])); ?></td>
                </tr>
            <?php endforeach;
        }
    }

    function fetchDisbursements($clientId){
        global $connect;
        $stmt = $connect->prepare("
        SELECT * FROM disbursed_funds WHERE clientId = ?
        ORDER BY date_disbursed DESC");
        $stmt->execute([$clientId]);
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($stmt->rowCount() > 0){
            foreach ($row as $disbursed): 
                $caseTitle = fetchCaseTitleById($disbursed['caseId']);
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($disbursed['date_disbursed']); ?></td>
                    <td><?php echo htmlspecialchars($caseTitle); ?></td>
                    <td><?php echo htmlspecialchars($disbursed['currency']); ?> <?php echo number_format($disbursed['amount'], 2); ?></td>
                    <td><?php echo html_entity_decode(decrypt($disbursed['description'])); ?></td>
                </tr>
            <?php endforeach;
        }
    }


    // Matter Details AI 

    function makeOpenAIRequest($caseTitle, $caseDescription) {
        $apiKey = SECRET_KEY;
        $url = 'https://api.openai.com/v1/chat/completions';
        
        $data = [
            'model' => 'gpt-4', // or 'gpt-4' if you have access
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a legal assistant analyzing case details.'
                ],
                [
                    'role' => 'user',
                    'content' => "Analyze this legal case:\n\nTitle: $caseTitle\n\nDescription: $caseDescription"
                ]
            ]
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: Bearer $apiKey"
        ]);
        
        // Enable verbose output for debugging
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        error_log("Verbose curl output:\n" . $verboseLog);
        
        curl_close($ch);
        
        error_log("OpenAI Response Code: $httpCode");
        error_log("OpenAI Response Body: $response");
        
        $decoded = json_decode($response, true);
        
        if ($httpCode != 200) {
            return ['error' => "HTTP Error: $httpCode", 'response' => $decoded];
        }
        
        return $decoded;
    }

    function storeAnalysisResult($caseId, $clientId, $analysis) {
        global $connect;
        $userId = $_SESSION['user_id'];
        $lawFirmId = $_SESSION['parent_id'];
        $stmt = $connect->prepare("INSERT INTO case_analyses (caseId, clientId, userId, lawFirmId, analysis, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$caseId, $clientId, $userId, $lawFirmId, $analysis]);
    }

    function getAnalysisCount($caseId) {
        global $connect;
        $stmt = $connect->prepare("SELECT COUNT(*) FROM case_analyses WHERE caseId = ? AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $stmt->execute([$caseId]);
        return $stmt->fetchColumn();
    }

    // AI Documents

    // Function to generate document using ChatGPT API via cURL
    function generateDocument($documentType, $prompt) {
        $apiKey = SECRET_KEY;
        $url = 'https://api.openai.com/v1/chat/completions';

        // Construct the full prompt
        $fullPrompt = "Generate a {$documentType} based on the following description: {$prompt}\n\n";

        // Set headers for the API request
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . 'Bearer ' . $apiKey
        ];

        // Prepare the data payload for the API request
        $data = [
            'model' => 'gpt-4',  // or 'gpt-4' if you have access
            'messages' => [
                ['role' => 'system', 'content' => 'You are a professional legal document generator.'],
                ['role' => 'user', 'content' => $fullPrompt]
            ],
            'max_tokens' => 1000,
            'temperature' => 0.7,
        ];

        // Initialize cURL session
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute the API call
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        // Check for cURL errors
        if ($err) {
            return "cURL Error: " . $err;
        } 

        // Decode the response
        $responseData = json_decode($response, true);

        // Check for API or response errors
        if (isset($responseData['error'])) {
            return "API Error: " . $responseData['error']['message'];
        }

        // Return the generated document content or handle any missing fields
        return $responseData['choices'][0]['message']['content'] ?? "Error: Unable to generate document";
    }

    function createTitle($prompt, $documentType) {
        $words = explode(' ', $prompt);
        $firstThreeWords = implode(' ', array_slice($words, 0, 5));
        return ucfirst($documentType) . ": " . $firstThreeWords . "...";
    }

    function convertMarkdownToHtml($text) {
        $text = preg_replace('/(\*\*|__)(.+?)\1/', '<strong>$2</strong>', $text);
        
        // Convert italic (*text* or _text_)
        $text = preg_replace('/(\*|_)(.+?)\1/', '<em>$2</em>', $text);
        
        // Convert line breaks
        $text = nl2br($text);
        
        return $text;
    }
?>