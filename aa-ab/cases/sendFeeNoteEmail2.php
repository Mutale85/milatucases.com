<?php
    include '../../includes/db.php';
    require '../../vendor/autoload.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use setasign\Fpdi\Fpdi;

    $lawFirmId = $_SESSION['parent_id'];


    function fetchClientInfoByForPdf($clientId) {
        global $connect, $lawFirmId;
        
        try {
            // Prepare the query to fetch client information by TPIN from lawFirmClients table
            $stmt = $connect->prepare("
                SELECT 
                    client_type,
                    business_name,
                    client_names,
                    client_email
                FROM lawFirmClients
                WHERE id = ? AND lawFirmId = ?
            ");
            $stmt->execute([$clientId, $lawFirmId]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                // Determine the client type and prepare the return array accordingly
                if ($data['client_type'] === 'Corporate') {
                    return [
                        'name' => $data['business_name'],
                        'email' => decrypt($data['client_email']),
                        'type' => 'Corporate'
                    ];
                } else {
                    return [
                        'name' =>decrypt( $data['client_names']),
                        'email' =>decrypt( $data['client_email']),
                        'type' => 'Individual'
                    ];
                }
            } else {
                return ['error' => 'No data found for the provided TPIN.'];
            }
        } catch (PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    function getClientInfo($clientId, $caseNo) {
        $clientData = fetchClientInfoByForPdf($clientId);
        if (isset($clientData['error'])) {
            return $clientData;
        }
        
        return [
            'name' => html_entity_decode($clientData['name']),
            'email' => html_entity_decode($clientData['email']),
            'type' => html_entity_decode($clientData['type']),
            'date' => html_entity_decode(date('d F Y')),
            'caseNo' => html_entity_decode($caseNo)
        ];
    }

    function savePdfMetadata($lawFirmId, $clientId, $caseId, $caseNo, $filePath, $fileSize) {
        global $connect;
        try {
            $stmt = $connect->prepare("INSERT INTO fee_notes (userId, lawFirmId, clientId, case_id, case_no, file_path, file_size) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $lawFirmId, $clientId, $caseId, $caseNo, $filePath, $fileSize]);
        } catch (PDOException $e) {
            die('Error saving PDF metadata: ' . $e->getMessage());
        }
    }

  
    function fetchCompanyInfo($lawFirmId) {
        global $connect;
        try {
            $sql = "SELECT * FROM company_info WHERE lawFirmId = ?";
            $stmt = $connect->prepare($sql);
            $stmt->execute([$lawFirmId]);
            $companyInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$companyInfo) {
                $companyInfo = [
                    'company_name' => 'Default Company Ltd.',
                    'address' => '1234 Default Address',
                    'postal_code' => '000-000-0000'
                ];
            } else {
                // Decode HTML entities for all fields
                foreach ($companyInfo as $key => $value) {
                    $companyInfo[$key] = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
            }
            
            return $companyInfo;
        } catch (PDOException $e) {
            // Handle database errors
            return [
                'error' => 'Database error: ' . htmlspecialchars($e->getMessage())
            ];
        }
    }

    function fetchLawFirmLogo($lawFirmId) {
        global $connect;
        $stmt = $connect->prepare("SELECT `logo` FROM `company_info` WHERE `lawFirmId` = ?");
        $stmt->execute([$lawFirmId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['logo'] : '../settings/uploads/sampleLogo.png';
    }


    function fetchTimerLogs($caseId, $clientId, $lawFirmId) {
        global $connect;
        $sql = "SELECT * FROM `task_billing` WHERE caseId = ? AND clientId = ? AND lawFirmId = ?";
        $stmt = $connect->prepare($sql);
        $stmt->execute([$caseId, $clientId, $lawFirmId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    class FeeNotePDF extends Fpdi {
        private $companyInfo;
        private $clientInfo;
        private $feeNoteDetails;

        public function __construct($companyInfo, $clientInfo, $feeNoteDetails) {
            parent::__construct();
            $this->companyInfo = $companyInfo;
            $this->clientInfo = $clientInfo;
            $this->feeNoteDetails = $feeNoteDetails;
        }
        
        function Header() {
            // Add "UNPAID" at the top
            // $this->SetFont('Arial', 'B', 20);
            // $this->SetTextColor(255, 0, 0); // Red color
            // $this->Cell(0, 10, 'UNPAID', 0, 1, 'C');
            // $this->SetTextColor(0); // Reset to black
            
            $this->Ln(5); // Add some space

            // Add logo
            $logoPath = '../settings/' . $this->companyInfo['logo'];
            try {
                $this->Image($logoPath, 10, $this->GetY(), 50);
            } catch (Exception $e) {
                $this->SetFont('Arial', 'B', 12);
                $this->Cell(0, 10, 'Company Logo (Error loading)', 0, 1);
            }

            // Company information on the left
            $this->SetFont('Arial', '', 10);
            $this->SetXY(10, $this->GetY() + 25);
            $companyInfoText = $this->companyInfo['company_name'] . "\n" .
                               $this->companyInfo['address'] . "\n" .
                               $this->companyInfo['postal_code'];
            $this->MultiCell(90, 5, $companyInfoText);

            // Add Feenote title and number on the right
            $this->SetFont('Arial', 'B', 20);
            $this->SetXY(110, $this->GetY() - 25);
            $this->Cell(90, 10, 'Feenote', 0, 1, 'R');
            $this->SetFont('Arial', '', 10);
            $this->SetXY(110, $this->GetY());
            $this->Cell(90, 10, 'Feenote #: ' . $this->feeNoteDetails['fee_note_number'], 0, 1, 'R');

            $this->SetXY(110, $this->GetY());
            $this->Cell(90, 5, 'Feenote Date: ' . $this->feeNoteDetails['date'], 0, 1, 'R');
            $this->SetX(110);
            $this->Cell(90, 5, 'Due Date: ' . $this->feeNoteDetails['due_date'], 0, 1, 'R');

            // Add a line to separate header from content
            $this->Line(10, $this->GetY() + 10, 200, $this->GetY() + 10);

            $this->Ln(15); // Add some space after the header
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        }

        function AddFeeDetails() {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(30, 10, 'Date', 1, 0, 'C');
            $this->Cell(70, 10, 'Task Description', 1, 0, 'C');
            $this->Cell(30, 10, 'Time (min)', 1, 0, 'C');
            $this->Cell(30, 10, 'Hourly Rate', 1, 0, 'C');
            $this->Cell(30, 10, 'Amount', 1, 1, 'C');

            $this->SetFont('Arial', '', 9);
            $total = 0;
            foreach ($this->feeNoteDetails['items'] as $item) {
                $this->Cell(30, 8, date('d/m/Y', strtotime($item['created_at'])), 1);
                $this->Cell(70, 8, $item['description'], 1);
                $this->Cell(30, 8, $item['elapsed_time'], 1, 0, 'C');
                $this->Cell(30, 8, number_format($item['hourly_rate'], 2), 1, 0, 'R');
                $this->Cell(30, 8, number_format($item['total_amount'], 2), 1, 1, 'R');
                $total += $item['total_amount'];
            }

            $this->SetFont('Arial', 'B', 10);
            $this->Cell(160, 10, 'Total (ZMW)', 1, 0, 'R');
            $this->Cell(30, 10, number_format($total, 2), 1, 1, 'R');
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $caseId = $_POST['caseId'];
        $clientId = $_POST['clientId'];
        $clientEmail = $_POST['clientEmail'];
        $caseNo = fetchCaseNoById($caseId);

        // Fetch company info
        $companyInfo = fetchCompanyInfo($lawFirmId);
        $companyInfo['logo'] = fetchLawFirmLogo($lawFirmId);

        // Fetch client info
        $clientInfo = getClientInfo($clientId, $caseNo);

        // Check if there was an error fetching client info
        if (isset($clientInfo['error'])) {
            echo json_encode(['success' => false, 'error' => $clientInfo['error']]);
            exit;
        }

        // Fetch timer logs
        $timerLogs = fetchTimerLogs($caseId, $clientId, $lawFirmId);

        // Prepare fee note details
        $feeNoteDetails = [
            'fee_note_number' => 'FN-' . date('Ymd') . '-' . $caseId,
            'date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'items' => $timerLogs
        ];

        // Create PDF
        $pdf = new FeeNotePDF($companyInfo, $clientInfo, $feeNoteDetails);
        $pdf->AddPage();
        $pdf->AddFeeDetails();


        $fileName = $feeNoteDetails['fee_note_number'] . '.pdf';
        $filePath = 'feenotes/' . $fileName;
        $pdf->Output('F', $filePath);

        $fileSize = filesize($filePath);
        savePdfMetadata($lawFirmId, $clientId, $caseId, $caseNo, $filePath, $fileSize);
        sendEmailWithAttachment($clientEmail, $filePath, $clientId, $lawFirmId);

    }
    

    function sendEmailWithAttachment($clientEmail, $pdfFilename, $clientId, $lawFirmId) {
        $clientNames = getClientNameById($clientId, $lawFirmId);
        $company = fetchCompanyName();
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mutamuls@gmail.com';
            $mail->Password = 'mdbm npox ftcj ougf'; // Consider using environment variables for credentials
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('mutamuls@gmail.com', 'Feenote');
            $mail->addAddress($clientEmail);

            // Attachments
            $mail->addAttachment($pdfFilename);
            $lawFirmName = $_SESSION['lawFirmName'];
            // Content
            $mail->isHTML(true);
            $mail->Subject = "Your Feenote from $lawFirmName";
            
            // Personalized email body
            $mail->Body = '
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    
                    .footer { margin-top: 20px; font-size: 12px; color: #888; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h2>Dear, '.$clientNames.'</h2>
                    <p>We hope this message finds you well.</p>
                    <p>Please find attached the Feenote document for the recent works. If you have any questions or need further assistance, feel free to reach out to us.</p>
                    <p>Thank you for your business!</p>
                    <p>Best regards,</p>
                    
                    <div class="footer">
                        '.$company.'
                    </div>
                </div>
            </body>
            </html>';

            $mail->send();
            echo "The Feenote has been successfully created and sent to $clientEmail.";
            // Optionally delete the file after sending
            // unlink($pdfFilename);
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
