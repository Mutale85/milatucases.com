<?php
include '../../includes/db.php';
require_once('../../tcpdf/tcpdf.php');
include '../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$lawFirmId = $_SESSION['parent_id'];
$author = $_SESSION['lawFirmName'];

function fetchClientInfoByForPdf($clientId) {
    global $connect, $lawFirmId;

    try {
        $stmt = $connect->prepare("SELECT * FROM lawFirmClients WHERE id = ? AND lawFirmId = ?");
        $stmt->execute([$clientId, $lawFirmId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            if ($row['client_type'] === 'Corporate') {
                return [
                    'name' => html_entity_decode($row['business_name']),
                    'email' => html_entity_decode(decrypt($row['client_email'])),
                    'Phone' => html_entity_decode(decrypt($row['client_phone'])),
                    'address' => html_entity_decode(decrypt($row['address'])),
                    'type' => 'Corporate'
                ];
            } else {
                return [
                    'name' => html_entity_decode(decrypt($row['client_names'])),
                    'email' => html_entity_decode(decrypt($row['client_email'])),
                    'Phone' => html_entity_decode(decrypt($row['client_phone'])),
                    'address' => html_entity_decode(decrypt($row['address'])),
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
            foreach ($companyInfo as $key => $value) {
                $companyInfo[$key] = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }

        return $companyInfo;
    } catch (PDOException $e) {
        error_log("Database error in fetchCompanyInfo: " . $e->getMessage());
        return [
            'error' => 'Database error: ' . htmlspecialchars($e->getMessage())
        ];
    }
}

function fetchLawFirmLogo($lawFirmId) {
    global $connect;
    $stmt = $connect->prepare("SELECT `logo` FROM `company_info` WHERE `lawFirmId` = ?");
    $stmt->execute([$lawFirmId]);
    if($stmt->rowCount() > 0){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $logoFile = '../settings/'.$row['logo'];
        $fileExtension = pathinfo($logoFile, PATHINFO_EXTENSION);
        return $logoFile;
    }else{
        return '../settings/uploads/sampleLogo.png';
    }
}

function fetchTimerLogs($caseId, $clientId, $lawFirmId, $startDate = null, $endDate = null) {
    global $connect;
    
    $sql = "SELECT * FROM `time_entries` 
            WHERE caseId = ? AND clientId = ? AND lawFirmId = ? AND billableStatus = 'Billable'";
    
    $params = [$caseId, $clientId, $lawFirmId];
    
    if ($startDate && $endDate) {
        $sql .= " AND dateCreated BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
    }
    
    $sql .= " ORDER BY dateCreated DESC";
    
    $stmt = $connect->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetchCaseNoByIdPdf($caseId) {
    global $connect;
    try {
        $stmt = $connect->prepare("SELECT case_number FROM cases WHERE id = ?");
        $stmt->execute([$caseId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['case_number'] : null;
    } catch (PDOException $e) {
        return null;
    }
}

class MYPDF extends TCPDF {
    private $isFirstPage = true;

    public function Header() {
        if ($this->isFirstPage) {
            $companyInfo = fetchCompanyInfo($_SESSION['parent_id']);
            $coy = $companyInfo['company_name'];
            $address = htmlspecialchars(html_entity_decode($companyInfo['address'])) . ", " . $companyInfo['postal_code'];
            $phone = $companyInfo['telephone'];
            $tpin = $companyInfo['tpin'];
            $email = $companyInfo['email'];
            $website = $companyInfo['website'];
            
            $this->SetY(15);

            $image_file = fetchLawFirmLogo($_SESSION['parent_id']);
            $fileExtension = pathinfo($image_file, PATHINFO_EXTENSION);
            switch ($fileExtension) {
                case 'jpg':
                case 'jpeg':
                    $this->Image($image_file, 10, 15, 50, '', 'JPG', '', 'T', false, 150, '', false, false, 0, false, false, false);
                    break;
                case 'png':
                    $this->Image($image_file, 10, 15, 50, '', 'PNG', '', 'T', false, 150, '', false, false, 0, false, false, false);
                    break;
                case 'gif':
                    $this->Image($image_file, 10, 15, 50, '', 'GIF', '', 'T', false, 150, '', false, false, 0, false, false, false);
                    break;
                default:
                    $this->Image('../settings/uploads/sampleLogo.png', 10, 15, 50, '', 'PNG', '', 'T', false, 150, '', false, false, 0, false, false, false);
                    break;
            }

            $this->SetX(70);
            $this->SetFont('dejavusans', 'B', 18);
            $this->SetTextColor(51, 51, 51);
            $this->Cell(0, 10, $coy, 0, 1, 'R', 0, '', 0, false, 'M', 'M');
            $this->SetFont('dejavusans', '', 11);
            $this->SetTextColor(51, 51, 51);
            $this->SetX(70);
            $this->MultiCell(0, 8, $address, 0, 'R', 0, 1, '', '', true);
            $this->SetX(70);
            $this->MultiCell(0, 8, "Tpin: $tpin", 0, 'R', 0, 1, '', '', true);
            $this->SetX(70);
            $this->MultiCell(0, 8, "Phone: $phone", 0, 'R', 0, 1, '', '', true);
            $this->SetX(70);
            $this->MultiCell(0, 8, "Email: $email", 0, 'R', 0, 1, '', '', true);
            $this->SetX(70);
            $this->MultiCell(0, 8, "Website: $website", 0, 'R', 0, 1, '', '', true);
        }
        $this->isFirstPage = false;
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('dejavusans', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $caseId = $_POST['caseId'];
    $clientId = $_POST['clientId'];
    $clientEmail = $_POST['emailInput'];
    $startDate = $_POST['startDate'] ?? null;
    $endDate = $_POST['endDate'] ?? null;

    $caseNo = fetchCaseNoByIdPdf($caseId);

    $companyInfo = fetchCompanyInfo($lawFirmId);
    if (isset($companyInfo['error'])) {
        echo json_encode(['success' => false, 'error' => $companyInfo['error']]);
        exit;
    }

    $clientInfo = fetchClientInfoByForPdf($clientId);
    $clientInfo['caseNo'] = $caseNo;
    if (isset($clientInfo['error'])) {
        echo json_encode(['success' => false, 'error' => $clientInfo['error']]);
        exit;
    }

    $timerLogs = fetchTimerLogs($caseId, $clientId, $lawFirmId, $startDate, $endDate);

    if (empty($timerLogs)) {
        echo json_encode(['success' => false, 'error' => 'No billable entries found for the selected date range.']);
        exit;
    }

    $feeNoteDetails = [
        'fee_note_number' => 'FN-' . date('Ymd') . '-' . $caseId,
        'date' => date('Y-m-d'),
        'due_date' => date('Y-m-d', strtotime('+30 days')),
        'items' => $timerLogs,
        'start_date' => $startDate,
        'end_date' => $endDate
    ];

    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor($author);
    $pdf->SetTitle('Fee Note');
    $pdf->SetSubject('Fee Note Details');
    $pdf->SetKeywords('PDF, fee note, billing');

    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    $pdf->SetFont('dejavusans', '', 10);

    $pdf->AddPage();

    $pdf->Ln(40);
    $pdf->SetFont('dejavusans', 'B', 16);
    $pdf->Cell(0, 10, 'FEE NOTE', 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('dejavusans', '', 10);
    $pdf->Cell(0, 5, 'Fee Note #: ' . $feeNoteDetails['fee_note_number'], 0, 1);
    $pdf->Cell(0, 5, 'Date: ' . date('D d M, Y', strtotime($feeNoteDetails['date'])), 0, 1);
    $pdf->Cell(0, 5, 'Due Date: ' . date('D d M, Y', strtotime($feeNoteDetails['due_date'])), 0, 1);

    if (!empty($feeNoteDetails['start_date']) && !empty($feeNoteDetails['end_date'])) {
        $pdf->Cell(0, 5, 'Period: ' . date('D d M, Y', strtotime($feeNoteDetails['start_date'])) . ' to ' . date('D d M, Y', strtotime($feeNoteDetails['end_date'])), 0, 1);
    }

    $pdf->Ln(5);

    $pdf->SetFont('dejavusans', 'B', 12);
    $pdf->Cell(0, 7, 'Bill To:', 0, 1);
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->Cell(0, 5, 'Attention: ' . $clientInfo['name'], 0, 1);
    $pdf->Cell(0, 5, 'Email: ' . $clientInfo['email'], 0, 1);
    $pdf->Cell(0, 5, 'Phone: ' . $clientInfo['Phone'], 0, 1);
    $pdf->MultiCell(0, 5, 'Address: ' . $clientInfo['address'], 0, 'L');
    $pdf->Cell(0, 5, 'Matter/File No: ' . $clientInfo['caseNo'], 0, 1);

    $pdf->Ln(5);

    $pdf->SetFillColor(200, 220, 255);
    $pdf->SetFont('dejavusans', 'B', 10);
    $pdf->Cell(25, 7, 'Date', 1, 0, 'C', 1, '', 0, '', '', '', '', 'T', 'M', 2);
    $pdf->Cell(100, 7, 'Task Description', 1, 0, 'C', 1, '', 0, '', '', '', '', 'T', 'M', 2);
    $pdf->Cell(20, 7, 'Time', 1, 0, 'C', 1, '', 0, '', '', '', '', 'T', 'M', 2);
    $pdf->Cell(25, 7, 'Rate/Hr', 1, 0, 'C', 1, '', 0, '', '', '', '', 'T', 'M', 2);
    $pdf->Cell(20, 7, 'Amount', 1, 1, 'C', 1, '', 0, '', '', '', '', 'T', 'M', 2);

    $pdf->SetFont('dejavusans', '', 9);
    $total = 0;
    foreach ($feeNoteDetails['items'] as $item) {
        $time = sprintf('%02d:%02d', $item['hours'], $item['minutes']);
        $hourlyRate = number_format($item['hourlyRate'], 2);
        $amount = number_format($item['cost'], 2);
        $description = htmlspecialchars(html_entity_decode(decrypt($item['description'])));

        $descriptionHeight = $pdf->getStringHeight(96, $description);
        $rowHeight = max($descriptionHeight, 7);

        $startY = $pdf->GetY();
        $pdf->MultiCell(25, $rowHeight, date('d-M-Y', strtotime($item['dateCreated'])), 1, 'C', false, 0, '', '', true, 0, false, true, $rowHeight, 'M');
        $pdf->MultiCell(100, $rowHeight, $description, 1, 'L', false, 0, '', '', true, 0, false, true, $rowHeight, 'M');
        $pdf->MultiCell(20, $rowHeight, $time, 1, 'C', false, 0, '', '', true, 0, false, true, $rowHeight, 'M');
        $pdf->MultiCell(25, $rowHeight, $hourlyRate, 1, 'R', false, 0, '', '', true, 0, false, true, $rowHeight, 'M');
        $pdf->MultiCell(20, $rowHeight, $amount, 1, 'R', false, 1, '', '', true, 0, false, true, $rowHeight, 'M');

        $total += $item['cost'];

        if ($pdf->GetY() > 250) {
            $pdf->AddPage();
            
            $pdf->SetFillColor(200, 220, 255);
            $pdf->SetFont('dejavusans', 'B', 10);
            $pdf->Cell(25, 7, 'Date', 1, 0, 'C', 1, '', 0, '', '', '', '', 'T', 'M', 2);
            $pdf->Cell(100, 7, 'Task Description', 1, 0, 'C', 1, '', 0, '', '', '', '', 'T', 'M', 2);
            $pdf->Cell(20, 7, 'Time', 1, 0, 'C', 1, '', 0, '', '', '', '', 'T', 'M', 2);
            $pdf->Cell(25, 7, 'Rate/Hr', 1, 0, 'C', 1, '', 0, '', '', '', '', 'T', 'M', 2);
            $pdf->Cell(20, 7, 'Amount', 1, 1, 'C', 1, '', 0, '', '', '', '', 'T', 'M', 2);
            $pdf->SetFont('dejavusans', '', 9);
        }
    }

    $pdf->SetFont('dejavusans', 'B', 9);
    $pdf->Cell(170, 7, 'Total', 1, 0, 'R', 0, '', 0, '', '', '', '', 'T', 'M', 2);
    $pdf->Cell(20, 7, number_format($total, 2), 1, 1, 'R', 0, '', 0, '', '', '', '', 'T', 'M', 2);

    $fileName = 'fee_note_' . $feeNoteDetails['fee_note_number'] . '.pdf';
    $filePath = __DIR__ . '/matters/' . $fileName;

    $directory = __DIR__ . '/matters';
    if (!is_dir($directory)) {
        if (!mkdir($directory, 0755, true)) {
            error_log("Failed to create directory: $directory");
            echo json_encode(['success' => false, 'error' => "Failed to create directory for PDF storage."]);
            exit;
        }
    }

    if (!is_writable($directory)) {
        error_log("Directory is not writable: $directory");
        echo json_encode(['success' => false, 'error' => "PDF storage directory is not writable."]);
        exit;
    }

    try {
        $pdf->Output($filePath, 'F');
    } catch (Exception $e) {
        error_log("TCPDF Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => "Failed to create PDF: " . $e->getMessage()]);
        exit;
    }

    if (!file_exists($filePath)) {
        error_log("PDF file was not created: $filePath");
        echo json_encode(['success' => false, 'error' => "PDF file was not created."]);
        exit;
    }

    $fileSize = filesize($filePath);

    // Uncomment the following line if you want to save PDF metadata
    // savePdfMetadata($lawFirmId, $clientId, $caseId, $caseNo, $filePath, $fileSize);

    $emailResult = sendEmailWithAttachment($clientEmail, $filePath, $clientId, $lawFirmId, $startDate, $endDate);
    
    if ($emailResult === true) {
        echo json_encode(['success' => true, 'message' => "The Feenote has been successfully created and sent to $clientEmail."]);
    } else {
        echo json_encode(['success' => false, 'error' => $emailResult]);
    }
}

function sendEmailWithAttachment($clientEmail, $pdfFilePath, $clientId, $lawFirmId, $startDate, $endDate) {
    $clientNames = getClientNameById($clientId, $lawFirmId);
    $company = fetchCompanyName();
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.zoho.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'support@milatucases.com';
        $mail->Password = 'Javeria##2019'; // Consider using environment variables for credentials
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('support@milatucases.com', 'Feenote');
        $mail->addAddress($clientEmail);

        $mail->addAttachment($pdfFilePath);
        $lawFirmName = $_SESSION['lawFirmName'];
        
        $mail->isHTML(true);
        $mail->Subject = "Your Feenote from $lawFirmName";

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
                <h2>Dear '.$clientNames.',</h2>
                <p>We hope this message finds you well.</p>
                <p>Please find attached the Feenote document for the period from '.date('d-M-Y', strtotime($startDate)).' to '.date('d-M-Y', strtotime($endDate)).'.</p>
                <p>If you have any questions or need further assistance, feel free to reach out to us.</p>
                <p>Thank you for your business!</p>
                <p>Best regards,</p>
                <div class="footer">
                    '.$company.'
                </div>
            </div>
        </body>
        </html>';

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
/*
function getClientNameById($clientId, $lawFirmId) {
    global $connect;
    try {
        $stmt = $connect->prepare("SELECT client_names, business_name, client_type FROM lawFirmClients WHERE id = ? AND lawFirmId = ?");
        $stmt->execute([$clientId, $lawFirmId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            if ($result['client_type'] === 'Corporate') {
                return html_entity_decode($result['business_name']);
            } else {
                return html_entity_decode(decrypt($result['client_names']));
            }
        }
        return 'Valued Client'; // Default if not found
    } catch (PDOException $e) {
        error_log("Database error in getClientNameById: " . $e->getMessage());
        return 'Valued Client'; // Default in case of error
    }
}

function fetchCompanyName() {
    global $connect, $lawFirmId;
    try {
        $stmt = $connect->prepare("SELECT company_name FROM company_info WHERE lawFirmId = ?");
        $stmt->execute([$lawFirmId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? html_entity_decode($result['company_name']) : 'Your Company';
    } catch (PDOException $e) {
        error_log("Database error in fetchCompanyName: " . $e->getMessage());
        return 'Your Company'; // Default in case of error
    }
}
*/


?>