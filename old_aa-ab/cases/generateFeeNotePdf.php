 <?php

    include '../../includes/db.php';
    require '../../vendor/autoload.php';

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
                if ($data['client_type'] === 'corporate') {
                    return [
                        'name' => html_entity_decode($data['business_name']),
                        'email' => html_entity_decode(decrypt($data['client_email'])),
                        'type' => 'Corporate'
                    ];
                } else {
                    return [
                        'name' =>html_entity_decode(decrypt($data['client_names'])),
                        'email' =>html_entity_decode(decrypt($data['client_email'])),
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
            $stmt = $connect->prepare("INSERT INTO fee_notes (userid, lawFirmId, clientId, case_id, case_no, file_path, file_size) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $lawFirmId, $clientId, $caseId, $caseNo, $filePath, $fileSize]);
        } catch (PDOException $e) {
            die('Error saving PDF metadata: ' . $e->getMessage());
        }
    }

    function fetchCompanyInfo($lawFirmId) {
        global $connect;

        try {
            // Prepare and execute the SQL query to fetch company information
            $sql = "SELECT * FROM company_info WHERE lawFirmId = ?";
            $stmt = $connect->prepare($sql);
            $stmt->execute([$lawFirmId]);
            $companyInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // If no company info is found, use default fake company info
            if (!$companyInfo) {
                $companyInfo = [
                    'company_name' => 'Default Company Ltd.',
                    'address' => '1234 Default Address',
                    'postal_code' => '000-000-0000'
                ];
            } else {
                // Decode HTML entities in the address
                $companyInfo['address'] = html_entity_decode($companyInfo['address'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
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
            // Add logo
            $logoPath = '../settings/' . $this->companyInfo['logo'];
            try {
                $this->Image($logoPath, 10, 10, 50);
            } catch (Exception $e) {
                $this->SetFont('Arial', 'B', 12);
                $this->SetXY(10, 10);
                $this->Cell(0, 10, 'Company Logo (Error loading)', 0, 1);
            }

            $this->SetFont('Arial', '', 10);
            $this->SetXY(10, 35);
            $companyInfoText = $this->companyInfo['company_name'] . "\n" .
                               $this->companyInfo['address'] . "\n" .
                               $this->companyInfo['postal_code'];
            $this->MultiCell(90, 5, $companyInfoText);

            // Add fee note title and number
            $this->SetFont('Arial', 'B', 16);
            $this->SetXY(110, 10);
            $this->Cell(90, 10, 'FEE NOTE', 0, 1, 'R');
            $this->SetFont('Arial', 'B', 12);
            $this->SetXY(110, 20);
            $this->Cell(90, 10, 'Fee Note #: ' . $this->feeNoteDetails['fee_note_number'], 0, 1, 'R');

            // Add fee note details
            $this->SetFont('Arial', '', 10);
            $this->SetXY(110, 35);
            $this->Cell(90, 5, 'Date: ' . $this->feeNoteDetails['date'], 0, 1, 'R');
            $this->SetX(110);
            $this->Cell(90, 5, 'Due Date: ' . $this->feeNoteDetails['due_date'], 0, 1, 'R');

            // Add client information
            $this->SetXY(10, 70);
            $this->SetFont('Arial', 'B', 11);
            $this->Cell(0, 7, 'Bill To:', 0, 1);
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 5, 'Attention: ' . $this->clientInfo['name'], 0, 1);
            $this->Cell(0, 5, 'Email: ' . $this->clientInfo['email'], 0, 1);
            // $this->Cell(0, 5, 'Address: ' . $this->companyInfo['address'], 0, 1);
            $this->Cell(0, 5, 'Date: ' . $this->clientInfo['date'], 0, 1);
            $this->Cell(0, 5, 'Case/File No: ' . $this->clientInfo['caseNo'], 0, 1);

            $this->Ln(10);
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
            $this->Cell(160, 10, 'Total', 1, 0, 'R');
            $this->Cell(30, 10, number_format($total, 2), 1, 1, 'R');
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $caseId = $_POST['caseId'];
        $clientId = $_POST['clientId']; // This should be the TPIN
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

        // $pdfFilename = 'fee_note_' . time() . '.pdf';
        // $pdf->Output('F', $pdfFilename);

        $fileName = $feeNoteDetails['fee_note_number'] . '.pdf';
        $filePath = 'feenotes/' . $fileName;
        $pdf->Output('F', $filePath);

        $fileSize = filesize($filePath);
        savePdfMetadata($lawFirmId, $clientId, $caseId, $caseNo, $filePath, $fileSize);


        echo json_encode(['success' => true, 'pdfUrl' => $filePath]);
    }


?>
