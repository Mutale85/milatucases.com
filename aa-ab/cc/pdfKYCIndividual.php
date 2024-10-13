<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    include '../../includes/db.php';
    require '../../vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use setasign\Fpdi\Fpdi;


function fetchCompanyInfoForPdf($lawFirmId) {
    global $connect;
    $sql = "SELECT * FROM `company_info` WHERE lawFirmId = ?";
    $stmt = $connect->prepare($sql);
    $stmt->execute([$lawFirmId]);

    $companyInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    $address = html_entity_decode($companyInfo['address']);
    $formatted_address = str_replace(["\r\n", "\n", "\r"], '<br>', $address);

    return [
        'company_name' => $companyInfo['company_name'],
        'address' => $formatted_address,
        'postal_code' => $companyInfo['postal_code'],
        'logo' => fetchLawFirmLogoForPdf($lawFirmId)
    ];
}

function fetchLawFirmLogoForPdf($lawFirmId) {
    global $connect;
    $stmt = $connect->prepare("SELECT `logo` FROM `company_info` WHERE `lawFirmId` = ? ");
    $stmt->execute([$lawFirmId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['logo'] : 'uploads/sampleLogo.png';
}

function fetchClientDetails($lawFirmId, $clientId) {
    global $connect;
    $sql = "SELECT * FROM `individualPart1` WHERE `lawFirmId` = ? AND `clientId` = ?";
    $stmt = $connect->prepare($sql);
    $stmt->execute([$lawFirmId, $clientId]);
    $clientDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    // Decrypt sensitive fields
    foreach ($clientDetails as $key => $value) {
        if ($key != 'date_of_birth' && $key != 'date_of_issue' && $key != 'signature_date') {
            $clientDetails[$key] = decrypt($value);
        }
    }
    return $clientDetails;
}

function fetchClientAdditionalDetails($lawFirmId, $clientId) {
    global $connect;
    $sql = "SELECT * FROM `individualPart2` WHERE `lawFirmId` = ? AND `clientId` = ?";
    $stmt = $connect->prepare($sql);
    $stmt->execute([$lawFirmId, $clientId]);
    $clientAdditionalDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    // Decrypt sensitive fields
    foreach ($clientAdditionalDetails as $key => $value) {
        if ($key != 'signature_date') {
            $clientAdditionalDetails[$key] = decrypt($value);
        }
    }
    return $clientAdditionalDetails;
}

function generatePdf($lawFirmId, $clientId) {
    $companyInfo = fetchCompanyInfoForPdf($lawFirmId);
    $clientDetails = fetchClientDetails($lawFirmId, $clientId);
    $clientAdditionalDetails = fetchClientAdditionalDetails($lawFirmId, $clientId);

    // Create new FPDI instance
    $pdf = new Fpdi();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Add Company Logo
    $logoPath = $companyInfo['logo'];
    if (file_exists($logoPath)) {
        $pdf->Image($logoPath, 10, 10, 50);
    }

    // Add Company Header
    $pdf->Cell(0, 10, $companyInfo['company_name'], 0, 1, 'C');
    $pdf->Cell(0, 10, $companyInfo['address'], 0, 1, 'C');
    $pdf->Cell(0, 10, $companyInfo['postal_code'], 0, 1, 'C');
    $pdf->Ln(10);

    // Add Client Details
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Client Details', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);
    foreach ($clientDetails as $key => $value) {
        $pdf->Cell(0, 10, ucfirst(str_replace('_', ' ', $key)) . ': ' . $value, 0, 1, 'L');
    }

    // Add Client Additional Details
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Client Additional Details', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);
    foreach ($clientAdditionalDetails as $key => $value) {
        $pdf->Cell(0, 10, ucfirst(str_replace('_', ' ', $key)) . ': ' . $value, 0, 1, 'L');
    }

    // Output PDF
    $pdf->Output('I', 'Client_Details.pdf');
}

// Example usage
