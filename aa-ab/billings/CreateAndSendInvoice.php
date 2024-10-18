<?php
include '../../includes/db.php';
require '../../vendor/autoload.php';

use setasign\Fpdi\Fpdi;

function fetchLawFirmLogoForPdf($lawFirmId) {
    global $connect;
    $stmt = $connect->prepare("SELECT `logo` FROM `company_info` WHERE `lawFirmId` = ? ");
    $stmt->execute([$lawFirmId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $output = $row['logo'];
    } else {
        $output = 'uploads/sampleLogo.png';
    }
    return $output;
}

function generateInvoicePDF($invoiceId, $lawFirmId) {
    global $connect;

    // Fetch invoice data
    $invoice_query = $connect->prepare("SELECT * FROM `invoices` WHERE `id` = ?");
    $invoice_query->execute([$invoiceId]);
    $invoice = $invoice_query->fetch(PDO::FETCH_ASSOC);

    if (!$invoice) {
        return ['error' => 'Invoice not found'];
    }

    // Fetch invoice items
    $items_query = $connect->prepare("
        SELECT `id`, `invoice_id`, `description`, `quantity`, `price`, `total` 
        FROM `invoice_items` 
        WHERE `invoice_id` = ?
    ");
    $items_query->execute([$invoiceId]);
    $invoice_items = $items_query->fetchAll(PDO::FETCH_ASSOC);

    $clientInfo = fetchClientInfoById($invoice['clientId']);

    // Fetch company info
    $company_query = $connect->prepare("SELECT * FROM company_info WHERE lawFirmId = ?");
    $company_query->execute([$lawFirmId]);
    $company_info = $company_query->fetch(PDO::FETCH_ASSOC);

    // Create PDF
    $pdf = new Fpdi();
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(true, 15);

    // Add logo on the left
    $logo_path = fetchLawFirmLogoForPdf($lawFirmId);
    if (file_exists('../settings/' . $logo_path)) {
        $pdf->Image('../settings/' . $logo_path, 10, 10, 30);
    }

    // Company info on the right
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetXY(100, 10);
    $pdf->Cell(0, 6, $company_info['company_name'], 0, 1, 'R');
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetX(100);
    $pdf->Cell(0, 5, 'TPIN: ' . $company_info['tpin'], 0, 1, 'R');
    $pdf->SetX(100);
    $pdf->Cell(0, 5, 'Address: ' . $company_info['address'], 0, 1, 'R');
    $pdf->SetX(100);
    $pdf->Cell(0, 5, 'Postal Code: ' . $company_info['postal_code'], 0, 1, 'R');
    $pdf->SetX(100);
    $pdf->Cell(0, 5, 'Telephone: ' . $company_info['telephone'], 0, 1, 'R');
    $pdf->SetX(100);
    $pdf->Cell(0, 5, 'Email: ' . $company_info['email'], 0, 1, 'R');
    $pdf->SetX(100);
    $pdf->Cell(0, 5, 'Website: ' . $company_info['website'], 0, 1, 'R');
    $pdf->SetX(100);
    $pdf->Cell(0, 5, 'LinkedIn: ' . $company_info['linkedin'], 0, 1, 'R');

    // Add a line below the header information
    $pdf->Line(10, 55, 200, 55);

    // Client info
    $pdf->SetY(60);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 6, 'Bill To:', 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, $clientInfo['name'], 0, 1);
    $pdf->Cell(0, 5, 'Tpin: ' . $clientInfo['client_tpin'], 0, 1);
    $pdf->Cell(0, 5, 'Email: ' . $clientInfo['email'], 0, 1);
    $pdf->Cell(0, 5, 'Phone: ' . $clientInfo['Phone'], 0, 1);
    $pdf->Cell(0, 5, 'Address: ' . $clientInfo['address'], 0, 1);

    // Invoice details
    $pdf->SetXY(120, 60);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 6, 'Invoice#: ' . $invoice['invoice_number'], 0, 1, 'R');
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetX(120);
    $pdf->Cell(0, 5, 'Invoice Date: ' . date('F j, Y', strtotime($invoice['date'])), 0, 1, 'R');
    $pdf->SetX(120);
    $pdf->Cell(0, 5, 'Due Date: ' . date('F j, Y', strtotime($invoice['due_date'])), 0, 1, 'R');

    // Invoice items
    $pdf->SetY(100);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 6, 'Invoice Items:', 0, 1);
    
    $pdf->Cell(90, 6, 'Description', 1);
    $pdf->Cell(25, 6, 'Quantity', 1);
    $pdf->Cell(35, 6, 'Unit Price', 1);
    $pdf->Cell(40, 6, 'Total', 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 10);
    foreach ($invoice_items as $item) {
        $pdf->Cell(90, 6, html_entity_decode(decrypt($item['description'])), 1);
        $pdf->Cell(25, 6, $item['quantity'], 1);
        $pdf->Cell(35, 6, formatCurrency($item['price']), 1);
        $pdf->Cell(40, 6, formatCurrency($item['total']), 1);
        $pdf->Ln();
    }

    // Subtotals (attached to the table)
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(150, 6, 'Subtotal:', 0);
    $pdf->Cell(40, 6, formatCurrency($invoice['subtotal']), 0, 1, 'R');
    $pdf->Cell(150, 6, 'Tax:', 0);
    $pdf->Cell(40, 6, formatCurrency($invoice['tax']), 0, 1, 'R');
    $pdf->Cell(150, 6, 'Tax Rate:', 0);
    $pdf->Cell(40, 6, '(' . $invoice['tax_rate'] . '%)', 0, 1, 'R');
    $pdf->Cell(150, 6, 'Amount Paid:', 0);
    $pdf->Cell(40, 6, formatCurrency($invoice['amountPaid']), 0, 1, 'R');
    $pdf->Cell(150, 6, 'Balance:', 0);
    $pdf->Cell(40, 6, formatCurrency($invoice['remainingBalance']), 0, 1, 'R');

    // Issued By and Notes
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, 'Issued By: ' . fetchLawFirmMemberNames($invoice['createdBy']), 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, html_entity_decode(decrypt($invoice['notes'])));

    // Terms and Conditions
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, 'Terms and Conditions:', 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->MultiCell(0, 5, html_entity_decode(decrypt($invoice['terms'])));

    $pdfPath = 'invoices/invoice_' . time() . '.pdf';
    $pdf->Output('F', $pdfPath);

    return $pdfPath;
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $invoiceId = $_POST['invoiceId'];
    $lawFirmId = $_POST['lawFirmId'];
    $pdfPath = generateInvoicePDF($invoiceId, $lawFirmId);

    if (isset($pdfPath['error'])) {
        echo json_encode(['success' => false, 'message' => $pdfPath['error']]);
    } else {
        echo json_encode(['success' => true, 'message' => 'PDF generated successfully', 'path' => $pdfPath]);
    }
}
?>