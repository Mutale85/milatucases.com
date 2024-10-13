<?php


require '../../vendor/autoload.php';

// Path to your Word document
$docxFile = '../uploads/' . $_GET['file'];

// Verify the file exists
if (!file_exists($docxFile)) {
    die('File not found.');
}

// Debugging: Output the file path
echo "File Path: $docxFile<br>";

try {
    // Load the Word document
    $phpWord = \PhpOffice\PhpWord\IOFactory::load($docxFile);

    // Save the Word document as HTML
    $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);
    $htmlFilePath = 'temp.html'; // Path to the temporary HTML file
    $htmlWriter->save($htmlFilePath);

    // Output the HTML content
    readfile($htmlFilePath);

    // Clean up temporary files
    unlink($htmlFilePath);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

?>
