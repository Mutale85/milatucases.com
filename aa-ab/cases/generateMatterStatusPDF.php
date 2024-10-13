<?php
include '../../includes/db.php';
require_once('../../tcpdf/tcpdf.php');
include '../../vendor/autoload.php';
$lawFirmId = $_SESSION['parent_id'];
$author = $_SESSION['lawFirmName'];
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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


class MYPDF extends TCPDF {
    private $isFirstPage = true;

    // Page header
    public function Header() {
        if ($this->isFirstPage) {
            $companyInfo = fetchCompanyInfo($_SESSION['parent_id']);
            $coy = $companyInfo['company_name'];
            $address = htmlspecialchars(html_entity_decode($companyInfo['address'])) . ", " . $companyInfo['postal_code'];
            $phone = $companyInfo['telephone'];
            $tpin = $companyInfo['tpin'];
            $address = $companyInfo['address'];
            $email = $companyInfo['email'];
            $website = $companyInfo['website'];
            // Add padding at the top
            $this->SetY(15);

            // Logo
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

            // Company info
            $this->SetX(70);
            $this->SetFont('helvetica', 'B', 18);
            $this->SetTextColor(51, 51, 51); // Dark grey
            $this->Cell(0, 10, $coy, 0, 1, 'R', 0, '', 0, false, 'M', 'M');
            $this->SetFont('helvetica', '', 11);
            $this->SetTextColor(51, 51, 51); // Dark grey
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

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($author);
$pdf->SetTitle('Matter Consolidation');
$pdf->SetSubject('Case Details');
$pdf->SetKeywords('PDF, case, matter');

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    function createPDF($caseId, $lawFirmId, $clientId, $from, $end, &$pdf) {
        global $connect;
        $start = date("D d M, Y", strtotime($from));
        $to = date("D d M, Y", strtotime($end));

        $pdf->setAutoPageBreak(TRUE, 10);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->AddPage();

        // Fetch case details
        $query = $connect->prepare("SELECT * FROM `cases` WHERE id = ? AND clientId = ?");
        $query->execute([$caseId, $clientId]);
        $caseDetails = $query->fetch(PDO::FETCH_ASSOC);
        $matterNo = $caseDetails['caseNo'];
        $pdf->Ln(35);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(10, $pdf->GetY(), $pdf->GetPageWidth() - 10, $pdf->GetY());
        $pdf->Ln(25);

        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, "Matter No: $matterNo Report", 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 10, "From: $start, To :$to", 0, 1, 'L');
        $pdf->Ln(15);

        $pdf->SetFont('helvetica', '', 11);
        
        if($caseDetails['causeId'] != null || $caseDetails['causeId'] != ""){
            $html1 = "<h4>Cause ID: {$caseDetails['causeId']} | Matter / File No: {$caseDetails['caseNo']} </h4>"; 
        }else{
            $html1 = "<h4> Matter / File No: {$caseDetails['caseNo']} </h4>";
        }

        $caseTitle = html_entity_decode(decrypt($caseDetails['caseTitle']));
        $html2 = "<h3>Matter Title</h3><br>" . $caseTitle;
        $pdf->Ln(3);
        $description = html_entity_decode(decrypt($caseDetails['caseDescription']));
        $html3 = "<h3>Matter Description</h3><br>" . $description;

        $pdf->writeHTMLCell(0, 0, '', '', $html1 . $html2 . $html3, 0, 1, 0, true, '', true);
        $pdf->Ln();

        // Progress Work
        $queryMilestones = $connect->prepare("
            SELECT * 
            FROM case_milestones 
            WHERE caseId = ? 
            AND created_at BETWEEN ? AND ?
            ORDER BY created_at DESC
        ");
        $queryMilestones->execute([$caseId, $from, $end]);

        if ($queryMilestones->rowCount() > 0) {
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 12);

            $html = '
            <h4>Status Updates</h4>
            <table border="0" cellpadding="5">
                <tr style="background-color: #f2f2f2;">
                    <th width="25%">Date</th>
                    <th width="75%">Description</th>
                </tr>
            ';

            $pdf->SetFont('helvetica', '', 10);

            while ($row = $queryMilestones->fetch(PDO::FETCH_ASSOC)) {
                $dates = date("D d M, Y H:i A", strtotime($row['created_at']));
                $milestoneTitle = html_entity_decode(decrypt($row['milestoneTitle']));
                $milestoneDescription = html_entity_decode(decrypt($row['milestoneDescription']));
                $addedBy = fetchLawFirmUserName($row['userId'], $lawFirmId);

                $html .= "
                <tr>
                    <td>{$dates}<br>({$addedBy})</td>
                    <td><strong>{$milestoneTitle}</strong><br>{$milestoneDescription}</td>
                </tr>
                ";
            }

            $html .= '</table>';
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
        }


        $queryStatus = $connect->prepare("
            SELECT DISTINCT(case_status), userId, date_added 
            FROM case_status 
            WHERE caseId = ? AND date_added BETWEEN ? AND ?
            ORDER BY date_added ASC
        ");
        $queryStatus->execute([$caseId, $from, $end]);
        if($queryStatus->rowCount() > 0){
            // Matter Status
            $pdf->AddPage();
            $pdf->SetFont('helvetica', 'B', 12);
            $html = '<h4>Matter Status</h4>';
            $pdf->SetFont('helvetica', '', 11);
            while ($row = $queryStatus->fetch(PDO::FETCH_ASSOC)) {
                $status = $row['case_status'];
                $statusDate = date("D d, M, Y H:i A", strtotime($row['date_added']));
                $html .= "$status ($statusDate) ->";
            }

            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
        }

        $pdfFilename = __DIR__ . '/matters/matter_' . $caseId . '.pdf';
        $pdf->Output($pdfFilename, 'F');

        if (!file_exists($pdfFilename)) {
            error_log("Failed to create PDF file: $pdfFilename");
            return false;
        }

        return $pdfFilename;
    }

    if (isset($_POST['caseId']) && isset($_POST['clientId']) && isset($_POST['from']) && isset($_POST['end'])) {
        $caseId = $_POST['caseId'];
        $clientId = $_POST['clientId'];
        $from = $_POST['from'];
        $end = $_POST['end'];
       
        $pdfFilename = createPDF($caseId, $lawFirmId, $clientId, $from, $end, $pdf);
        $fileName = 'matters/matter_' . $caseId . '.pdf';
        if ($pdfFilename) {
            echo json_encode(['success' => true, 'pdfUrl' => $fileName]);
        } else {
            echo "Failed to create PDF.";
        }
    } else {
        echo "Missing parameters.";
    }

